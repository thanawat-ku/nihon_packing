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
        DefectFinder $defectFinder,
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

        $lot = $this->lotFinder->findLots($data);

        $viewData = [
            'lot' => $lot[0],
            'defects' => $this->defectFinder->findDefects($params),
            'lotDefects' => $this->finder->findLotDefects($data),
            'user_login' => $this->session->get('user'),
        ];


        return $this->twig->render($response, 'web/lotDefects.twig', $viewData);
    }
}
