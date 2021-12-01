<?php

namespace App\Action\Api;

use App\Domain\Tag\Service\TagFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class TagAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;

    public function __construct(
        Twig $twig,
        TagFinder $finder,
        Responder $responder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        $rtdata['message'] = "Get Tag Successful";
        $rtdata['error'] = false;
        $rtdata['tags'] = $this->finder->findTags($params);

        return $this->responder->withJson($response, $rtdata);
    }
}
