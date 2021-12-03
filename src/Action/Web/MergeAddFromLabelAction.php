<?php

namespace App\Action\Web;

use App\Domain\MergePack\Service\MergePackFinder;
use App\Domain\MergePack\Service\MergePackUpdater;
use App\Domain\MergePackDetail\Service\MergePackDetailUpdater;
use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Domain\Product\Service\ProductFinder;

/**
 * Action.
 */
final class MergeAddFromLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $updater;
    private $session;
    private $labelFinder;
    private $labelUpdater;
    private $mergeDetailUpdater;

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(
        Twig $twig,
        MergePackFinder $finder,
        Session $session,
        Responder $responder,
        MergePackUpdater  $updater,
        LabelFinder $labelFinder,
        LabelUpdater $labelUpdater,
        MergePackDetailUpdater $mergeDetailUpdater,

    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->session = $session;
        $this->responder = $responder;
        $this->labelFinder = $labelFinder;
        $this->labelUpdater = $labelUpdater;
        $this->mergeDetailUpdater = $mergeDetailUpdater;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();


        $label = $this->labelFinder->findLabelSingleTable($data);

        if (
            isset($label[0]) && $label[0]['status'] == "PACKED" &&
            ($label[0]['label_type'] == "NONFULLY" || $label[0]['label_type'] == "MERGE_NONFULLY")
        ) {
            $user_id = $this->session->get('user')["id"];
            $dataMerge['merge_status'] = "MERGING";
            $dataMerge['product_id'] = $label[0]['product_id'];
            $mergeId = $this->updater->insertMergePackApi($dataMerge, $user_id);
            $labeId = $label[0]['id'];
            $dataMergeDetail['label_id'] = $labeId;
            $dataMergeDetail['merge_pack_id'] = $mergeId;
            $this->mergeDetailUpdater->insertMergePackDetail($dataMergeDetail);
            $dataLabel['status'] = "MERGING";
            $this->labelUpdater->updateLabel($labeId, $dataLabel);
            $viewData = [
                'id' => $mergeId,
            ];
        } else {
            $viewData = [
                'None_label' => "Error",
            ];
        }
        // return $this->responder->withRedirect($response, "merges");
        return $this->responder->withRedirect($response, "merge_detail", $viewData);
    }
}
