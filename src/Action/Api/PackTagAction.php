<?php

namespace App\Action\Api;

use App\Domain\Pack\Service\PackFinder;
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
final class PackTagAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;


    public function __construct(
        Twig $twig,
        PackFinder $finder,
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
        $packID=$data['pack_id'];

        $packRow = $this->finder->findPackRow($packID);
        $packTag = $this->finder->findPackTag($data);
        $packLabel = $this->finder->findPackLabel($data);

        $totalLabel = count($packLabel);
        $totalTag = count($packTag);

        $packRow['total_label'] = $totalLabel;
        $packRow['total_tag'] = $totalTag;

        if ($packTag) {
            $rtdata['message'] = 'Login successfully';
            $rtdata['error'] = false;
            $rtdata['pack_row'] = $packRow;
        } else {
            $rtdata['message'] = 'Login fail';
            $rtdata['error'] = true;
            $rtdata['pack_row'] = null;
        }

        return $this->responder->withJson($response, $rtdata);
    }
}
