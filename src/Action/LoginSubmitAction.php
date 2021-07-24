<?php

namespace App\Action;

use App\Domain\User\Service\UserLoginChecker;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use Symfony\Component\HttpFoundation\Session\Session;

final class LoginSubmitAction
{
    /**
     * @var Session
     */
    private $session;
    private $userLoginChecker;

    public function __construct(Session $session,UserLoginChecker $userLoginChecker)
    {
        $this->session = $session;
        $this->userLoginChecker=$userLoginChecker;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ){
        $data = (array)$request->getParsedBody();
        $username = (string)($data['username'] ?? '');
        $password = (string)($data['password'] ?? '');

        // Pseudo example
        // Check user credentials. You may use an application/domain service and the database here.
        $user = null;
        
        $userRow = $this->userLoginChecker->checkLogin($username,$password);
        if($userRow) {
            $user = $userRow;
        }

        // Clear all flash messages
        $flash = $this->session->getFlashBag();
        $flash->clear();

        // Get RouteParser from request to generate the urls
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if ($user) {
            // Login successfully
            // Clears all session data and regenerates session ID
            $this->session->invalidate();
            $this->session->start();
    
            $this->session->set('user', $user);
            $flash->set('success', 'Login successfully');
    
            // Redirect to protected page
            $url = 'users';
        } else {
            $flash->set('error', 'Login failed!');

            // Redirect back to the login page
            $url = $routeParser->urlFor('login');
        }

        return $response->withStatus(302)->withHeader('Location', $url);
    }
}