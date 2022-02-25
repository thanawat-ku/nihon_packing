<?php

namespace App\Action\Web;


use App\Domain\Sell\Service\SellFinder;
use App\Domain\TempQuery\Service\TempQueryFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use PHPUnit\Framework\Constraint\Count;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class SellAddAction
{
    private $twig;
    private $finder;
    private $tempQueryFinder;
    private $responder;
    private $updater;
    private $findProcut;
    private $session;

    public function __construct(
        Twig $twig,
        Responder $responder,
        SellFinder $finder,
        SellUpdater $updater,
        TempQueryFinder $tempQueryFinder,
        ProductFinder $findProcut,
        Session $session
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->tempQueryFinder = $tempQueryFinder;
        $this->findProcut = $findProcut;
        $this->session = $session;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $data['ProductID'] = $data['product_id'];
        $checkSell = false;

        $rtSellCheck = $this->finder->findSells($data);
        for ($i = 0; $i < count($rtSellCheck); $i++) {
            if ($rtSellCheck[$i]['sell_status'] == "CREATED" || $rtSellCheck[$i]['sell_status'] == "SELECTING_CPO" || $rtSellCheck[$i]['sell_status'] == "SELECTED_CPO" || $rtSellCheck[$i]['sell_status'] == "SELECTING_LABEL") {
                $checkSell = true;
            }
        }

        if ($checkSell == false) {
            $id = $this->updater->insertSell($data);

            $rtSell['sell_id'] = $id;

            $sellRow = $this->finder->findSellRow($id);


            $param_search['sell_id'] = $id;

            $viewData = [
                'sell_id' => $sellRow['id'],
                'product_id' => $sellRow['product_id'],

            ];
            return $this->responder->withRedirect($response, "cpo_item_check_temp_query", $viewData);

        } else {
            if (!isset($data['startDate'])) {
                $data['startDate'] = date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))));
                $data['endDate'] = date('Y-m-d');
            }


            $viewData = [
                'products' => $this->findProcut->findProducts($data),
                'sells' => $this->finder->findSells($data),
                'user_login' => $this->session->get('user'),
                'startDate' => $data['startDate'],
                'endDate' => $data['endDate'],
                'checkError' => "true",
            ];

            return $this->responder->withRedirect($response, "sells", $viewData);
        }
    }
}
