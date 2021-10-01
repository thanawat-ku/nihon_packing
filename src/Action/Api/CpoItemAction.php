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


    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(CpoItemFinder $CpoItemFinder, Responder $responder, Session $session)
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

        $rtdata = $this->CpoItemFinder->findCpoItem($params);

        // $stack = array();

        // for ($i = 0; $i < count($rtdata['cpo_item']); $i++) {
        //     if ($rtdata['cpo_item'][$i]['Quantity'] != $rtdata['cpo_item'][$i]['PackingQty']) {
        //         $cpodata = $rtdata['cpo_item'][$i];
        //         array_push($stack, $cpodata);
        //     }
        // }

        $cpoitemdata['message'] = "Get CpoItem Successful";
        $cpoitemdata['error'] = false;
        $cpoitemdata['cpo_item'] = $rtdata;

        return $this->responder->withJson($response, $cpoitemdata);
    }
}
