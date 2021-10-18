<?php

namespace App\Action\Web;

use App\Domain\MergePack\Service\MergePackFinder;
use App\Domain\MergePack\Service\MergePackUpdater;
use App\Domain\MergePackDetail\Service\MergePackDetailFinder;
use App\Domain\MergePackDetail\Service\MergePackDetailUpdater;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * Action.
 */
final class MergeDeleteAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $updater;
    private $session;
    private $mergeDetailFinder;
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
        MergePackDetailFinder $mergeDetailFinder,
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->session = $session;
        $this->responder = $responder;
        $this->mergeDetailFinder = $mergeDetailFinder;
        $this->mergeDetailUpdater = $mergeDetailUpdater;
        $this->labelUpdater = $labelUpdater;
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

        $data2['merge_pack_id'] = $data['id'];
        $mergeDetail = $this->mergeDetailFinder->findMergePackDetailsForMerge($data2);

        if (isset($mergeDetail[0])) {
            for ($i = 0; $i < sizeof($mergeDetail); $i++) {
                $labelId = $mergeDetail[$i]['label_id'];
                $dataLabel['status'] = "PACKED";
                $this->labelUpdater->updateLabel($labelId, $dataLabel);
            }
        }

        $this->mergeDetailUpdater->deleteMergePackDetail($mergeId);
        $this->updater->deleteMergePack($mergeId);

        return $this->responder->withRedirect($response, "merges");
    }
}