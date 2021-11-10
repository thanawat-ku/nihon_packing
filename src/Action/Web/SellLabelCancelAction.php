<?php

namespace App\Action\Web;

use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\TempQuery\Service\TempQueryFinder;
use App\Domain\Sell\Service\SellFinder;
use App\Domain\SellLabel\Service\SellLabelUpdater;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\Sell\Service\SellUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

use function DI\string;

/**
 * Action.
 */
final class SellLabelCancelAction
{
    private $responder;
    private $updater;
    private $labelUpdater;
    private $sellFinder;
    private $session;

    public function __construct(
        SellLabelUpdater $updater,
        Responder $responder,
        SellFinder $sellFinder,
        LabelUpdater $labelUpdater,
        Session $session,
    ) {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->labelUpdater = $labelUpdater;
        $this->sellFinder = $sellFinder;
        $this->session = $session;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $SellID = (int)$data['sell_id'];
        $labelID = (int)$data['id'];

        $sellRow = $this->sellFinder->findSellRow($SellID);
        $productID = (string)$sellRow['product_id'];
        $sellID = (string)$sellRow['id'];

        $user_id = $this->session->get('user')["id"];

        $this->updater->deleteSellLabelIDApi($labelID);
        $dataUpdate['up_status'] = "PACKED";
        $this->labelUpdater->updateLabelStatus($labelID, $dataUpdate, $user_id);

        return $this->responder->withRedirect($response, "select_label_for_sells?ProductID=$productID&sell_id=$sellID");
    }
}