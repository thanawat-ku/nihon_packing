<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LotReprintAction
{
    private $responder;
    private $updater;
    private $finder;

    public function __construct(Responder $responder, LabelUpdater $updater, LabelFinder $finder)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $lotId = $data["lot_id"];
        $printerId = $data["printer_id"];
        $params['lot_id']=$lotId;

        $labels = $this->finder->findLabels($params);
        for ($i = 0; $i < sizeof($labels); $i++) {
            $labelId = $labels[$i]['id'];
            $data['wait_print'] = "Y";
            $data['printer_id'] = $printerId;
            $this->updater->updateLabel($labelId, $data, $user_id);
        }

        return $this->responder->withRedirect($response, "lots");
    }
}
