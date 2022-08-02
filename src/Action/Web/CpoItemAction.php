<?php

namespace App\Action\Web;

use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\Pack\Service\PackFinder;
use App\Domain\PackCpoItem\Service\PackCpoItemFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class CpoItemAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $tempQueryFinder;
    private $packFinder;
    private $packCpoItemFinder;
    private $session;

    public function __construct(
        Twig $twig,
        CpoItemFinder $finder,
        PackCpoItemFinder $packCpoItemFinder,
        Session $session,
        Responder $responder,
        PackFinder $packFinder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->packFinder = $packFinder;
        $this->packCpoItemFinder = $packCpoItemFinder;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getQueryParams();
        $packID = (int)$data['pack_id'];

        //ตัดช่องว่างหลัง product_id
        $data['search_product_id'] = str_replace(' ', '', $data['search_product_id']);

        $rtPackCpoITems =  $this->packCpoItemFinder->findPackCpoItems($data);

        // $searchCpoItem['ProductID'] = $data['product_id']; 
        // $rtCpoItems = $this->finder->findCpoItemSelect($searchCpoItem);

        $cpodata = $this->finder->findCpoItem($data);
        $uuid = uniqid();

        $pack = null;

        //เช็คว่ามีการสร้าง pack_cpo_item ขึ้นมารึยัง 
        $cpoitemcheck = $this->packCpoItemFinder->findPackCpoItems($data);
        if (!isset($cpoitemcheck[0])) {
            $checkPackCpo = "true";
        }else{
            $checkPackCpo = "false";
        }

        $packRow = $this->packFinder->findPackRow($packID);

        // $param_search['uuid']=$uuid;
        // $param_search['pack_id']=$packID;

        $viewData = [
            'checkPackCpo' => $checkPackCpo, 
            'packRow' => $packRow,
            'CpoItem' => $rtPackCpoITems,
            'user_login' => $this->session->get('user'),
            'search_product_id' => $data['search_product_id'],
            'search_pack_status' => $data['search_pack_status'],
        ];

        return $this->twig->render($response, 'web/cpoItem.twig', $viewData);
    }
}
