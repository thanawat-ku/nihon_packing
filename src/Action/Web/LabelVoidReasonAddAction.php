<?php

namespace App\Action\Web;

use App\Domain\LabelVoidReason\Service\LabelVoidReasonFinder;
use App\Domain\LabelVoidReason\Service\LabelVoidReasonUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

final class  LabelVoidReasonAddAction
{

    private $responder;
    private $twig;
    private $finder;
    private $session;
    private $updater;


    public function __construct(
        Twig $twig,
        LabelVoidReasonFinder $finder,
        Session $session,
        Responder $responder,
        LabelVoidReasonUpdater $updater,
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        
        $this->updater->insertLabelVoidReason($data);

        return $this->responder->withRedirect($response, "label_void_reasons");
    }
}
