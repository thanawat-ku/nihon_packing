<?php

namespace App\Action\Web;

use App\Domain\LotDefect\Service\LotDefectFinder;
use App\Domain\Lot\Service\LotFinder;
use App\Domain\Defect\Service\DefectFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LotDefectAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $session;
    private $lotFinder; 
    private  $defectFinder;

    public function __construct(
        Twig $twig,
        LotDefectFinder $finder,
        Session $session,
        Responder $responder,
        LotFinder $lotFinder,
        DefectFinder $defectFinder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->session = $session;
        $this->responder = $responder;
        $this->lotFinder = $lotFinder;
        $this->defectFinder = $defectFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        $data['lot_id'] = $params['id'];

        $lotDefects = $this->finder->findLotDefects($data);
        $lot['id'] = $data['lot_id'];
        $lot['lot_no'] = $lotDefects[0]['lot_no'];
        
        if (isset($lotDefects[0])) {
            $qtyLotDefacts  = 0;
            for ($j = 0; $j < sizeof($lotDefects); $j++) {
                $qtyLotDefacts = $qtyLotDefacts + (int)$lotDefects[$j]['quantity'];
            }
            $lots['qty_lot_defact'] = $qtyLotDefacts;
        } else {
            $lots['qty_lot_defact'] = 0;
        }

        $viewData = [
            'lot' => $lot,
            'defects' => $$this->defectFinder->findDefects($params),
            'lotDefects' => $lotDefects,
            'user_login' => $this->session->get('user'),
        ];


        return $this->twig->render($response, 'web/lotDefects.twig', $viewData);
    }
}
