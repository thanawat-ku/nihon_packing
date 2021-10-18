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
final class LotDefectEditAction
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
        $this->updater->updateLotDefect($lotDefectId ,$data);
        $dataLot['lot_id'] = $data['lot_id'];
        $lot = $this->lotFinder->findLots($dataLot);

        $viewData = [
            'id' => $lot[0]['id'],
        ];

        return $this->responder->withRedirect($response, "lot_defect_detail", $viewData);
    }
}
