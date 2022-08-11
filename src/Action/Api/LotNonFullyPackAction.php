<?php

namespace App\Action\Api;

use App\Domain\LotNonFullyPack\Service\LotNonFullyPackFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LotNonFullyPackAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;

    public function __construct(
        Twig $twig,
        LotNonFullyPackFinder $finder,
        Session $session,
        Responder $responder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        $rtdata['message'] = "Get LotNonFullyPack Successful";
        $rtdata['error'] = false;
        $rtdata['lot_non_fully_packs'] = $this->finder->findLotNonFullyPacks($params);

        return $this->responder->withJson($response, $rtdata);
    }
}
