<?php

namespace App\Action\Api;

use App\Domain\MergePackDetail\Service\MergePackDetailFinder;
// use App\Domain\MergePackDetail\Service\MergePackDetailFinder;
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
final class MergePackDetailDeleteAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;
    private $finderlabel;

    public function __construct(Twig $twig,MergePackDetailFinder $finder, ProductFinder $productFinder, MergePackDetailUpdater $updater,
    Session $session,Responder $responder)
    {
        $this->twig = $twig;
        $this->finder=$finder;
        $this->updater=$updater;
        $this->productFinder=$productFinder;
        $this->session=$session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $label_id=$data["label_id"];
        $this->updater->deleteLabelMergePackApi($label_id, $data);

        $rtdata['message']="Get MergePackDetail Successful";
        $rtdata['error']=false;
        $rtdata['merge_pack_details']=$this->finder->findLabelNonfullys($data);

        return $this->responder->withJson($response, $rtdata);

        

    }
}