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
final class MergeRegisterAction
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
        $mergeId = $data['id'];
        
        $dataLaebl['status'] = "PACKED";
        $this->labelUpdater->registerMerge($mergeId, $dataLaebl);

        $dataMergePack['merge_status'] = "COMPLETE";
        $this->updater->updatePackMerge($mergeId,$dataMergePack);

        
        return $this->responder->withRedirect($response, "merges");
    }
}
