<?php

namespace App\Action\Api;

use App\Domain\Sell\Service\SellFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class SellProductIDAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;

    public function __construct(
        Twig $twig,
        SellFinder $finder,
        ProductFinder $productFinder,
        SellUpdater $updater,
        Session $session,
        Responder $responder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->productFinder = $productFinder;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        // $merge_status = (string)($data['merge_status'] ?? '');
        $product_id = (int)($data['product_id'] ?? '');
        $user_id = (int)($data['user_id'] ?? '');
        // $merge_pack_ID = $data['id'];


        if ($product_id == 0) {
           
        } else {
            $sell = null;

            // $merge_pack_id = $this->finder->findSells($data);

            $sellRow = $this->finder->findSellProductID($product_id );

            if ($sellRow) {
                $sell = $sellRow;
            }


            if ($sell) {
                $rtdata['message'] = 'Login successfully';
                $rtdata['error'] = false;
                $rtdata['sell'] = $sell;
            } else {
                $rtdata['message'] = 'Login fail';
                $rtdata['error'] = true;
                $rtdata['sell'] = null;
            }
        }
        return $this->responder->withJson($response, $rtdata);
    }
}
