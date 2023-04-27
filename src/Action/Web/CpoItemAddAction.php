<?php

namespace App\Action\Web;

use App\Domain\CpoItem\Service\CpoItemFinder;
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
    private $twig;

    public function __construct(
        Twig $twig,
        CpoItemFinder $finder,
        PackCpoItemUpdater $packCpoItemUpdater,
        Session $session,
        Responder $responder,
        PackUpdater  $packUpdater,
        PackFinder $packFinder,
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
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

        $dueDate = explode(" ", $data['due_date']);
        $data['due_date']=$dueDate[0];
        
        $this->packCpoItemUpdater->insertPackCpoItem($data);

        $flash = $this->session->getFlashBag();
        $flash->clear();

        $totalQty = 0;
        

        //ค้นหา po_no ในฐานข้อมูล nsp
        $searchCpoItemNsp['CpoItemID'] = $data['cpo_item_id'];
        $rtPackCpoItem = $this->finder->findCpoItemSelect($searchCpoItemNsp);
        $arrTotalQty['po_no'] = $rtPackCpoItem[0]['PONo'];
        $arrTotalQty['total_qty'] = $data['pack_qty']; 


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
