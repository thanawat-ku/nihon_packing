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
use App\Domain\LabelVoidReason\Service\LabelVoidReasonFinder;

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
    private $voidReasonFinder;


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
        LabelVoidReasonFinder $voidReasonFinder,
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->session = $session;
        $this->responder = $responder;
        $this->productFinder = $productFinder;
        $this->labelFinder = $labelFinder;
        $this->voidReasonFinder = $voidReasonFinder;
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

        $data['merge_pack_id'] = $mergePackId;

        $labels = $this->labelFinder->findLabelForLotZero($data);

        $mergePack['id'] = $mergePackId;
        $mergePack['merge_no'] = $labels[0]['merge_no'];
        $mergePack['register'] = "N";

        $merge = $this->finder->findMergePacks($data);

        if($merge[0]['merge_status'] == "PRINTED"){
            $mergePack['register'] = "Y";
        }
        

        $viewData = [
            'labels' =>  $labels,
            'mergePack' => $mergePack,
            'void_reasons' => $this->voidReasonFinder->findLabelVoidReasonsForVoidLabel($params),
            'user_login' => $this->session->get('user'),
        ];


        return $this->twig->render($response, 'web/labelsMerge.twig', $viewData); //-----edit twig
    }
}
