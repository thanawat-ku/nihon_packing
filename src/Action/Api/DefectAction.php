<?php

namespace App\Action\Api;

use App\Domain\Defect\Service\DefectFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class DefectAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $twig;
    private $session;

    public function __construct(Twig $twig,DefectFinder $finder,
    Session $session,Responder $responder)
    {
        $this->twig = $twig;
        $this->finder=$finder;
        $this->session=$session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        
        $rtdata['message']="Get Defect Successful";
        $rtdata['error']=false;
        $rtdata['defects']=$this->finder->findDefects($params);


        
        return $this->responder->withJson($response, $rtdata);
    }
}
