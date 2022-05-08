<?php

namespace App\Action\Api;

use App\Domain\Pack\Service\PackFinder;
use App\Domain\Pack\Service\PackUpdater;
use App\Domain\Packing\Service\PackingFinder;
use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\CpoItem\Service\CpoItemUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class PackCompleteAction
{
    private $responder;
    private $finder;
    private $updater;
    private $findPacking;
    private $findCpoItem;
    private $updateCpoItem;

    public function __construct(Responder $responder,PackFinder $finder,  PackUpdater $updater, PackingFinder $findPacking,CpoItemFinder $findCpoItem, CpoItemUpdater $updateCpoItem)
    {
        $this->responder = $responder;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->findPacking = $findPacking;
        $this->findCpoItem = $findCpoItem;
        $this->updateCpoItem = $updateCpoItem;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $data = (array)$request->getParsedBody();
        $sellID = $data['pack_id'];
        $user_id = $data['user_id'];

        $data['pack_status'] = 'COMPLETE';
        $this->updater->updatePackStatus($sellID, $data, $user_id);

        return $this->responder->withJson($response, $data);
    }
}
