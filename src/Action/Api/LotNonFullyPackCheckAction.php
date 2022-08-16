<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\LotNonFullyPack\Service\LotNonFullyPackFinder;
use App\Domain\LotNonFullyPack\Service\LotNonFullyPackUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LotNonFullyPackCheckAction
{
    private $responder;
    private $labelFinder;
    private $labelUpdater;
    private $lnfpFinder;
    private $lnfpUpdater;

    public function __construct(
        Responder $responder,
        LabelFinder $labelFinder,
        LabelUpdater $labelUpdater,
        LotNonFullyPackFinder $lnfpFinder,
        LotNonFullyPackUpdater $lnfpUpdater,

    ) {
        $this->responder = $responder;
        $this->labelFinder = $labelFinder;
        $this->labelUpdater = $labelUpdater;
        $this->lnfpFinder = $lnfpFinder;
        $this->lnfpUpdater = $lnfpUpdater;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $data = (array)$request->getParsedBody();
        $labels = $data['labels'];
        $lotID = $data['lot_id'];
        $user_id = $data['user_id'];
        $labelExp = explode("#", $labels);

        //ลบ ข้อมูลที่อยู่ใน lot non fully pack ออกให้หมด เพื่อเคลียพื้นที่ว่างให้ label ใหม่ที่จะเข้ามา
        $this->lnfpUpdater->deleteLotNonFullyPackAll($lotID);

        $rtLabel = [];
        $mergeQty = 0;
        foreach ($labelExp as $label) {
            if (strlen($label) == 12) {
                $searchLabel['label_no'] = $label;
                $labelRow = $this->labelFinder->findLabelSingleTable($searchLabel);

                if (($labelRow[0]['status'] == 'PACKED' || $labelRow[0]['status'] == 'MERGED') && $labelRow[0]['label_type'] == "NONFULLY") {

                    //เปลี่ยนสถานะเป็น merge เมื่อที่การ merge
                    $upStatus['status'] = 'MERGED';
                    $this->labelUpdater->updateLabelStatus(intval($labelRow[0]['id']), $upStatus, $user_id);

                    //ค้นหาข้อมูลที่มี label_id ซ้ำกัน
                    $seachData['label_id'] = $labelRow[0]['id'];
                    $checkDupli = $this->lnfpFinder->checkLabelInLotNonFullyPacks($seachData);

                    //เช็คความถูกต้อง เเละส่งข้อมูลไปยัง app
                    // ถ้าไม่มีข้อมูลที่ซ้ำกัน ให้ข้อมูลถูกต้อง
                    if (!isset($checkDupli[0])) {
                        $labelRow[0]['check_correct'] = true;
                    } else {
                        $labelRow[0]['check_correct'] = false;
                    }
                } else {
                    $labelRow[0]['check_correct'] = false;
                }
                array_push($rtLabel, $labelRow[0]);

                //insert data ลงไปใน lot non fully pack
                $insertData['lot_id'] = $lotID;
                $insertData['label_id'] = $labelRow[0]['id'];
                $insertData['date'] = date("Y-m-d");
                $insertData['is_register'] = 'N';
                $this->lnfpUpdater->insertLotNonFullyPackApi($insertData, $user_id);
            }
        }

        $rtdata['message'] = "Get Lot Non Fully Pack Successful";
        $rtdata['error'] = false;
        $rtdata['lot_non_fully_packs'] = $rtLabel;

        return $this->responder->withJson($response, $rtdata);
    }
}
