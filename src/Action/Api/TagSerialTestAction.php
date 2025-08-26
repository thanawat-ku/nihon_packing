<?php

namespace App\Action\Api;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Stream;
use TCPDF;

use App\Domain\TagSerial\Service\TagSerialFinder;

/**
 * Action.
 */
final class TagSerialTestAction
{
    /**
     * @var Responder
     */
    private $responder;

    /**
     * @var IssueUpdater
     */
    private $tagSerialFinder;


    public function __construct(Responder $responder,TagSerialFinder $tagSerialFinder)
    {
        $this->responder = $responder;
        $this->tagSerialFinder = $tagSerialFinder;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     * @param array $args The route arguments
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $params = (array)$request->getQueryParams();
        $rt= $this->tagSerialFinder->getTagSerialNoTest(199);
        return $this->responder->withJson($rt);
    }
}
