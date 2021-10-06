<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\SplitLabelDetail\Service\SplitLabelDetailFinder;
use App\Domain\SplitLabel\Service\SplitLabelFinder;
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


    public function __construct(
        Twig $twig,
        SplitLabelFinder $finder,
        LabelFinder $labelFinder,
        Session $session,
        Responder $responder,
        SplitLabelDetailFinder $splitDetailFinder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->labelFinder = $labelFinder;
        $this->session = $session;
        $this->responder = $responder;
        $this->splitDetailFinder = $splitDetailFinder;
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
            array_push($labels, $label[0]);
        }
        $findSplitNo['split_label_id'] = $SplitLabelId;
        $splitNo = $this->finder->findSplitLabels($findSplitNo);
        $viewData = [
            'split_no' => $splitNo[0]['split_label_no'],
            'labels' => $labels,
            'user_login' => $this->session->get('user'),
        ];
        return $this->twig->render($response, 'web/labelsSplit.twig', $viewData);
    }
}
