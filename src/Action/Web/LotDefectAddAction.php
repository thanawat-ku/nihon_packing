<?php

namespace App\Action\Web;

use App\Domain\LotDefect\Service\LotDefectFinder;
use App\Domain\LotDefect\Service\LotDefectUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LotDefectAddAction
{
    private $responder;
    private $updater;
    private $finder;
    private $session;
    private $twig;


    public function __construct(
        Responder $responder,
        LotDefectUpdater $updater,
        LotDefectFinder $finder,
        Session $session,
        Twig $twig,

    ) {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->session = $session;
        $this->twig = $twig;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();

        $this->updater->insertLotDefect($data);

        return $this->responder->withRedirect($response, "lots");
    }
}
