<?php

namespace App\Action\Api;

use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
    private $CpoItemFinder;
    private $tempQueryUpdater;
    private $tempQueryFinder;
    private $session;


    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(CpoItemFinder $CpoItemFinder, Responder $responder, 
    Session $session)
    {

        $this->CpoItemFinder = $CpoItemFinder;
        $this->responder = $responder;
        $this->session = $session;
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
        $packID=(int)$params['pack_id'];

        $cpodata = $this->CpoItemFinder->findCpoItem($params);
        $uuid=uniqid();

        $param_search['uuid']=$uuid;
        $param_search['pack_id']=$packID;
        $cpoitemdata['message'] = "Get CpoItem Successful";
        $cpoitemdata['error'] = false;

        return $this->responder->withJson($response, $cpoitemdata);
    }
}
