<?php

namespace App\Action\Api;

use App\Domain\Pack\Service\PackFinder;
use App\Domain\Pack\Service\PackUpdater;
use App\Domain\Invoice\Service\InvoiceFinder;
use App\Domain\Invoice\Service\InvoiceUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class RegisterTagOnPackAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $findPack;
    private $finder;
    private $updatePack;
    private $updateInvoice;


    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(
        PackFinder $findPack,
        PackUpdater $updatePack,
        InvoiceFinder $finder,
        Responder $responder,
        InvoiceUpdater $updateInvoice
    ) {

        $this->finder = $finder;
        $this->updatePack = $updatePack;
        $this->findPack = $findPack;
        $this->responder = $responder;
        $this->updateInvoice = $updateInvoice;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $params = (array)$request->getQueryParams();
        $user_id = $params['user_id'];
        $invoiceNo = $params['invoice_no'];

        $arrPack = [];

        if (isset($params['start_date'])) {
            $params['startDate'] = $params['start_date'];
            $params['endDate'] = $params['end_date'];
        }

        if ($invoiceNo != "") {

            $params['InvoiceNo'] = $invoiceNo;
            $rtIvoiceNsp = $this->finder->findInvoice($params);

            if (isset($rtIvoiceNsp[0])) {
                //เช็คใน invoices  ว่ามี invoice no ตัวนี้หรือไม่
                for ($i = 0; $i < count($rtIvoiceNsp); $i++) {

                    //หา invoice no จาก invoice
                    $findInvoicePackings['invoice_no'] = $invoiceNo;
                    $findInvoicePackings['invoice_status'] = "INVOICED";
                    $rtIvoicePack = $this->finder->findInvoices($findInvoicePackings);

                    //หา packing id จาก pack
                    $findPackingID['packing_id'] = $rtIvoiceNsp[$i]['PackingID'];
                    $rtPack = $this->findPack->findPacks($findPackingID);

                    //ถ้าไม่มีข้อมูลเลย ให้สร้าง invoice ขึ้นมา ถ้ามีให้ทำการ update ข้อมูล
                    if (!isset($rtIvoicePack[0])) {
                        $findInvoice['sync'] = true;
                        $findInvoice['packing_id'] = $rtIvoiceNsp[$i]['PackingID'];
                        $rtIvoice = $this->finder->findInvoice($findInvoice);

                        $insertInvoice['invoice_no'] = $invoiceNo;
                        $insertInvoice['customer_id'] = $rtIvoice[0]['CustomerID'];
                        //แยกวันเเละเวลาออกจากกัน
                        $timestamp = $rtIvoice[0]['IssueDate'];
                        $splitTimeStamp = explode(" ", $timestamp);
                        $date = $splitTimeStamp[0];
                        $insertInvoice['date'] = $date;
                        $insertInvoice['invoice_status'] = "INVOICED";
                        $invoiceID = $this->updateInvoice->insertInvoicePacking($insertInvoice, $user_id);

                        if (isset($rtPack[0])) {
                            if ($rtPack[0]['pack_status'] == 'TAGGED') {
                                $upPack['pack_status'] = 'INVOICED';
                                $upPack['invoice_id'] = $invoiceID;
                                $this->updatePack->updatePackSyncApi((int)$rtPack[0]['id'],  $upPack, $user_id);
                            }
                        }
                    } else {
                        if (isset($rtPack[0])) {
                            if ($rtPack[0]['pack_status'] == 'TAGGED') {
                                $upPack['pack_status'] = 'INVOICED';
                                $upPack['invoice_id'] = $rtIvoicePack[0]['id'];
                                $this->updatePack->updatePackSyncApi((int)$rtPack[0]['id'],  $upPack, $user_id);
                            }
                        }
                    }
                }

                if (isset($rtIvoicePack[0])) {
                    $searchInvoice['invoice_id'] = $rtIvoicePack[0]['id'];
                } else {
                    $searchInvoice['invoice_id'] = $invoiceID;
                }

                $rtdata['packs'] = $this->finder->findInvoicePackings($searchInvoice);
                $rtdata['message'] = "Get Pack Successful";
                $rtdata['error'] = false;
                return $this->responder->withJson($response, $rtdata);
            } else {
                $rtdata['message'] = "Get Pack Successful";
                $rtdata['error'] = false;
                return $this->responder->withJson($response, $rtdata);
            }
        } else {
            // $rtdata['packs'] = $this->findPack->findPacks($packID);
            $rtdata['message'] = "Get Pack Successful";
            $rtdata['error'] = false;

            return $this->responder->withJson($response, $rtdata);
        }
    }
}
