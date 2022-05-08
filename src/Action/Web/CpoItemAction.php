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
    private $sellFinder;
    private $session;

    public function __construct(Twig $twig,CpoItemFinder $finder,TempQueryFinder $tempQueryFinder,
    Session $session,Responder $responder, PackFinder $sellFinder)
    {
        $this->twig = $twig;
        $this->finder=$finder;
        $this->tempQueryFinder=$tempQueryFinder;
        $this->sellFinder=$sellFinder;
        $this->session=$session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getQueryParams();
        $sellID=(int)$data['pack_id'];

        $cpodata = $this->finder->findCpoItem($data);
        $uuid=uniqid();

        $sell = null;

        $cpoitemcheck = $this->tempQueryFinder->findTempQueryCheck($data);
        if (!$cpoitemcheck) {
            $checkPackCpo = "true";
        }else{
            $checkPackCpo = "false";
        }

        $sellRow = $this->sellFinder->findPackRow($sellID);

        $param_search['uuid']=$uuid;
        $param_search['pack_id']=$sellID;

       
      
        $viewData = [
            'checkPackCpo' => $checkPackCpo, 
            'sellRow'=>$sellRow,
            'CpoItem' => $this->tempQueryFinder->findTempQuery($param_search),
            'user_login' => $this->session->get('user'),
        ];
        
        return $this->twig->render($response, 'web/cpoItem.twig',$viewData);
    }
}
