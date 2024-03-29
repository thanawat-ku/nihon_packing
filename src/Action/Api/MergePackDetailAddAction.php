<?php

namespace App\Action\Api;

use App\Domain\MergePackDetail\Service\MergePackDetailFinder;
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
final class MergePackDetailAddAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;
    private $finderlabel;
    private $twig;
    private $productFinder;
    private $session;

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
        $user_id=$data["user_id"];
        // $merge_pack_id=$data["merge_pack_id"];
        // $label_no=$data["label_no"];

        // $rtdata=$this->finder->findLabelNonfully($data);
        // $data['label_id']=$rtdata[0]['id'];

        $this->updater->insertMergePackDetailApi($data, $user_id);

        // $data['label_id']=$id;
        // $this->updater->updateMergePackDetailApi($id, $data, $user_id);
        



        // $rtdata['message']="Get MergePackDetail Successful";
        // $rtdata['error']=false;
        // $rtdata['labels']=$this->finder->findLabelNonfully($data);
        

        // $countlabel = count($rtdata["labels"]);

        // for ($i=0; $i < $countlabel; $i++) {
        //     if ($rtdata["labels"][$i]['merge_pack_id'] == $merge_pack_id && $rtdata["labels"][$i]['label_no'] == $label_no) {
        //         $data1['label_id']=$rtdata["labels"][$i]['id'];
        //         $data1['merge_pack_id'] = $merge_pack_id; 
        //         // $data1['status']="MERGING";

        //         $this->updater->insertMergePackDetailApi($data1, $user_id);
        //         break;
        //     }
        // }
        return $this->responder->withJson($response, $data);

    }
}