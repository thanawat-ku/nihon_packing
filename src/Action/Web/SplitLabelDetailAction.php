<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\SplitLabelDetail\Service\SplitLabelDetailFinder;
use App\Domain\SplitLabel\Service\SplitLabelFinder;
use App\Domain\LabelVoidReason\Service\LabelVoidReasonFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

final class  SplitLabelDetailAction
{

    private $responder;
    private $twig;
    private $finder;
    private $session;
    private $splitDetailFinder;
    private $labelFinder;


    public function __construct(
        Twig $twig,
        SplitLabelFinder $finder,
        LabelFinder $labelFinder,
        Session $session,
        Responder $responder,
        SplitLabelDetailFinder $splitDetailFinder,
        LabelVoidReasonFinder $voidReasonFinder,
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->labelFinder = $labelFinder;
        $this->session = $session;
        $this->responder = $responder;
        $this->splitDetailFinder = $splitDetailFinder;
        $this->voidReasonFinder = $voidReasonFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getQueryParams();
        $SplitLabelId = $data["id"];

        $dataDetail["split_label_id"] = $SplitLabelId;
        $labelDetail = $this->splitDetailFinder->findSplitLabelDetails($dataDetail);

        $labels = [];
        for ($i = 0; $i < sizeof($labelDetail); $i++) {

            $labelId['label_id'] = $labelDetail[$i]['label_id'];
            $label = $this->labelFinder->findLabels($labelId);
            if(!isset($label[0])){
                $label = $this->labelFinder->findLabelForLotZero($labelId);
            }
            array_push($labels, $label[0]);
        }
        $findSplitNo['split_label_id'] = $SplitLabelId;
        $splitLabel = $this->finder->findSplitLabels($findSplitNo);

        if (isset($splitLabel[0])) {
            $split =  $splitLabel[0];
        } else {
            $split = "ERROR";
        }

        $viewData = [
            'split' => $split,
            'labels' => $labels,
            'void_reasons' => $this->voidReasonFinder->findLabelVoidReasonsForVoidLabel($data),
            'user_login' => $this->session->get('user'),
        ];
        return $this->twig->render($response, 'web/labelsSplit.twig', $viewData);
    }
}
