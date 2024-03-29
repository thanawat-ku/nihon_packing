<?php

namespace App\Action\Api;

use App\Domain\Invoice\Service\InvoiceFinder;
use App\Domain\Invoice\Service\InvoiceUpdater;
use App\Domain\Pack\Service\PackFinder;
use App\Domain\Pack\Service\PackUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class SyncInvoiceNoAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updateInvoice;
    private $findPack;
    private $updatePack;
    private $session;


    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(
        InvoiceFinder $finder,
        InvoiceUpdater $updateInvoice,
        PackFinder $findPack,
        PackUpdater $updatePack,
        Responder $responder,
        Session $session
    ) {
        $this->finder = $finder;
        $this->updateInvoice = $updateInvoice;
        $this->findPack = $findPack;
        $this->updatePack = $updatePack;
        $this->responder = $responder;
        $this->session = $session;
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
        if (!isset($params['sync'])) {
            $params['sync'] = "true";
            if (isset($this->session->get('user')["id"])) {
                $params['user_id'] = $this->session->get('user')["id"];
                $user_id = $params['user_id'];
            }
        } else {
            $user_id = $params['user_id'];
        }

        $rtPack = $this->findPack->findPacks($params);
        $rtData = [];

        if (isset($rtPack[0])) {
            for ($i = 0; $i < count($rtPack); $i++) {
                $params['packing_id'] = $rtPack[$i]['packing_id'];
                $rtIvoice = $this->finder->findInvoice($params);

                if (isset($rtIvoice[0])) {
                    // ค้นหา ข้อมูล ที่มี invoice_no และ invoice_status ตรงกัน
                    $findInvoicePackings['invoice_no'] = $rtIvoice[0]['InvoiceNo'];
                    $findInvoicePackings['invoice_status'] = "INVOICED";
                    $rtIvoicePack = $this->finder->findInvoicePackings($findInvoicePackings);

                    //เมื่อเช็คว่า ข้อมูลใน invoices ไม่มีให้ทำการสร้าง invoices ขึ้นมา ถ้ามีก็ให้ update ข้อมูลลงใน packs
                    if (!isset($rtIvoicePack[0])) {
                        $insertInvoice['invoice_no'] = $rtIvoice[0]['InvoiceNo'];
                        $insertInvoice['customer_id'] = $rtIvoice[0]['CustomerID'];
                        //แยกวันเเละเวลาออกจากกัน
                        $timestamp = $rtIvoice[0]['IssueDate'];
                        $splitTimeStamp = explode(" ", $timestamp);
                        $date = $splitTimeStamp[0];
                        $insertInvoice['date'] = $date;
                        $insertInvoice['invoice_status'] = "INVOICED";
                        $invoiceID = $this->updateInvoice->insertInvoicePacking($insertInvoice, $user_id);

                        if ($rtPack[$i]['is_completed'] == 'Y') {
                            $upPack['pack_status'] = 'INVOICED';
                            $upPack['invoice_id'] = $invoiceID;
                            $this->updatePack->updatePackSyncApi((int)$rtPack[$i]['id'],  $upPack, $user_id);
                        }

                        $rtData = [];
                        array_push($rtData, $insertInvoice);
                    } else {
                        if ($rtPack[$i]['is_completed'] == 'Y') {
                            $upPack['pack_status'] = 'INVOICED';
                            $upPack['invoice_id'] = $rtIvoicePack[0]['id'];
                            $this->updatePack->updatePackSyncApi((int)$rtPack[$i]['id'],  $upPack, $user_id);
                        }

                        $rtData = [];
                        array_push($rtData, $rtIvoicePack);
                    }
                }
            }
        }


        //หา pack ที่มี status เป็น TAGGED
        $checkProductCom['check_status_pack'] = true;
        $rtPackProductCom = $this->findPack->findPacks($checkProductCom);

        if (isset($rtPackProductCom[0])) {
            //เมื่อได้ pack ที่มี status เป็น TAGGED แล้ว เช็คว่า is_completed ของ product เป็น N or Y
            for ($j = 0; $j < count($rtPackProductCom); $j++) {
                //หา data invoice ที่อยู่ใน จาก packing id ที่อยู่ใน db packing
                $params['packing_id'] = $rtPackProductCom[$j]['packing_id'];
                $rtIvoice = $this->finder->findInvoice($params);

                if (isset($rtIvoice[0])) {
                    // ค้นหา ข้อมูล ที่มี invoice_no และ invoice_status ตรงกัน
                    $findInvoicePackings['invoice_no'] = $rtIvoice[0]['InvoiceNo'];
                    $findInvoicePackings['invoice_status'] = "INVOICED";
                    $rtIvoicePack = $this->finder->findInvoicePackings($findInvoicePackings);

                    //เมื่อเช็คว่า ข้อมูลใน invoices ไม่มีให้ทำการสร้าง invoices ขึ้นมา ถ้ามีก็ให้ update ข้อมูลลงใน packs
                    if (!isset($rtIvoicePack[0])) {
                        $insertInvoice['invoice_no'] = $rtIvoice[0]['InvoiceNo'];
                        $insertInvoice['customer_id'] = $rtIvoice[0]['CustomerID'];
                        //แยกวันเเละเวลาออกจากกัน
                        $timestamp = $rtIvoice[0]['IssueDate'];
                        $splitTimeStamp = explode(" ", $timestamp);
                        $date = $splitTimeStamp[0];
                        $insertInvoice['date'] = $date;
                        $invoiceID = $this->updateInvoice->insertInvoicePacking($insertInvoice, $user_id);

                        if ($rtPackProductCom[$j]['is_completed'] == 'N') {
                            $upPack['invoice_id'] = $invoiceID;
                            $this->updatePack->updatePackSyncApi((int)$rtPackProductCom[$j]['id'],  $upPack, $user_id);
                        }

                    } else {
                        if ($rtPackProductCom[$j]['is_completed'] == 'N') {
                            $upPack['invoice_id'] = $rtIvoicePack[0]['id'];
                            $this->updatePack->updatePackSyncApi((int)$rtPackProductCom[$j]['id'],  $upPack, $user_id);
                        }
                        
                    }
                }

                //update pack status ที่มีสถานะเป็น TAGGED
                if ($rtPackProductCom[$j]['pack_status'] == 'TAGGED') {
                    //เป็น  N  update status is INVOICED
                    $upPackCheckProduct['pack_status'] = 'COMPLETED';
                    $this->updatePack->updatePackSyncApi((int)$rtPackProductCom[$j]['id'],  $upPackCheckProduct, $user_id);
                }
            }
        }

        return $this->responder->withJson($response, $rtData);
    }
}
