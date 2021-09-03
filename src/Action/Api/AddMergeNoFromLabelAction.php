<?php

namespace App\Action\Api;

use App\Domain\CreateMergeNoFromLabel\Service\CreateMergeNoFromLabelFinder;
use App\Domain\CreateMergeNoFromLabel\Service\CreateMergeNoFromLabelUpdater;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class AddMergeNoFromLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;

    public function __construct(Twig $twig,CreateMergeNoFromLabelFinder $finder,ProductFinder $productFinder, CreateMergeNoFromLabelUpdater $updater,
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
        $user_id=$data["user_id"];
        $label_no = $data["label_no"];

        $params["label_no"]=$label_no;

        $rtdata['labels']=$this->finder->findCreateMergeNoFromLabels($params);

        $countlabel=count($rtdata["labels"]);
        // $sumqty=0;

        for($i=0; $i < $countlabel; $i++){
            if($rtdata["labels"][$i]['label_no']  == $label_no && $rtdata["labels"][$i]['label_type'] == "NONFULLY"){
                $productID = $rtdata["labels"][$i]['product_id'];
                $productCode = $rtdata["labels"][$i]['product_code'];

                $data1['merge_no']= "M".$productCode.str_pad($i, 5, "0", STR_PAD_LEFT); //สร้าง merge_no
                $data1['product_id']= $productID;
                $data1['merge_status']= "CREATED";

                $this->updater->insertMergePackApi($data1, $user_id);
                break;
            }

        }
        return $this->responder->withJson($response, $rtdata);
    }
}