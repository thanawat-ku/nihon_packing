<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\SplitLabelDetail\Service\SplitLabelDetailFinder;
use App\Domain\SplitLabelDetail\Service\SplitLabelDetailUpdater;
use App\Domain\SplitLabel\Service\SplitLabelFinder;
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
    private $labelFinder;
    private $session;
    private $finder;
    private $splitDetailFinder;
    private $splitDetailUpdater;
    private $labelUpdater;
    private $updater;


    public function __construct(
        Twig $twig,
        LabelFinder $labelFinder,
        Session $session,
        Responder $responder,
        SplitLabelDetailFinder $splitDetailFinder,
        SplitLabelDetailUpdater $splitDetailUpdater,
        LabelUpdater $labelUpdater,
        SplitLabelUpdater $updater,
        SplitLabelFinder $finder

    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->session = $session;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->splitDetailFinder = $splitDetailFinder;
        $this->splitDetailUpdater = $splitDetailUpdater;
        $this->labelFinder = $labelFinder;
        $this->labelUpdater = $labelUpdater;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $SplitLabelId = $data["id"];



        $findDetail["split_label_id"] = $SplitLabelId;
        $split = $this->finder->findSplitLabels($findDetail);
        if ($split[0]['status'] == "CREATED") {
            $labelDetail = $this->splitDetailFinder->findSplitLabelDetails($findDetail);
            for ($i = 0; $i < sizeof($labelDetail); $i++) {
                $dataDelete['is_delete'] = "Y";
                $labelId['label_id'] = $labelDetail[$i]['label_id'];
                $label = $this->labelFinder->findLabelSingleTable($labelId);
                $labelId2 = $label[0]['id'];

                $this->labelUpdater->updateLabel($labelId2, $dataDelete);
            }

            // $this->splitDetailUpdater->deleteSplitLabelDetailAll($SplitLabelId, $data);
            
            $this->updater->updateSplitLabel($SplitLabelId, $dataDelete);
            $dataLabel['status'] = "PACKED";
            $labelId3 = $data['label_id'];
            $dataLabel['label_void_reason_id'] = 0;
            $this->labelUpdater->updateLabel($labelId3, $dataLabel);
        }


        return $this->responder->withRedirect($response, "splitLabels");
    }
}
