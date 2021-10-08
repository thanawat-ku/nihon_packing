<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\SplitLabelDetail\Service\SplitLabelDetailFinder;
use App\Domain\SplitLabelDetail\Service\SplitLabelDetailUpdater;
use App\Domain\SplitLabel\Service\SplitLabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

final class  SplitLabelDeleteAction
{

    private $responder;
    private $twig;
    private $finder;
    private $session;
    private $splitDetailFinder;
    private $splitDetailUpdater;
    private $labelUpdater;
    private $updater;


    public function __construct(
        Twig $twig,
        LabelFinder $finder,
        Session $session,
        Responder $responder,
        SplitLabelDetailFinder $splitDetailFinder,
        SplitLabelDetailUpdater $splitDetailUpdater,
        LabelUpdater $labelUpdater,
        SplitLabelUpdater $updater,

    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->session = $session;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->splitDetailFinder = $splitDetailFinder;
        $this->splitDetailUpdater = $splitDetailUpdater;
        $this->labelUpdater = $labelUpdater;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $SplitLabelId = $data["id"];

        $Fdetail["split_label_id"] = $SplitLabelId;
        $labelDetail = $this->splitDetailFinder->findSplitLabelDetails($Fdetail);
        for ($i = 0; $i < sizeof($labelDetail); $i++) {
            $labelId['label_id'] = $labelDetail[$i]['label_id'];
            $label = $this->finder->findLabels($labelId);
            $labelId2 = $label[0]['id'];
            $this->labelUpdater->deleteLabel($labelId2, $data);
        }

        $this->splitDetailUpdater->deleteSplitLabelDetailAll($SplitLabelId, $data);
        $this->updater->deleteSplitLabel($SplitLabelId, $data);
        $dataLabel['status'] = "PACKED";
        $labelId3 = $data['label_id'];
        $this->labelUpdater->updateLabel($labelId3, $dataLabel);

        return $this->responder->withRedirect($response, "splitLabels");
    }
}
