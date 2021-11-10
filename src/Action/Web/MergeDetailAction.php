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

        if (($mergePack[0]['merge_status'] == "CREATED") or ($mergePack[0]['merge_status'] == "MERGING")) {
            $mergePack[0]['select_label'] = "Y";
        } else {
            $mergePack[0]['select_label'] = "N";
        }


        $labels = [];
        if (isset($mergeDetail[0])) {
            for ($i = 0; $i < sizeof($mergeDetail); $i++) {
                $labelId['label_id'] = $mergeDetail[$i]['label_id'];
                $label = $this->labelFinder->findLabels($labelId);

                if(!isset($label[0])){
                    $label = $this->labelFinder->findLabelForLotZero($labelId);
                }
                array_push($labels, $label[0]);
            }
        }

        if(isset($mergeDetail[1])){
            $mergePack[0]['merge_confirm'] = "Y";
        }
        else{
            $mergePack[0]['merge_confirm'] = "N";
        }

        $viewData = [
            'labels' => $labels,
            'mergePack' => $mergePack[0],
            'user_login' => $this->session->get('user'),
        ];


        return $this->twig->render($response, 'web/mergeDetail.twig', $viewData);
    }
}
