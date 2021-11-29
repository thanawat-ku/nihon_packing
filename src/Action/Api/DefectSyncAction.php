<?php

namespace App\Action\Api;

use App\Domain\Defect\Service\DefectFinder;
use App\Domain\Defect\Service\DefectUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

final class DefectSyncAction
{
    /**
     * @var Responder
     */ 
    private $responder;
    private $finder;
    private $updater;

    public function __construct(
        Twig $twig,
        DefectFinder $finder,
        DefectUpdater $updater,
        Responder $responder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        $max_id=$this->finder->getLocalMaxDefectId();

        $defects = $this->finder->getSyncDefects($max_id);
        $rtData=[];
        
        for($i=0;$i<count($defects);$i++)
        {
            $params1['id']=$defects[$i]["CauseID"];
            $params1['defect_name']=$defects[$i]["CauseName"];
            $params1['defect_description']=$defects[$i]["CauseDesc"]??"";
            $params1['oqc_check']=$defects[$i]["OqcCheck"];
            $this->updater->insertDefect($params1);
            $rtData=[];
            array_push($rtData, $defects[$i]);
        }

        return $this->responder->withJson($response, $rtData);
    }
}