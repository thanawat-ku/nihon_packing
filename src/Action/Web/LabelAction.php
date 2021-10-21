<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\LabelVoidReason\Service\LabelVoidReasonFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

final class  LabelAction
{

    private $responder;
    private $twig;
    private $finder;
    private $voidReasonFinder;
    private $session;


    public function __construct(
        Twig $twig,
        LabelFinder $finder,
        Session $session,
        Responder $responder,
        LabelVoidReasonFinder $voidReasonFinder,
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->session = $session;
        $this->responder = $responder;
        $this->voidReasonFinder = $voidReasonFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        if (!isset($params['startDate'])) {
            $params['startDate'] = date('Y-m-d', strtotime('-3 days', strtotime(date('Y-m-d'))));
            $params['endDate'] = date('Y-m-d');
            $data2['startDate'] = $params['startDate'];
            $data2['endDate'] = $params['endDate'];
        }else{
            $data2['startDate'] = $params['startDate'];
            $data2['endDate'] = $params['endDate'];
        }

        
        $labels1 = $this->finder->findLabels($params);
        $data2['lot_zero'] = 0;
        $labels2 =  $this->finder->findLabelForLotZero($data2);
        if(isset($labels2[0])){
            $labelsAll = array_merge($labels1,$labels2);
        }
        else{
            $labelsAll = $labels1;
        }
        
        $viewData = [
            'labels' => $labelsAll,
            'void_reasons' => $this->voidReasonFinder->findLabelVoidReasonsForVoidLabel($params),
            'user_login' => $this->session->get('user'),
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
        ];


        return $this->twig->render($response, 'web/labels.twig', $viewData);
    }
}
