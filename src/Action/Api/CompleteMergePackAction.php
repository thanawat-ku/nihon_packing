<?php

namespace App\Action\Api;

use App\Domain\MergePack\Service\MergePackFinder;
use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\MergePack\Service\MergePackUpdater;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

use function DI\string;

/**
 * Action.
 */
final class CompleteMergePackAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $finderlabel;
    private $updater;
    private $updatelabel;

    public function __construct(MergePackFinder $finder,ProductFinder $productFinder, MergePackUpdater $updater,
    Session $session,Responder $responder, LabelFinder $finderlabel, LabelUpdater $updatelabel)
    {
        $this->finder=$finder;
        $this->finderlabel=$finderlabel;
        $this->updater=$updater;
        $this->updatelabel=$updatelabel;
        $this->productFinder=$productFinder;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $user_id=$data['user_id'];
        $mergepackID=$data['merge_pack_id'];
        $label = $data['labels'];
        
        $arrlabel = explode("#", $label);

        for ($i = 1; $i < count($arrlabel); $i++) {
            $label_no = $arrlabel[$i];
            $data1['label_no'] = explode(",", $label_no)[0];
            $label_row = $this->finderlabel->findLabelSingleTable($data1);
            $labelID=(int)$label_row[0]['id'];

            $dataUpdate['up_status'] = "PACKED"; 
            $this->updatelabel->updateLabelStatusPackedApi($labelID, $dataUpdate, $user_id);
        }
        $this->updater->updateCompleteApi($mergepackID, $label_row, $user_id);
        
        $allData=[''];

        $rtdata['message']="Get MergePack Successful";
        $rtdata['error']=false;
        $rtdata['merge_packs']=$this->finder->findMergePacks($allData);

        return $this->responder->withJson($response, $rtdata);
    }
}