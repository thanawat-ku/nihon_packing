<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\SplitLabelDetail\Service\SplitLabelDetailFinder;
use App\Domain\SplitLabel\Service\SplitLabelFinder;
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
    private $splitFinder;
    private $labelUpdater;
    private $updater;


    public function __construct(
        Twig $twig,
        LabelFinder $finder,
        Session $session,
        Responder $responder,
        SplitLabelDetailFinder $splitDetailFinder,
        SplitLabelFinder $splitFinder,
        LabelUpdater $labelUpdater,
        SplitLabelUpdater $updater
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->session = $session;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->splitDetailFinder = $splitDetailFinder;
        $this->splitFinder = $splitFinder;
        $this->labelUpdater = $labelUpdater;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $SplitLabelId = $data["id"];



        $Finddetail["split_label_id"] = $SplitLabelId;
        $labelDetail = $this->splitDetailFinder->findSplitLabelDetails($Finddetail);


        $split = $this->splitFinder->findSplitLabels($Finddetail);
        if ($split[0]['status'] == "PRINTED") {
            $data2['status'] = "PACKED";
            for ($i = 0; $i < sizeof($labelDetail); $i++) {
                $labelId2 = $labelDetail[$i]['label_id'];

                $this->labelUpdater->updateLabel($labelId2, $data2);
            }
            $this->updater->updateSplitLabel($SplitLabelId, $data2);
        }
        $viewData = [];
        return $this->responder->withRedirect($response, "splitLabels", $viewData);
    }
}
