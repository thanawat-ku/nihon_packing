<?php

namespace App\Action\Web;

use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\TempQuery\Service\TempQueryFinder;
use App\Domain\Sell\Service\SellFinder;
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
    private $sellFinder;
    private $session;
    private $tempQueryFinder;

    public function __construct(
        Twig $twig,
        CpoItemFinder $finder,
        SellFinder $sellFinder,
        Session $session,
        Responder $responder,
        TempQueryFinder $tempQueryFinder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->sellFinder = $sellFinder;
        $this->session = $session;
        $this->responder = $responder;
        $this->tempQueryFinder = $tempQueryFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        $sell_id = (int)$params['sell_id'];

        $uuid = uniqid();
        $param_search['uuid'] = $uuid;
        $param_search['sell_id'] = $sell_id;

        $sellRow = $this->sellFinder->findSellRow($sell_id);



        $arrTemQuery = $this->tempQueryFinder->findTempQuery($param_search);
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
            'sellRow' => $sellRow,
            'CpoItemSelects' =>  CheckCpoItemSelect($arrTemQuery, $arrCpoItem),
            'user_login' => $this->session->get('user'),
        ];

        // return $this->responder->withRedirect($response, "cpo_items",$viewData);

        return $this->twig->render($response, 'web/cpoItemSelects.twig', $viewData);
    }
}
