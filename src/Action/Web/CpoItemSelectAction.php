<?php

namespace App\Action\Web;

use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\Pack\Service\PackFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class CpoItemSelectAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $packFinder;
    private $session;
    private $tempQueryFinder;

    public function __construct(
        Twig $twig,
        CpoItemFinder $finder,
        PackFinder $packFinder,
        Session $session,
        Responder $responder,
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->packFinder = $packFinder;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        $pack_id = (int)$params['pack_id'];

        //ตัดช่องว่างหลัง string ออก
        $params['search_product_id'] = str_replace(' ', '', $params['search_product_id']);

        $uuid = uniqid();
        $param_search['uuid'] = $uuid;
        $param_search['pack_id'] = $pack_id;

        if (!isset($params['startDate'])) {
            $dt = date('Y-m-d');
            $time  = strtotime($dt);
            $month = date('m', $time);
            $year  = date('Y', $time);
            $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            $params['startDate'] = date('Y-'.$month.'-01');
            $params['endDate'] = date('Y-'.$month.'-'.$day);
        }

        $packRow = $this->packFinder->findPackRow($pack_id);

        $arrCpoItem = $this->finder->findCpoItemSelect($params);

        function CheckCpoItemSelect(array $arrTemQuery, array $arrCpoItem)
        {
            $arrCpoItemSelect = [];
            if ($arrTemQuery) {

                for ($i = 0; $i < count($arrCpoItem); $i++) {
                    $checkCpo = true;
                    for ($j = 0; $j < count($arrTemQuery); $j++) {
                        if ($arrCpoItem[$i]['CpoItemID'] == $arrTemQuery[$j]['cpo_item_id']) {
                            $checkCpo = false;
                        }
                    }
                    if ($checkCpo == true) {
                        array_push($arrCpoItemSelect, $arrCpoItem[$i]);
                    }
                }
            } else {
                $arrCpoItemSelect = $arrCpoItem;
            }
            if (!$arrCpoItemSelect) {
                $arrCpoItemSelect = [];
            }
            return $arrCpoItemSelect;
        }


        $viewData = [
            'packRow' => $packRow,
            'CpoItemSelects' =>  $arrCpoItem,
            'user_login' => $this->session->get('user'),
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
            'search_product_id' => $params['search_product_id'],
            'search_pack_status' => $params['search_pack_status'],
        ];

        

        return $this->twig->render($response, 'web/cpoItemSelects.twig', $viewData);
    }
}
