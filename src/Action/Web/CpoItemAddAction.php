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
    private $sellCpoItemUpdater;
    private $sellFinder;
    private $sellUpdater;
    private $session;
    private $tempQueryFinder;

    public function __construct(
        Twig $twig,
        CpoItemFinder $finder,
        PackCpoItemUpdater $sellCpoItemUpdater,
        Session $session,
        Responder $responder,
        PackUpdater  $sellUpdater,
        PackFinder $sellFinder,
        TempQueryFinder $tempQueryFinder,
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->tempQueryFinder = $tempQueryFinder;
        $this->sellFinder = $sellFinder;
        $this->sellFinder = $sellFinder;
        $this->sellUpdater = $sellUpdater;
        $this->sellCpoItemUpdater = $sellCpoItemUpdater;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $pack_id = (int)$data['pack_id'];

        $rtCpoItem = $this->finder->findCpoItem($data);

        $dataCpoItem['PackingQty'] = $rtCpoItem[0]['PackingQty'] + $data['pack_qty'];


        $uuid = uniqid();
        $param_search['uuid'] = $uuid;
        $param_search['pack_id'] = $pack_id;

        $this->sellCpoItemUpdater->insertPackCpoItem($data);

        $flash = $this->session->getFlashBag();
        $flash->clear();

        $totalQty = 0;

        $sellCpoItem = $this->tempQueryFinder->findTempQuery($param_search);


        for ($i = 0; $i < count($sellCpoItem); $i++) {
            $totalQty += (int)$sellCpoItem[$i]['pack_qty'];
            $arrTotalQty['total_qty'] = $totalQty;
        }


        $this->sellUpdater->updatePackStatusSelectingCpo($pack_id,  $arrTotalQty);

        $sellRow = $this->sellFinder->findPackRow($pack_id);
        $data1['ProductID'] = $sellRow['product_id'];

        $viewData = [
            'pack_id' => $sellRow['id'],
            'product_id' => $sellRow['product_id'],
        ];

        return $this->responder->withRedirect($response, "cpo_items", $viewData);
    }
}
