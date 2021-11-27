<?php

namespace App\Action\Api;

use App\Domain\Product\Service\ProductFinder;
use App\Domain\Product\Service\ProductUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

final class ProductSyncAction
{
    /**
     * @var Responder
     */ 
    private $responder;
    private $finder;
    private $updater;

    public function __construct(
        Twig $twig,
        ProductFinder $finder,
        ProductUpdater $updater,
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

        $max_id=$this->finder->getLocalMaxProductId();

        $products = $this->finder->getSyncProducts($max_id);
        $rtData=[];
        for($i=0;$i<count($products);$i++)
        {
            $params1['id']=$products[$i]["ProductID"];
            $params1['part_no']=$products[$i]["PartNo"];
            $params1['part_name']=$products[$i]["PartName"];
            $params1['part_code']=$products[$i]["PartCode"];
            $params1['customer_id']=$products[$i]["CustomerID"];
            $params1['std_pack']=$products[$i]["PackingStd"];
            $params1['std_box']=$products[$i]["BoxStd"];
            $this->updater->insertProduct($params1);
            $rtData=[];
            array_push($rtData, $products[$i]);
        }

        return $this->responder->withJson($response, $rtData);
    }
}