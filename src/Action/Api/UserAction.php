<?php

namespace App\Action\Api;

use App\Domain\User\Service\UserFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class UserAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;

    public function __construct(UserFinder $finder, Responder $responder)
    {
        $this->finder = $finder;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        $rtdata['message'] = "Get User Successful";
        $rtdata['error'] = false;
        $rtdata['users'] = $this->finder->findUsers($params);

        return $this->responder->withJson($response, $rtdata);
    }
}
