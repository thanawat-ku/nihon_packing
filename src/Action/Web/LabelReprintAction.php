<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\Lot\Service\LotFinder;
use App\Domain\Lot\Service\LotUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LabelReprintAction
{
    private $session;
    private $responder;
    private $updater;
    private $finder;
    private $lotFinder;
    private $lotUpdater;

    public function __construct(
        Session $session,
        Responder $responder,
        LabelUpdater $updater,
        LabelFinder $finder,
        LotFinder $lotFinder,
        LotUpdater $lotUpdater
    ) {
        $this->responder = $responder;
        $this->session = $session;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->lotFinder = $lotFinder;
        $this->lotUpdater = $lotUpdater;
    }


    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        // Extract the form data from the request body
        $data = (array)$request->getParsedBody();
        $labelId = $data["id"];
        $params["printer_id"] = $data["printer_id"];
        $params["wait_print"] = "Y";
        $this->updater->updateLabel($labelId,$params);

        return $this->responder->withRedirect($response, "labels");
        
    }
}
