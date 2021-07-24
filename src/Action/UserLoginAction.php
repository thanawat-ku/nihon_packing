<?php

namespace App\Action;

use App\Domain\User\Service\UserLoginChecker;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class UserLoginAction
{
    private $session;
    private $responder;
    private $userLoginChecker;

    public function __construct(Session $session,UserLoginChecker $userLoginChecker, Responder $responder)
    {
        $this->session = $session;
        $this->userLoginChecker=$userLoginChecker;
        $this->responder = $responder;
    }


    public function __invoke(
        ServerRequestInterface $request, 
        ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getQueryParams();
        $username = (string)($data['username'] ?? '');
        $password = (string)($data['password'] ?? '');

        $user = null;
        $userRow = $this->userLoginChecker->checkLogin($username,$password);
        if($userRow) {
            $user = $userRow;
        }

        // Clear all flash messages
        $flash = $this->session->getFlashBag();
        $flash->clear();

        if ($user) {
            // Login successfully
            // Clears all session data and regenerates session ID
            $this->session->invalidate();
            $this->session->start();
    
            $this->session->set('user', $user);
            $flash->set('success', 'Login successfully');

            $rtdata['error'] = false;
            $rtdata['message'] = 'Login successfully';
            $rtdata['username'] = $username;
            $rtdata['user'] = $user;
    
        } else {
            $flash->set('error', 'Login failed!');
            
            $rtdata['error'] = true;
            $rtdata['message'] = 'Invalid username or password';
        }

        return $this->responder->withJson($response, $rtdata);
    }
}
