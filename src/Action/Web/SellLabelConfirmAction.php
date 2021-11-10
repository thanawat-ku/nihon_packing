<?php

namespace App\Action\Web;

use App\Domain\SellLabel\Service\SellLabelFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use PhpParser\Node\Stmt\Label;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class SellLabelConfirmAction
{
    private $responder;
    private $finder;
    private $updater;
    private $labelUpdater;
    private $session;

    public function __construct(Responder $responder,SellLabelFinder $finder,  SellUpdater $updater,LabelUpdater $labelUpdater, Session $session)
    {
        $this->responder = $responder;
        $this->finder=$finder;
        $this->updater = $updater;
        $this->labelUpdater = $labelUpdater;
        $this->session=$session;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $sellID=(int)$data['sell_id'];

        $user_id=$this->session->get('user')["id"];

        $data['up_status']="SELECTED_LABEL";
        $this->updater-> updateSellStatus($sellID, $data,$user_id);
        $arrSellLabel = $this->finder->findSellLabels($data);
        $dataUpdate['up_status']="USED";
        $user_id=$this->session->get('user')["id"];
        for ($i=0; $i < count($arrSellLabel); $i++) { 
            $this->labelUpdater->updateLabelStatus($arrSellLabel[$i]['label_id'],$dataUpdate, $user_id);
        }

        return $this->responder->withRedirect($response,"sells");
    }
}