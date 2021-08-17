<?php

namespace App\Action\Api;

use App\Domain\LabelNonfully\Service\LabelNonfullyFinder;
use App\Domain\LabelNonfully\Service\LabelNonfullyUpdater;
use App\Domain\Lot\Service\LotFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class DefectAddAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;

    public function __construct(Twig $twig,LabelNonfullyFinder $finder,LotFinder $productFinder,
    Session $session,Responder $responder, LabelNonfullyUpdater $updater)
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
        $user_id=$params["user_id"];
        $labelId=$params["merge_pack_id"];
        
        $this->updater->updateLabelNonfullyApi($labelId, $params, $user_id);
        
        $rtdata['message']="Get Lot Successful";
        $rtdata['error']=false;
        $rtdata['labels']=$this->finder->findLabelNonfullys($params);

        
        return $this->responder->withJson($response, $rtdata);
    }
}