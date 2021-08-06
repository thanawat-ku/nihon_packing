<?php

namespace App\Action\Api;

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
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;

    public function __construct(Twig $twig,LotDefectFinder $finder,LotDefectUpdater $updater,
    Session $session,Responder $responder)
    {
        $this->twig = $twig;
        $this->finder=$finder;
        $this->updater=$updater;
        $this->session=$session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getParsedBody();
        $user_id=$params["user_id"];
        
        $this->updater->insertLotDefectApi($params, $user_id);
        $rtdata['message']="Get Lot Successful";
        $rtdata['error']=false;
        $rtdata['lot_defects']=$this->finder->findLotDefects($params);
        
        return $this->responder->withJson($response, $rtdata);
    }
}
