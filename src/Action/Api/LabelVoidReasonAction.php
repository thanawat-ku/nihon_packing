<?php

namespace App\Action\Api;

use App\Domain\LabelVoidReason\Service\LabelVoidReasonFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LabelVoidReasonAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;

    public function __construct(LabelVoidReasonFinder $finder, Responder $responder)
    {
        $this->finder = $finder;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        $rtdata['message'] = "Get LabelVoidReason Successful";
        $rtdata['error'] = false;
        $rtdata['label_void_reasons'] = $this->finder->findLabelVoidReasons($params);

        return $this->responder->withJson($response, $rtdata);
    }
}
