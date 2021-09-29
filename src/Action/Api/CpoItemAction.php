<?php

namespace App\Action\Api;

use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class CpoItemAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $productFinder;
    

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(CpoItemFinder $productFinder,Responder $responder)
    {
        
        $this->productFinder=$productFinder;
        $this->responder = $responder;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $params = (array)$request->getQueryParams();
        
        $rtdata['message']="Get CpoItem Successful";
        $rtdata['error']=false;
        $rtdata['cpo_item']=$this->productFinder->findCpoItem($params);        

        return $this->responder->withJson($response, $rtdata);

        // $params = (array)$request->getQueryParams();
        
        // $viewData = [
        //     'cpo_item' => $this->productFinder->findCpoItem($params),
        //     'user_login' => $this->session->get('user'),
        // ];
        

        // return $this->twig->render($response, 'web/cpo_item.twig',$viewData);
    }
}