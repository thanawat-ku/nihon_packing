<?php

namespace App\Action\Web;

use App\Domain\MergePackDetail\Service\MergePackDetailFinder;
use App\Domain\MergePack\Service\MergePackFinder;
use App\Domain\Label\Service\LabelFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class MergeDetailAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $session;
    private $finder;
    private $mergeFinder;
    private $labelFinder;
    private $mergePackDetailFinder;

    public function __construct(
        Twig $twig,
        MergePackDetailFinder $finder,
        Session $session,
        Responder $responder,
        MergePackFinder $mergeFinder,
        LabelFinder $labelFinder,
        MergePackDetailFinder $mergePackDetailFinder,
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->mergeFinder = $mergeFinder;
        $this->session = $session;
        $this->responder = $responder;
        $this->labelFinder = $labelFinder;
        $this->mergePackDetailFinder = $mergePackDetailFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        $data1['merge_pack_id'] = $params['id'];
        $mergeDetail = $this->finder->findMergePackDetailsForMerge($data1);
        $mergePack =  $this->mergeFinder->findMergePacks($data1);

        
        $labels = [];
        if(isset($mergeDetail[0])){
            for($i=0;$i<sizeof($mergeDetail);$i++){
                $labelId['label_id'] = $mergeDetail[$i]['label_id'];
                $label = $this->labelFinder->findLabels($labelId);
                if($label[0]['merge_pack_id'] != "0"){
                    $data2['merge_pack_id'] = $label[0]['merge_pack_id'];
                    $mergePack2 = $this->mergeFinder->findMergePacks($data2);
                    $label[0]['merge_no'] = $mergePack2[0]['merge_no'];
                }
                array_push($labels, $label[0]);
            }
        }
        
        
        // $mergeNo = $mergePack[0]['merge_no'];

        $viewData = [
            'labels' => $labels,
            'mergePack' => $mergePack[0] ,
            'user_login' => $this->session->get('user'),
        ];


        return $this->twig->render($response, 'web/mergeDetail.twig', $viewData); 
    }
}
