<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\SellLabel\Service\SellLabelUpdater;
use App\Domain\MergePackDetail\Service\MergePackDetailFinder;
use App\Responder\Responder;
use PhpParser\Node\Stmt\Label;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

use function DI\string;

/**
 * Action.
 */
final class CheckLabelScanRegisMergeAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;
    private $updatesell;
    private $updateselllabel;
    private $mergepackDetailFinder;

    public function __construct(
        Twig $twig,
        LabelFinder $finder,
        SellUpdater $updatesell,
        LabelUpdater $updater,
        Session $session,
        Responder $responder,
        SellLabelUpdater $updateselllabel,
        MergePackDetailFinder $mergepackDetailFinder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->updatesell = $updatesell;
        $this->updateselllabel = $updateselllabel;
        $this->session = $session;
        $this->responder = $responder;
        $this->mergepackDetailFinder = $mergepackDetailFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $label = $data['label'];
        
        $arrlabel = explode("#", $label);

        $rtdata['mpd_from_lots']=[];
        $rtdata['mpd_from_merges'] =[];

        for ($i = 1; $i < count($arrlabel); $i++) {
            $label_no = $arrlabel[$i];
            $data1['label_no'] = explode(",", $label_no)[0];
            $label_row = $this->finder->findLabelSingleTable($data1);
            $labelID['id']=$label_row[0]['id'];

            if ($label_row != null) {
                if ($label_row[0]['merge_pack_id'] == 0) {
                    if ($label_row[0]['lot_id'] != 0) {
                        $rtdata['message'] = "Get Label Successful";
                        $rtdata['error'] = false;
                        $labelLots = $this->finder->findCreateMergeNoFromLabels($labelID);
                        array_push($rtdata['mpd_from_lots'], $labelLots[0]);
                    } 
                } 
                else {
                    $rtdata['message'] = "Get Label Successful";
                    $rtdata['error'] = false;
                    $labelMergePacks = $this->finder->findLabelFromMergePacks($labelID);
                    array_push($rtdata['mpd_from_merges'], $labelMergePacks[0]);
                    
                }
            } else {
                $rtdata['message'] = "Get Label Successful";
                $rtdata['error'] = true;
                break;
            }
        }

        return $this->responder->withJson($response, $rtdata);
    }
}
