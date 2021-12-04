<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\SplitLabelDetail\Service\SplitLabelDetailFinder;
use App\Domain\SplitLabel\Service\SplitLabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

final class  SplitLabelRegisterAction
{

    private $responder;
    private $twig;
    private $finder;
    private $session;
    private $splitDetailFinder;
    private $labelUpdater;
    private $updater;


    public function __construct(
        Twig $twig,
        LabelFinder $finder,
        Session $session,
        Responder $responder,
        SplitLabelDetailFinder $splitDetailFinder,
        LabelUpdater $labelUpdater,
        SplitLabelUpdater $updater,

    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->session = $session;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->splitDetailFinder = $splitDetailFinder;
        $this->labelUpdater = $labelUpdater;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $SplitLabelId = $data["id"];

        $Finddetail["split_label_id"] = $SplitLabelId;
        $labelDetail = $this->splitDetailFinder->findSplitLabelDetails($Finddetail);


        // check printed label
        $labels = [];
        $checkLabelPrinted = 0;
        for ($i = 0; $i < sizeof($labelDetail); $i++) {
            $labelId['label_id'] = $labelDetail[$i]['label_id'];
            $label = $this->finder->findLabelSingleTable($labelId);

            if ($label[0]['status'] == "PRINTED") {
                $checkLabelPrinted++;
                array_push($labels, $label[0]);
            }
        }

        if (sizeof($labelDetail) == $checkLabelPrinted) {
            for ($i = 0; $i < sizeof($labels); $i++) {
                $labelId2 = $labels[$i]['id'];
                $data2['status'] = "PACKED";
                $this->labelUpdater->updateLabel($labelId2, $data2);
            }
            $data3['status'] = "PACKED";
            $this->updater->updateSplitLabel($SplitLabelId, $data3);
        }


        return $this->responder->withRedirect($response, "splitLabels");
    }
}
