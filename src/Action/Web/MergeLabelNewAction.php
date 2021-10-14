<?php

namespace App\Action\Web;

use App\Domain\MergePack\Service\MergePackFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Domain\Product\Service\ProductFinder;
use App\Domain\Label\Service\LabelFinder;

/**
 * Action.
 */
final class MergeLabelNewAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $session;
    private $productFinder;
    private $labelFinder;


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
        ProductFinder $productFinder,
        LabelFinder $labelFinder,
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->session = $session;
        $this->responder = $responder;
        $this->productFinder = $productFinder;
        $this->labelFinder = $labelFinder;
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
        $params = (array)$request->getQueryParams();
        $mergePackId =  $params['id'];

        $dataLabel['merge_pack_id'] = $mergePackId;

        $labels = $this->labelFinder->findLabelForLotZero($dataLabel);

        $mergePack['id'] = $mergePackId;
        $mergePack['merge_no'] = $labels[0]['merge_no'];

        $viewData = [
            'labels' =>  $labels,
            'mergePack' => $mergePack,
            'user_login' => $this->session->get('user'),
        ];


        return $this->twig->render($response, 'web/labelsMergeNew.twig', $viewData); //-----edit twig
    }
}
