<?php

namespace App\Action\Api;

use App\Domain\CpoItem\Service\CpoItemFinder;
// use App\Domain\CpoItem\Service\TempQueryFinder;
use App\Domain\TempQuery\Service\TempQueryUpdater;
use App\Domain\TempQuery\Service\TempQueryFinder;
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


    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(CpoItemFinder $CpoItemFinder, TempQueryFinder $tempQueryFinder, Responder $responder, 
    Session $session,TempQueryUpdater $tempQueryUpdater)
    {

        $this->CpoItemFinder = $CpoItemFinder;
        $this->responder = $responder;
        $this->session = $session;
        $this->tempQueryUpdater = $tempQueryUpdater;
        $this->tempQueryFinder = $tempQueryFinder;
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

        $cpoitemcheck = $this->tempQueryFinder->findTempQueryCheck($params);

        $param_search['uuid']=$uuid;
        $param_search['pack_id']=$packID;
        $cpoitemdata['message'] = "Get CpoItem Successful";
        $cpoitemdata['error'] = false;
        $cpoitemdata['data'] = $this->tempQueryFinder->findTempQuery($param_search);

        return $this->responder->withJson($response, $cpoitemdata);
    }
}
