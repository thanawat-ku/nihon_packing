<?php

namespace App\Action\Web;

use App\Domain\MergePackDetail\Service\MergePackDetailFinder;
use App\Domain\MergePack\Service\MergePackFinder;
use App\Domain\Label\Service\LabelFinder;
use App\Domain\Printer\Service\PrinterFinder;
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
    private $printerFinder;

    public function __construct(
        Twig $twig,
        MergePackDetailFinder $finder,
        Session $session,
        Responder $responder,
        MergePackFinder $mergeFinder,
        LabelFinder $labelFinder,
        MergePackDetailFinder $mergePackDetailFinder,
        PrinterFinder $printerFinder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->mergeFinder = $mergeFinder;
        $this->session = $session;
        $this->responder = $responder;
        $this->labelFinder = $labelFinder;
        $this->mergePackDetailFinder = $mergePackDetailFinder;
        $this->printerFinder = $printerFinder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        if (isset($params['product_id'])) {
            $params['search_product_id'] = $params['product_id'];
        }
        if (!isset($params['search_status'])) {
            $params['search_status'] = "MERGING";
        }
        if (!isset($params['search_product_id'])) {
            $params['search_product_id'] = 2713;
            $params['search_status'] = 'CREATED';
        }

        if (!isset($params['None_label'])) {
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

                    if (!isset($label[0])) {
                        $label = $this->labelFinder->findLabelForLotZero($labelId);
                    }
                    array_push($labels, $label[0]);
                }
            }

            if (isset($mergeDetail[1])) {
                $mergePack[0]['merge_confirm'] = "Y";
            } else {
                $mergePack[0]['merge_confirm'] = "N";
            }
            $printerType['printer_type'] = "LABEL";
            $viewData = [
                'labels' => $labels,
                'mergePack' => $mergePack[0],
                'printers' => $this->printerFinder->findPrinters($printerType),
                'user_login' => $this->session->get('user'),
                'search_product_id' => $params['search_product_id'],
                'search_status' => $params['search_status'],
            ];
        } else {
            $mergePack2['merge_no'] = "Error";
            $labels = [];
            if (!isset($params['search_product_id'])) {
                $params['search_product_id'] = 2713;
                $params['search_status'] = 'CREATED';
            }
            $viewData = [
                'mergePack' => $mergePack2,
                'labels' => $labels,
                'user_login' => $this->session->get('user'),
                'search_product_id' => $params['search_product_id'],
                'search_status' => $params['search_status'],
            ];
        }

        return $this->twig->render($response, 'web/mergeDetail.twig', $viewData);
    }
}
