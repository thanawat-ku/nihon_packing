<?php

namespace App\Action\Api;

use App\Domain\LotNonFullyPack\Service\LotNonFullyPackFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class LotNonFullyPackAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;

    public function __construct(
        Twig $twig,
        LotNonFullyPackFinder $finder,
        Session $session,
        Responder $responder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        $params['search_prefer_lot_id'] = true;
        $params['prefer_lot_id'] = $params['lot_id'];
        $user_id = $params['user_id'];

        $rtLabel = [];
        $mergeQty = 0;
        $rtLabelMerge = $this->finder->findLotNonFullyPacks($params);

        for ($i = 0; $i < count($rtLabelMerge); $i++) {
            if (($rtLabelMerge[0]['status'] == 'PACKED' || $rtLabelMerge[0]['status'] == 'MERGED') && $rtLabelMerge[0]['label_type'] == "NONFULLY") {
                //ค้นหาข้อมูลที่มี label_id ซ้ำกัน
                $seachData['label_id'] = $rtLabelMerge[0]['id'];
                $checkDupli = $this->finder->checkLabelInLotNonFullyPacks($seachData);

                //เช็คความถูกต้อง เเละส่งข้อมูลไปยัง app
                // ถ้าไม่มีข้อมูลที่ซ้ำกัน ให้ข้อมูลถูกต้อง
                if (!isset($checkDupli[0])) {
                    $rtLabelMerge[$i]['check_correct'] = true;
                } else {
                    $rtLabelMerge[$i]['check_correct'] = false;
                }
            } else {
                $rtLabelMerge[$i]['check_correct'] = false;
            }
            array_push($rtLabel, $rtLabelMerge[$i]);
        }

        //ทำการหาจำนวน quantity ของ label
        for ($j = 0; $j < count($rtLabelMerge); $j++) {
            $mergeQty += $rtLabelMerge[$j]['quantity'];
        }

        $rtdata['message'] = "Get LotNonFullyPack Successful";
        $rtdata['error'] = false;
        $rtdata['lot_non_fully_packs'] = $rtLabel;
        $rtdata['merge_qty'] = $mergeQty;

        return $this->responder->withJson($response, $rtdata);
    }
}
