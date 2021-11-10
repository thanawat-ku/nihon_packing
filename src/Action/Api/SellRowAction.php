<?php

namespace App\Action\Api;

use App\Domain\Sell\Service\SellFinder;
use App\Domain\SellCpoItem\Service\SellCpoItemFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

use function DI\string;

/**
 * Action.
 */
final class SellRowAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $findersct;
    private $updater;

    public function __construct(
        Twig $twig,
        SellFinder $finder,
        ProductFinder $productFinder,
        SellUpdater $updater,
        Session $session,
        Responder $responder,
        SellCpoItemFinder $findersct,
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->productFinder = $productFinder;
        $this->session = $session;
        $this->responder = $responder;
        $this->findersct = $findersct;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $sell_id=$data['sell_id'];
        $user_is=$data['user_id'];

        $sells = $this->findersct->findSellCpoItems($data);
        $this->updater->updateSellApi($sell_id, $sells, $user_is);
        // $this->updater->updateSellSelectingApi($sell_id, $sells, $user_is);
        $sellRow = $this->finder->findSellRow($sell_id);

        if ($sellRow) {
            $rtdata['message'] = 'Login successfully';
            $rtdata['error'] = false;
            $rtdata['sell_row'] = $sellRow;
        } else {
            $rtdata['message'] = 'Login fail';
            $rtdata['error'] = true;
            $rtdata['sell_row'] = null;
        }

        return $this->responder->withJson($response, $rtdata);
    }
}