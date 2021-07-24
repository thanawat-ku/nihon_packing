<?php

namespace App\Action;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class LogoutAction
{
    /**
     * @var Session
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    

    public function __invoke(
        ServerRequestInterface $request, 
        ResponseInterface $response
    ): ResponseInterface {
        // Logout user
        $this->session->invalidate();

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $url = $routeParser->urlFor('login');
        
        return $response->withStatus(302)->withHeader('Location', $url);
    }
}