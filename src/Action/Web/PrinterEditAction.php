<?php

namespace App\Action\Web;

use App\Domain\Printer\Service\PrinterFinder;
use App\Domain\Printer\Service\PrinterUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

final class  PrinterEditAction
{

    private $responder;
    private $twig;
    private $finder;
    private $session;
    private $updater;


    public function __construct(
        Twig $twig,
        PrinterFinder $finder,
        Session $session,
        Responder $responder,
        PrinterUpdater $updater
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $printerID = $data['id'];
        $this->updater->updatePrinter($printerID, $data);

        return $this->responder->withRedirect($response, "printers");
    }
}
