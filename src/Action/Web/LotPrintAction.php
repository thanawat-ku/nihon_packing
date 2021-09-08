<?php

namespace App\Action\Web;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\Lot\Service\LotFinder;
use App\Domain\Lot\Service\LotUpdater;
use App\Domain\Label\Service\LabelUpdater;

/**
 * Action.
 */
final class LotPrintAction
{
    private $responder;
    private $updater;
    private $finder;
    private $labelUpdater;
    public function __construct(Responder $responder, LotUpdater $updater,
        LotFinder $finder,LabelUpdater $labelUpdater
    )
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->labelUpdater = $labelUpdater;
    }


    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        // Extract the form data from the request body
        $data = (array)$request->getParsedBody();
        $lotId = $data["id"];

        // generate labels
        $params["lot_id"]=$lotId;
        $lots = $this->finder->findLots($params);
        if($lots[0]['status']=="CREATED"){
            // Invoke the Domain with inputs and retain the result
            $this->updater->printLot($lotId);

            $quantity=$lots[0]["quantity"];
            $std_pack=$lots[0]["std_pack"];
            $num_packs=ceil($quantity/$std_pack);


            
            for($i=0; $i < $num_packs; $i++){
                $data1['lot_id']=$lots[0]['id'];
                $data1['label_no']=$lots[0]['lot_no'].str_pad( $i, 3, "0", STR_PAD_LEFT);
                $data1['label_type']="FULLY";
                $data1['quantity']=$std_pack;
                $data1['status']="CREATED";
                $this->labelUpdater->insertLabel($data1);
            }
            $data1['lot_id']=$lots[0]['id'];
            $data1['label_no']=$lots[0]['lot_no'].str_pad( $i, 3, "0", STR_PAD_LEFT);
            $data1['label_type']="NONFULLY";
            $data1['quantity']=0;
            $data1['status']="CREATED";
            $this->labelUpdater->insertLabel($data1);
        }
        

        // Build the HTTP response
        return $this->responder->withRedirect($response,"lots");
    }
}
