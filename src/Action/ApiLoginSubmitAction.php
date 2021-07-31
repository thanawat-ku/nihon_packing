<?php

namespace App\Action;

use App\Domain\User\Service\UserLoginChecker;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Responder\Responder;

final class ApiLoginSubmitAction
{
    private $userLoginChecker;
    private $responder;

    public function __construct(UserLoginChecker $userLoginChecker, Responder $responder)
    {
        $this->userLoginChecker=$userLoginChecker;
        $this->responder=$responder;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $username = (string)($data['username'] ?? '');
        $password = (string)($data['password'] ?? '');

        $user = null;
        
        $userRow = $this->userLoginChecker->checkLogin($username,$password);
        if($userRow) {
            $user = $userRow;
        }

        if ($user) {
            $rtdata['message'] = 'Login successfully';
            $rtdata['error']=false;
            $rtdata['user']=$user;
        } else {
            $rtdata['message'] = 'Login fail';
            $rtdata['error']=true;
            $rtdata['user']=null;
        }
        return $this->responder->withJson($response, $rtdata);
    }
}