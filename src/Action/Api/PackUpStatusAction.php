<?php

namespace App\Action\Api;

use App\Domain\Pack\Service\PackFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\Pack\Service\PackUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Chronos\Chronos;

use function DI\string;

/**
 * Action.
 */
final class PackUpStatusAction
{
    private $responder;
    private $updater;
    private $finder;
    private $findproduct;
    private $findcpo_item;


    public function __construct(
        Responder $responder,  
        PackUpdater $updater, 
        PackFinder $finder, 
        ProductFinder $findproduct, 
        CpoItemFinder $findcpo_item)
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
        $pack_id=$data['pack_id'];

        if (isset($data['start_date'])) {
            $data['startDate'] =$data['start_date'];
            $data['endDate'] = $data['end_date'];
        }

        $data['up_status'] = "SELECTED_CPO";
        $this->updater->updatePackStatus($pack_id, $data, $user_id);
        $dataAll=[''];

        $rtdata['message']="Get Lot Defect Successful";
        $rtdata['error']=false;
        $rtdata['packs']=$this->finder->findPacks($dataAll);
        
        return $this->responder->withJson($response, $rtdata);
    }
}
