<?php

namespace App\Action\Api;

use App\Domain\MergePack\Service\MergePackFinder;
use App\Domain\MergePack\Service\MergePackUpdater;
use App\Domain\MergePackDetail\Service\MergePackDetailFinder;
use App\Domain\MergePackDetail\Service\MergePackDetailUpdater;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function DI\string;

/**
 * Action.
 */
final class MergePackDeleteAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;
    private $findMergePackDetail;
    private $updateMergePackDetail;
    private $updateLabel;

    public function __construct(
        MergePackFinder $finder,
        MergePackUpdater $updater,
        MergePackDetailFinder $findMergePackDetail,
        MergePackDetailUpdater $updateMergePackDetail,
        LabelUpdater $updateLabel,
        Responder $responder
    ) {
        $this->finder = $finder;
        $this->updater = $updater;
        $this->findMergePackDetail = $findMergePackDetail;
        $this->updateMergePackDetail = $updateMergePackDetail;
        $this->updateLabel = $updateLabel;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $mergePackID = $data['merge_pack_id'];
        $user_id = $data["user_id"];

        $trMergePack = $this->finder->findMergePacks($data);

        if ($trMergePack[0]['merge_status'] == "MERGING" || $trMergePack[0]['merge_status'] == "CREATED") {
           
            $rtMergePackDetail = $this->findMergePackDetail->findMergePackDetails($data);

            $upStatusLabel['status'] = "PACKED";
            for ($i = 0; $i < count($rtMergePackDetail); $i++) {
                $labelID = $rtMergePackDetail[$i]['label_id'];
                $this->updateLabel->updateLabel($labelID, $upStatusLabel);
            }
            $this->updateMergePackDetail->deleteMergePackDetail($mergePackID);

            $data['is_delete'] = "Y";
            $this->updater->updateMergePackApi($mergePackID, $data, $user_id);

            // $data['find_is_delete'] = 'Y'; 
            // $rtdata = $this->finder->findMergePacks($data);

            return $this->responder->withJson($response, $trMergePack[0]);
        }else{
            return $this->responder->withJson($response, null);
        }
    }
}
