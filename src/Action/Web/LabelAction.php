<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

final class  LabelAction
{
   
    private $responder;
    private $twig;
    private $finder;
    private $session;

 
    public function __construct(Twig $twig,LabelFinder $finder,Session $session,Responder $responder)
    {
        $this->twig = $twig;
        $this->finder=$finder;
        $this->session=$session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        
        if(!isset($params['startDate'])){
            $params['startDate']=date('Y-m-d',strtotime('-3 days',strtotime(date('Y-m-d'))));
            $params['endDate']=date('Y-m-d');
        }

        $viewData = [
            'labels' => $this->finder->findLabels($params),
            'user_login' => $this->session->get('user'),
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
        ];
        

        return $this->twig->render($response, 'web/labels.twig',$viewData);
    }
}
