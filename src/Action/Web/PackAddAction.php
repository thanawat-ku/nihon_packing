<?php

namespace App\Action\Web;


use App\Domain\Pack\Service\PackFinder;
use App\Domain\TempQuery\Service\TempQueryFinder;
use App\Domain\Pack\Service\PackUpdater;
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
final class PackAddAction
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
        PackFinder $finder,
        PackUpdater $updater,
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
        $checkPack = false;

        $rtPackCheck = $this->finder->findPacks($data);
        for ($i = 0; $i < count($rtPackCheck); $i++) {
            if ($rtPackCheck[$i]['pack_status'] == "CREATED" || $rtPackCheck[$i]['pack_status'] == "SELECTING_CPO" || $rtPackCheck[$i]['pack_status'] == "SELECTED_CPO" || $rtPackCheck[$i]['pack_status'] == "SELECTING_LABEL") {
                $checkPack = true;
            }
        }

        if ($checkPack == false) {
            $id = $this->updater->insertPack($data);

            $rtPack['pack_id'] = $id;

            $packRow = $this->finder->findPackRow($id);


            $param_search['pack_id'] = $id;

            $viewData = [
                'pack_id' => $packRow['id'],
                'product_id' => $packRow['product_id'],

            ];
            return $this->responder->withRedirect($response, "cpo_item_check_temp_query", $viewData);

        } else {
            if (!isset($data['startDate'])) {
                $data['startDate'] = date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))));
                $data['endDate'] = date('Y-m-d');
            }


            $viewData = [
                'products' => $this->findProcut->findProducts($data),
                'packs' => $this->finder->findPacks($data),
                'user_login' => $this->session->get('user'),
                'startDate' => $data['startDate'],
                'endDate' => $data['endDate'],
                'checkError' => "true",
            ];

            return $this->responder->withRedirect($response, "packs", $viewData);
        }
    }
}
