<?php

namespace App\Action\Api;

use App\Domain\Section\Service\SectionFinder;
use App\Domain\Section\Service\SectionUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

final class SectionSyncAction
{
    /**
     * @var Responder
     */ 
    private $responder;
    private $finder;
    private $updater;
    private $twig;

    public function __construct(
        Twig $twig,
        SectionFinder $finder,
        SectionUpdater $updater,
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

        $max_id=$this->finder->getLocalMaxSectionId();

        $sections = $this->finder->getSyncSections($max_id);
        $rtData=[];
        
        for($i=0;$i<count($sections);$i++)
        {
            $params1['id']=$sections[$i]["SectionID"];
            $params1['section_name']=$sections[$i]["SectionName"];
            $params1['section_description']=$sections[$i]["SectionDesc"]??"";
            $params1['is_vendor']=$sections[$i]["IsVendor"];
            $params1['is_scrap']=$sections[$i]["IsScrap"];
            $this->updater->insertSection($params1);
            $rtData=[];
            array_push($rtData, $sections[$i]);
        }

        return $this->responder->withJson($response, $rtData);
    }
}