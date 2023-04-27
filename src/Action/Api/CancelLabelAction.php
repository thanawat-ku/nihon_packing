<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\MergePackDetail\Service\MergePackDetailUpdater;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class CancelLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $findermpd;
    private $updater;
    private $upmergepackdetail;
    private $productFinder;

    public function __construct(LabelFinder $finder,ProductFinder $productFinder, LabelUpdater $updater,
    Responder $responder, MergePackDetailUpdater $findermpd,MergePackDetailUpdater $upmergepackdetail)
    {
        $this->finder=$finder;
        $this->updater=$updater;
        $this->productFinder=$productFinder;
        $this->responder = $responder;
        $this->findermpd = $findermpd;
        $this->upmergepackdetail = $upmergepackdetail;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $user_id=$data["user_id"];
        $label_id=$data['label_id'];
        $mpd_data_id=$data['id'];
        $data1['label_id']=$label_id;

        $label = $this->finder->findLabelSingleTable($data1);
        $label['check_mp_id']=$data['merge_pack_id'];

        $this->upmergepackdetail->deleteLabelMergePackDetailApi($mpd_data_id);
        $this->updater->updateCancelLabelApi($label_id, $label, $user_id);
        
        return $this->responder->withJson($response, $data);
    }
}