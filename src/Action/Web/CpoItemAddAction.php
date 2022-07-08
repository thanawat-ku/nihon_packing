<?php

namespace App\Action\Web;

use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\TempQuery\Service\TempQueryFinder;
use App\Domain\Pack\Service\PackFinder;
use App\Domain\PackCpoItem\Service\PackCpoItemUpdater;
use App\Domain\Pack\Service\PackUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class CpoItemAddAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $packCpoItemUpdater;
    private $packFinder;
    private $packUpdater;
    private $session;
    private $tempQueryFinder;

    public function __construct(
        Twig $twig,
        CpoItemFinder $finder,
        PackCpoItemUpdater $packCpoItemUpdater,
        Session $session,
        Responder $responder,
        PackUpdater  $packUpdater,
        PackFinder $packFinder,
        TempQueryFinder $tempQueryFinder,
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->tempQueryFinder = $tempQueryFinder;
        $this->packFinder = $packFinder;
        $this->packFinder = $packFinder;
        $this->packUpdater = $packUpdater;
        $this->packCpoItemUpdater = $packCpoItemUpdater;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $pack_id = (int)$data['pack_id'];
        $user_id=$this->session->get('user')["id"];

        $rtCpoItem = $this->finder->findCpoItem($data);

        $dataCpoItem['PackingQty'] = $rtCpoItem[0]['PackingQty'] + $data['pack_qty'];


        $uuid = uniqid();
        $param_search['uuid'] = $uuid;
        $param_search['pack_id'] = $pack_id;

        $this->packCpoItemUpdater->insertPackCpoItem($data);

        $flash = $this->session->getFlashBag();
        $flash->clear();

        $totalQty = 0;

        $packCpoItem = $this->tempQueryFinder->findTempQuery($param_search);



        for ($i = 0; $i < count($packCpoItem); $i++) {
            $totalQty += (int)$packCpoItem[$i]['pack_qty'];
            $arrTotalQty['total_qty'] = $totalQty;
            $arrTotalQty['po_no'] = $packCpoItem[$i]['po_no'];
        }


        $this->packUpdater->updatePackStatusSelectingCpo($pack_id,  $arrTotalQty);

        $packRow = $this->packFinder->findPackRow($pack_id);
        $data1['ProductID'] = $packRow['product_id'];

        $viewData = [
            'pack_id' => $packRow['id'],
            'product_id' => $packRow['product_id'],
            'search_product_id' => $data['search_product_id'],
            'search_pack_status' => $data['search_pack_status'],
        ];

        return $this->responder->withRedirect($response, "cpo_items", $viewData);
    }
}
