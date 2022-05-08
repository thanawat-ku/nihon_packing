<?php

namespace App\Action\Api;

use App\Domain\Tag\Service\TagFinder;
use App\Domain\Tag\Service\TagUpdater;
use App\Domain\Pack\Service\PackUpdater;
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
    private $updatePack;

    public function __construct(Responder $responder,  TagUpdater $updater, TagFinder $finder, PackUpdater $updatePack)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->updatePack = $updatePack;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $data = (array)$request->getParsedBody();

        $searchTag['tag_no'] = $data['tag_no'];
        $rtTags = $this->finder->findTags($searchTag);

        if ($rtTags[0]['pack_id'] == $data['pack_id']) {

            $PackID['pack_id'] = $rtTags[0]['pack_id'];

            $rtTags = $this->finder->findTags($PackID);

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
