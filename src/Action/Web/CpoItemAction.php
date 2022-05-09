<?php

namespace App\Action\Web;

use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\Pack\Service\PackFinder;
use App\Domain\TempQuery\Service\TempQueryFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class CpoItemAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $tempQueryFinder;
    private $packFinder;
    private $session;

    public function __construct(Twig $twig,CpoItemFinder $finder,TempQueryFinder $tempQueryFinder,
    Session $session,Responder $responder, PackFinder $packFinder)
    {
        $this->twig = $twig;
        $this->finder=$finder;
        $this->tempQueryFinder=$tempQueryFinder;
        $this->packFinder=$packFinder;
        $this->session=$session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getQueryParams();
        $packID=(int)$data['pack_id'];

        $cpodata = $this->finder->findCpoItem($data);
        $uuid=uniqid();

        $pack = null;

        $cpoitemcheck = $this->tempQueryFinder->findTempQueryCheck($data);
        if (!$cpoitemcheck) {
            $checkPackCpo = "true";
        }else{
            $checkPackCpo = "false";
        }

        $packRow = $this->packFinder->findPackRow($packID);

        $param_search['uuid']=$uuid;
        $param_search['pack_id']=$packID;

       
      
        $viewData = [
            'checkPackCpo' => $checkPackCpo, 
            'packRow'=>$packRow,
            'CpoItem' => $this->tempQueryFinder->findTempQuery($param_search),
            'user_login' => $this->session->get('user'),
        ];
        
        return $this->twig->render($response, 'web/cpoItem.twig',$viewData);
    }
}
