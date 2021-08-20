<?php

namespace App\Action\Api;

use App\Domain\LabelPackMerge\Service\LabelPackMergeFinder;
use App\Domain\LabelPackMerge\Service\LabelPackMergeUpdater;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class UpStatusMergedAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;

    public function __construct(Twig $twig,LabelPackMergeFinder $finder,ProductFinder $productFinder, LabelPackMergeUpdater $updater,
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
        $params = (array)$request->getParsedBody();
        $merge_status=$params["merge_status"];
        $user_id=$params["user_id"];
        $id=$params["label_no"];
       

        $this->updater->updateLabelPackMergeApi($id, $params, $user_id);

        $rtdata['message']="Get MergePack Successful";
        $rtdata['error']=false;
        $rtdata['merge_packs']=$this->finder->findLabelPackMerges($params);


        return $this->responder->withJson($response, $rtdata);

        

    }
}