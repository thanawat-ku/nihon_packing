<?php

namespace App\Action\Web;

use App\Domain\Tag\Service\TagFinder;
use App\Domain\Pack\Service\PackFinder;
use App\Domain\Customer\Service\CustomerFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class TagAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $tagFinder;
    private $packFinder;
    private $customerFinder;
    private $session;

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(Twig $twig, TagFinder $tagFinder, PackFinder $packFinder, CustomerFinder $customerFinder, Session $session, Responder $responder)
    {
        $this->twig = $twig;
        $this->tagFinder = $tagFinder;
        $this->packFinder = $packFinder;
        $this->customerFinder = $customerFinder;
        $this->session = $session;
        $this->responder = $responder;
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
        if (isset($params['pack_id'])) {
            $packID = $params['pack_id'];

            //เช็คว่า มีตัวแปร viewpack หรือไม่ ถ้าไม่ ให้ทำ
            if (!isset($params['view_pack'])) {
                

                $checkTagPrinted = "true";

                $packRow = $this->packFinder->findPackRow($packID);

                $rtTags = $this->tagFinder->findTags($params);
                for ($i = 0; $i < count($rtTags); $i++) {
                    if ($rtTags[$i]['status'] != "PRINTED") {
                        $checkTagPrinted = "false";
                    }
                }

                
                $packRow['cpo_item_id'] = $rtTags[0]['cpo_item_id'];
                $packRow['total_qty'] = $rtTags[0]['total_qty'];

                $viewData = [
                    'checkTag' => "pack", //tag ที่ยังไม่ได้ register จากการ pack
                    'checkTagPrinted' => $checkTagPrinted,
                    'packRow' => $packRow,
                    'tags' => $rtTags,
                    'user_login' => $this->session->get('user'),
                    'search_product_id' => $params['search_product_id'],
                    'search_pack_status' => $params['search_pack_status'],
                ];
            }else{
                $packRow = $this->packFinder->findPackRow($packID);
                $rtTags = $this->tagFinder->findTags($params);
                $viewData = [
                    'checkTag' => "viewpack", //tag ที่ยังไม่ได้ register จากการ pack
                    'packRow' => $packRow,
                    'tags' => $rtTags,
                    'user_login' => $this->session->get('user'),
                ];
            }
        } else {
            $rtCustomer = $this->customerFinder->findCustomers($params);

            if (!$this->session->get('startDateTag')) {
                $params['startDate'] = date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))));
                $params['endDate'] = date('Y-m-d');

                //กำหนด session ให้กับ  startDatePack และ endDatePack
                $this->session->start();

                $this->session->set('startDateTag', $params['startDate']);
                $this->session->set('endDateTag', $params['endDate']);
            } else {
                //กำหนด session ให้กับ  startDateTag และ endDateTag
                if (isset($params['startDate'])) {
                    $this->session->start();

                    $this->session->set('startDateTag', $params['startDate']);
                    $this->session->set('endDateTag', $params['endDate']);
                } else {
                    $params['startDate'] = $this->session->get('startDateTag');
                    $params['endDate'] = $this->session->get('endDateTag');
                }
            }

            if (!isset($params['search_customer_id'])) {
                $params['search_customer_id'] = $rtCustomer[0]['id'];
            }
            if (!isset($params['search_tag_status'])) {
                $params['search_tag_status'] = 'ALL';
            }
            $rtTags = $this->tagFinder->findTagInvoices($params);
            $viewData = [
                'checkTag' => "all", //tag all status
                'customers' => $rtCustomer,
                'tags' => $rtTags,
                'search_customer_id' => $params['search_customer_id'],
                'search_tag_status' => $params['search_tag_status'],
                'user_login' => $this->session->get('user'),
                'startDate' => $this->session->get('startDateTag'),
                'endDate' => $this->session->get('endDateTag'),
            ];
        }

        return $this->twig->render($response, 'web/tags.twig', $viewData);
    }
}
