<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class MergeLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;

    public function __construct(Twig $twig,LabelFinder $finder, LabelUpdater $updater,
    Session $session,Responder $responder)
    {
        $this->twig = $twig;
        $this->finder=$finder;
        $this->updater=$updater;
        $this->session=$session;
        $this->responder = $responder;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface{
        $params = (array)$request->getParsedBody();
        $user_id=$params["user_id"];
        $labelNo=$params["label_no"];
        // $mergeNO=$params["label_no"];
        
        $rtdata['labels']=$this->finder->findLabels($params);
        $countlabel=count($rtdata['labels']);
        //---for loop---
        for ( $i=0; $i<$countlabel; $i++) {
            if($labelNo == $rtdata['labels'][$i]["label_no"]){
                $data1["product_id"]=$rtdata['labels'][$i]["product_id"];
                $data1["merge_no"]="M".str_pad($i, 3, "0", STR_PAD_LEFT);
                $this->updater->insertLabelerror($data1, $user_id,$labelNo, $params);
            }
          }
        //---for loop---
        
        return $this->responder->withJson($response, $rtdata, "merge_packs");
    }
}
