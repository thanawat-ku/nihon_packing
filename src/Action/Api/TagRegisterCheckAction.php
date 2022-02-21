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
final class TagRegisterCheckAction
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

            $SellID['sell_id'] = $rtTags[0]['sell_id'];

            $rtTags = $this->finder->findTags($SellID);

            $checkTagPrinted = true;
            for ($i = 0; $i < count($rtTags); $i++) {
                if ($rtTags[$i]['status'] != "PRINTED") {
                    $checkTagPrinted = false;
                }
            }

            if ($checkTagPrinted == true) {
                $rtdata['message'] = "Get Tag Successful";
                $rtdata['error'] = false;
                $rtdata['tags'] = $this->finder->findTags($data);
            } else {
                $rtdata['message'] = "Get Tag Successful";
                $rtdata['error'] = true;
                $rtdata['tag_no'] = $data['tag_no'];
            }
        } else {
            $rtdata['message'] = "Get Tag Successful";
            $rtdata['error'] = true;
            $rtdata['tag_no'] = $data['tag_no'];
        }

        return $this->responder->withJson($response, $rtdata);
    }
}
