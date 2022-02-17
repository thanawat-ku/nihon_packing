<?php

namespace App\Action\Web;

use App\Domain\Printer\Service\PrinterFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

final class  PrinterAction
{
   
    private $responder;
    private $twig;
    private $finder;
    private $session;

 
    public function __construct(Twig $twig,PrinterFinder $finder,Session $session,Responder $responder)
    {
        $this->twig = $twig;
        $this->finder=$finder;
        $this->session=$session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        
        $viewData = [
            'printers' => $this->finder->findPrinters($params),
            'user_login' => $this->session->get('user'),
        ];
        
        return $this->twig->render($response, 'web/printers.twig',$viewData);
    }
}
