<?php

namespace App\Action\Api;

use App\Domain\Tag\Service\TagFinder;
use App\Domain\Tag\Service\TagUpdater;
use App\Domain\Sell\Service\SellUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Chronos\Chronos;

use function DI\string;

/**
 * Action.
 */
final class TagRegisterAction
{
    private $responder;
    private $updater;
    private $finder;
    private $updateSell;

    public function __construct(Responder $responder,  TagUpdater $updater, TagFinder $finder, SellUpdater $updateSell)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->updateSell = $updateSell;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $data = (array)$request->getParsedBody();
        $user_id = $data['user_id'];

        $rtTags = $this->finder->findTags($data);

        if ($rtTags) {

            $sellID = $rtTags[0]['sell_id'];

            $upStatus['up_status'] = "TAGGED";
            $this->updateSell->updateSellStatus($sellID, $upStatus, $user_id);

            $upStatus['status'] = "BOXED";
            $this->updater->updateTagAllFromSellIDApi($sellID, $upStatus,  $user_id);

            $rtdata['message'] = "Get Tag Defect Successful";
            $rtdata['error'] = false;
            $rtdata['tags'] = $this->finder->findTags($data);
        } else {
            $rtdata['message'] = "Get Tag Defect Successful";
            $rtdata['error'] = true;
            $rtdata['tag_no'] = $data['tag_no'];
        }



        return $this->responder->withJson($response, $rtdata);
    }
}