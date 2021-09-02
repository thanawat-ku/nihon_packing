<?php

namespace App\Action\Api;

use App\Domain\LabelPackMerge\Service\LabelPackMergeFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class GetQtyMpLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;

    public function __construct(Twig $twig,LabelPackMergeFinder $finder,ProductFinder $productFinder, 
    Session $session,Responder $responder)
    {
        $this->twig = $twig;
        $this->finder=$finder;
        // $this->updater=$updater;
        $this->productFinder=$productFinder;
        $this->session=$session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $mrege_pack_id = (int)($data['merge_pack_id'] ?? '');

        $label = $this->finder->findLabelPackMerges($data);
        
        $countlb = count($label);
        $sum_qty = 0;
        $pack_qty = 0;

        for($i=0; $i<$countlb;$i++) {
            if($label[$i]['merge_pack_id'] == $mrege_pack_id && ($label[$i]['label_type'] == "MERGE_FULLY" || $label[$i]['label_type'] == "MERGE_NONFULLY" )){
                $qty = $label[$i]['quantity'];
                $sum_qty += $qty;
                if($label[$i]['status'] == "PACKED"){
                    $packqty = $label[$i]['quantity'];
                    $pack_qty += $packqty;
                }
            }
        }
        $array_labels = array($sum_qty, $pack_qty,);

        $rtdata['labels'] = $array_labels;

        return $this->responder->withJson($response, $rtdata);
    }
}