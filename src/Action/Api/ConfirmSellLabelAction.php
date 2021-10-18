<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\SellLabel\Service\SellLabelFinder;
use App\Domain\Sell\Service\SellFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Responder\Responder;
use PhpParser\Node\Stmt\Label;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class ConfirmSellLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;
    private $updatelabel;
    private $findersell;

    public function __construct(
        Twig $twig,
        SellLabelFinder $finder,
        LabelUpdater $updatelabel,
        LabelFinder $finderlabel,
        SellFinder $findersell,
        Session $session,
        Responder $responder,
        SellUpdater $updater,
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->finderlabel = $finderlabel;
        $this->updatelabel = $updatelabel;
        $this->findersell = $findersell;
        $this->updater = $updater;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $user_id=(int)$data['user_id'];
        $SellID =(int)$data['sell_id'];

        $sellLabel=$this->finder->findSellLabels($data);

        for ($i=0; $i < count($sellLabel); $i++) { 
            $labelID=(int)$sellLabel[$i]['label_id'];
            $dataUpdate['up_status']="USED";
            $this->updatelabel->updateLabelStatus($labelID, $dataUpdate, $user_id);
        }

        $this->updater->updateSellSelectedLabelApi($SellID, $data, $user_id);
        $allData=[''];
        

        $rtdata['message']="Get Sell Successful";
        $rtdata['error']=false;
        $rtdata['sells']=$this->findersell->findSells($allData);
        
        return $this->responder->withJson($response, $rtdata);
    }
}
