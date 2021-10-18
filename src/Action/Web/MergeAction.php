<?php

namespace App\Action\Web;

use App\Domain\MergePack\Service\MergePackFinder;
use App\Domain\MergePackDetail\Service\MergePackDetailFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Domain\Product\Service\ProductFinder;

/**
 * Action.
 */
final class MergeAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $session;
    private $productFinder;
    private $mergeDetailFinder;

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
        MergePackDetailFinder $mergeDetailFinder,
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->session = $session;
        $this->responder = $responder;
        $this->productFinder = $productFinder;
        $this->mergeDetailFinder = $mergeDetailFinder;
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

        if (!isset($params['startDate'])) {
            $params['startDate'] = date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))));
            $params['endDate'] = date('Y-m-d');
        }

        $mergePack = $this->finder->findMergePacks($params);

        for($i=0;$i<sizeof($mergePack);$i++){
            if($mergePack[$i]['status'] = "MERGING"){
                $mergePackId['merge_pack_id']=  $mergePack[$i]['id'];
                $gerMergeDetail = $this->mergeDetailFinder->findMergePackDetailsForMerge($mergePackId);

                if(isset($gerMergeDetail[1])){
                    $mergePack[$i]['merge_confirm'] = "Y";
                }
                else{
                    $mergePack[$i]['merge_confirm'] = "N";
                }
            }
        }

        $viewData = [
            'mergePacks' => $mergePack, 
            'products' => $this->productFinder->findProducts($params),
            'user_login' => $this->session->get('user'),
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
        ];


        return $this->twig->render($response, 'web/merges.twig', $viewData); //-----edit twig
    }
}
