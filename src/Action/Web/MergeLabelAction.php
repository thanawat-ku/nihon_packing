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
final class MergeLabelAction
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
        $mergePack = $this->finder->findMergePacks($params);

        $productId['product_id'] = $mergePack[0]['product_id'];

        $getLabels = $this->labelFinder->findLabelForMerge($productId);

       
        if (isset($getLabels[0])) {
            $labels = [];
            for($i=0;$i<sizeof($getLabels);$i++){
                if($getLabels[$i]['merge_pack_id'] != "0"){
                    $mergePackId['id'] = $getLabels[$i]['merge_pack_id'];
                    $mergePack2 = $this->finder->findMergePacks($mergePackId);
                    $getLabels[$i]['merge_no'] = $mergePack2[0]['merge_no'];
                }
                $getLabels[$i]['from_merge_id'] = $mergePack[0]['id'];
                array_push($labels,  $getLabels[$i]);
            }

        }

        $viewData = [
            'labels' =>  $getLabels, //Focus that!!!!!!
            'mergePack' => $mergePack[0],
            'user_login' => $this->session->get('user'),
        ];


        return $this->twig->render($response, 'web/labelsMerge.twig', $viewData); //-----edit twig
    }
}
