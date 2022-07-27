<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\LotNonFullyPack\Service\LotNonFullyPackUpdater;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

use function DI\string;

/**
 * Action.
 */
final class LotNonFullyPackAddAction
{
    private $responder;
    private $lNFPupdater;
    private $labelFinder;
    private $updatePack;
    private $labelUpdater;
    private $packFinder;
    private $session;

    public function __construct(
        LabelFinder $labelFinder,
        LotNonFullyPackUpdater $lNFPupdater,
        Session $session,
        Responder $responder,
        LabelUpdater $labelUpdater
    ) {

        $this->labelFinder = $labelFinder;
        $this->lNFPupdater = $lNFPupdater;
        $this->labelUpdater = $labelUpdater;
        $this->responder = $responder;
        $this->session = $session;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
       
        //ค้นหาข้อมูลของ label ที่ได้มาจาก id
        $searchLabelForID['label_id']=$data['id'];
        $labelRow = $this->labelFinder->findLabelSingleTable($searchLabelForID);

        //เพิ่มข้อมูลใน table lot_non_fully_packs
        $insertData['lot_id']=$data['lot_id'];
        $insertData['label_id']=$labelRow[0]['id'];
        $insertData['date']=date("Y-m-d");
        $insertData['is_register']='N';
        $this->lNFPupdater->insertLotNonFullyPack($insertData);

        //update status label is MERGED
        $labelID=$data['id'];
        $data['up_status'] = true;
        $updateLabel['status']='MERGED';
        $user_id = $this->session->get('user')["id"];
        $this->labelUpdater->updateLabelStatus($labelID, $updateLabel, $user_id);

        $viewData = [
            'lot_id' => $data['lot_id'],
            'ProductID'=>$data['ProductID'],
            'user_login' => $this->session->get('user'),
        ];

        return $this->responder->withRedirect($response, "lot_non_fully_pack_details", $viewData);
    }
}
