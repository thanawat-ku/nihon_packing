<?php

namespace App\Action\Web;

use App\Domain\MergePack\Service\MergePackFinder;
use App\Domain\MergePack\Service\MergePackUpdater;
use App\Domain\MergePackDetail\Service\MergePackDetailUpdater;
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
final class MergeLabelAddAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $updater;
    private $session;
    private $mergeDetailUpdater;
    private $labelUpdater;

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
        MergePackDetailUpdater $mergeDetailUpdater,
        LabelUpdater $labelUpdater,
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->session = $session;
        $this->responder = $responder;
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
        $mergeId = $data['merge_pack_id'];
        $labelId = $data['label_id'];

        $this->mergeDetailUpdater->insertMergePackDetail($data);
        
        $dataLabel['status'] = "MERGING";
        $this->labelUpdater->updateLabel($labelId, $dataLabel);
        $data2['merge_status'] = "MERGING";
        $this->updater->updatePackMerge($mergeId,$data2);

        $viewData = [
            'id' => $mergeId,
        ];

        return $this->responder->withRedirect($response, "label_merges", $viewData);
    }
}
