<?php

namespace App\Action\Web;

use App\Domain\Tag\Service\TagFinder;
use App\Domain\Tag\Service\TagUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class PackReprintAction
{
    private $responder;
    private $updater;
    private $finder;

    public function __construct(Responder $responder, TagUpdater $updater, TagFinder $finder)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $packId = $data["pack_id"];
        $printerId = $data["printer_id"];
        $params['pack_id']=$packId;

        $tags = $this->finder->findTags($params);
        for ($i = 0; $i < sizeof($tags); $i++) {
            $tagId = $tags[$i]['id'];
            $data['wait_print'] = "Y";
            $data['printer_id'] = $printerId;
            $this->updater->updateTag($tagId, $data);
        }

        return $this->responder->withRedirect($response, "packs");
    }
}
