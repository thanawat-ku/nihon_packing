<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Pack\Service\PackFinder;
use App\Domain\PackLabel\Service\PackLabelUpdater;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

use function DI\string;

/**
 * Action.
 */
final class PackLabelCancelAction
{
    private $responder;
    private $twig;
    private $finder;
    private $updater;
    private $labelUpdater;
    private $sellFinder;
    private $session;
    

    public function __construct(
        Twig $twig,
        LabelFinder $finder,
        PackLabelUpdater $updater,
        Responder $responder,
        PackFinder $sellFinder,
        LabelUpdater $labelUpdater,
        Session $session
    ) {
        $this->twig = $twig;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->labelUpdater = $labelUpdater;
        $this->sellFinder = $sellFinder;
        $this->session = $session;
        $this->finder=$finder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $PackID = (int)$data['pack_id'];
        $labelID = (int)$data['id'];

        $sellRow = $this->sellFinder->findPackRow($PackID);

        $user_id = $this->session->get('user')["id"];

        $this->updater->deletePackLabelIDApi($labelID);
        $dataUpdate['up_status'] = "PACKED";
        $this->labelUpdater->updateLabelStatus($labelID, $dataUpdate, $user_id);

        $labels=[];
        $labelFromLot = $this->finder->findCreateMergeNoFromLabels($data);
        array_push($labels,$labelFromLot);
        $labelFromMerge = $this->finder->findLabelFromMergePacks($data);
        array_push($labels,$labelFromMerge);

        $sellRow = $this->sellFinder->findPackRow($PackID);

        $viewData = [
            'pack_id'=> $sellRow['id'],
            'product_id'=> $sellRow['product_id'],
        ];
        
        return $this->responder->withRedirect($response, "pack_labels",$viewData);
    }
}
