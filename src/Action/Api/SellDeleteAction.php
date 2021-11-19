<?php

namespace App\Action\Api;

use App\Domain\Sell\Service\SellFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Chronos\Chronos;

use function DI\string;

/**
 * Action.
 */
final class SellDeleteAction
{
    private $responder;
    private $updater;
    private $finder;
    private $findproduct;
    private $findcpo_item;


    public function __construct(Responder $responder,  SellUpdater $updater, SellFinder $finder, ProductFinder $findproduct, CpoItemFinder $findcpo_item)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->findproduct = $findproduct;
        $this->findcpo_item = $findcpo_item;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $data = (array)$request->getParsedBody();
        $sellID = $data['sell_id'];
        $user_id = $data['user_id'];

        $data['is_delete']="Y";
        $this->updater->updateSellApi($sellID, $data, $user_id);

        return $this->responder->withJson($response, $data);
    }
}
