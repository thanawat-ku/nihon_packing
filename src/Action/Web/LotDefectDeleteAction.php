<?php

namespace App\Action\Web;

use App\Domain\LotDefect\Service\LotDefectFinder;
use App\Domain\LotDefect\Service\LotDefectUpdater;
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
final class LotDefectDeleteAction
{
    private $responder;
    private $updater;
    private $finder;
    private $session;
    private $twig;
    private $lotFinder; 
    private  $defectFinder;


    public function __construct(
        Responder $responder,
        LotDefectUpdater $updater,
        LotDefectFinder $finder,
        Session $session,
        Twig $twig,
        LotFinder $lotFinder,
        DefectFinder $defectFinder,

    ) {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->session = $session;
        $this->twig = $twig;
        $this->lotFinder = $lotFinder;
        $this->defectFinder = $defectFinder;
        
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $lotDefectId = $data['id'];
        $this->updater->deleteLotDefect($lotDefectId);
        $dataLot['lot_id'] = $data['lot_id'];
        $lot = $this->lotFinder->findLots($dataLot);

        $viewData = [
            'lot' => $lot[0],
            'defects' => $this->defectFinder->findDefects($data),
            'lotDefects' => $this->finder->findLotDefects($dataLot),
            'user_login' => $this->session->get('user'),
        ];

        return $this->twig->render($response, 'web/lotDefects.twig', $viewData);
    }
}
