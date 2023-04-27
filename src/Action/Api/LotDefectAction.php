<?php

namespace App\Action\Api;

use App\Domain\LotDefect\Service\LotDefectFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LotDefectAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $twig;
    private $session;

    public function __construct(Twig $twig,LotDefectFinder $finder,
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
        
        $rtdata['message']="Get LotDefect Successful";
        $rtdata['error']=false;
        $rtdata['lot_defects']=$this->finder->findLotDefects($params);


        
        return $this->responder->withJson($response, $rtdata);
    }
}
