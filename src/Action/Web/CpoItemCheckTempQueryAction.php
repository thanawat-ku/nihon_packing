<?php

namespace App\Action\Web;

use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Domain\Pack\Service\PackFinder;
use App\Domain\TempQuery\Service\TempQueryFinder;
use App\Domain\TempQuery\Service\TempQueryUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class CpoItemCheckTempQueryAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $tempQueryFinder;
    private $packFinder;
    private $tempQueryUpdater;
    private $session;

    public function __construct(
        Twig $twig,
        CpoItemFinder $finder,
        TempQueryFinder $tempQueryFinder,
        Session $session,
        Responder $responder,
        TempQueryUpdater  $tempQueryUpdater,
        PackFinder $packFinder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->tempQueryFinder = $tempQueryFinder;
        $this->packFinder = $packFinder;
        $this->tempQueryUpdater = $tempQueryUpdater;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getQueryParams();
        $packID = (int)$data['pack_id'];

        // $cpodata = $this->finder->findCpoItem($data);
        // $uuid = uniqid();

        // $pack = null;

        // $cpoitemcheck = $this->tempQueryFinder->findTempQueryCheck($data);

        // if (!$cpoitemcheck) {
        //     foreach ($cpodata as $cpo) {
        //         $param_cpo['uuid'] = $uuid;
        //         $param_cpo['cpo_item_id'] = $cpo['CpoItemID'];
        //         $param_cpo['po_no'] = $cpo['PONo'];
        //         $param_cpo['product_id'] = $cpo['ProductID'];
        //         $param_cpo['quantity'] = $cpo['Quantity'];
        //         $param_cpo['packing_qty'] = $cpo['PackingQty'];
        //         $param_cpo['due_date'] = $cpo['DueDate'];
        //         $this->tempQueryUpdater->insertTempQuery($param_cpo);

        //     }
        // } else {
        //     $productID['product_id'] = $data['product_id'];
        //     $cpoitemcheck = $this->tempQueryFinder->findTempQueryCheckUpdate($productID);
        //     for ($i = 0; $i < count($cpoitemcheck); $i++) {
        //         $param_cpo['cpo_item_id'] = $cpoitemcheck[$i]['CpoItemID'];
        //         $param_cpo['packing_qty'] = $$cpoitemcheck[$i]['PackingQty'];
        //         $param_cpo['due_date'] = $$cpoitemcheck[$i]['DueDate'];
        //         $this->tempQueryUpdater->updateTempquery((int)$cpoitemcheck[$i]['cpo_item_id'], $param_cpo);
        //     }
        // }


        $packRow = $this->packFinder->findPackRow($packID);

        $param_search['uuid'] = $uuid;
        $param_search['pack_id'] = $packID;

        $viewData = [
            'pack_id' => $packRow['id'],
            'product_id' => $packRow['product_id'],
            'search_product_id' => $data['search_product_id'],
            'search_pack_status' => $data['search_pack_status'],
        ];

        return $this->responder->withRedirect($response, "cpo_items", $viewData);
    }
}
