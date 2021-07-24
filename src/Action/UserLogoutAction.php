<?php

namespace App\Action;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class UserLogoutAction
{
    /**
     * @var Session
     */
    private $session;
    private $responder;

    public function __construct(SessionInterface $session, Responder $responder)
    {
        $this->session = $session;
        
        $this->responder = $responder;
    }
    

    public function __invoke(
        ServerRequestInterface $request, 
        ResponseInterface $response
    ): ResponseInterface {
        // Logout user
        $this->session->invalidate();

        $data['error'] = false;
        $data['message'] = 'Login successfully';
        return $this->responder->withJson($response, $data);
    }
}