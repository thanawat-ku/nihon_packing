<?php

namespace App\Action\Api;

use App\Domain\Sell\Service\SellFinder;
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
final class SellTagAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;


    public function __construct(
        Twig $twig,
        SellFinder $finder,
        ProductFinder $productFinder,
        Session $session,
        Responder $responder,
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->productFinder = $productFinder;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $sellID=$data['sell_id'];

        $sellRow = $this->finder->findSellRow($sellID);
        $sellTag = $this->finder->findSellTag($data);
        $sellLabel = $this->finder->findSellLabel($data);

        $totalLabel = count($sellLabel);
        $totalTag = count($sellTag);

        $sellRow['total_label'] = $totalLabel;
        $sellRow['total_tag'] = $totalTag;

        if ($sellTag) {
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
