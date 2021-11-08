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
final class SellUpStatusAction
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
        $this->finder=$finder;
        $this->findproduct=$findproduct;
        $this->findcpo_item=$findcpo_item;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
       
        $data = (array)$request->getParsedBody();

        $user_id=$data['user_id'];
        $sell_id=$data['sell_id'];

        if (isset($params['start_date'])) {
            $params['startDate'] = $params['start_date'];
            $params['endDate'] = $params['end_date'];
        }

        $data['up_status'] = "SELECTED_CPO";
        $this->updater->updateSellStatus($sell_id, $data, $user_id);
        $dataAll=[''];

        $rtdata['message']="Get Lot Defect Successful";
        $rtdata['error']=false;
        $rtdata['sells']=$this->finder->findSells($dataAll);
        
        return $this->responder->withJson($response, $rtdata);
    }
}
