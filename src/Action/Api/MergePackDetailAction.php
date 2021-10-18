<?php

namespace App\Action\Api;

use App\Domain\MergePackDetail\Service\MergePackDetailFinder;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class MergePackDetailAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;

    public function __construct(Twig $twig,MergePackDetailFinder $finder,ProductFinder $productFinder,
    Session $session,Responder $responder)
    {
        $this->twig = $twig;
        $this->finder=$finder;
        $this->productFinder=$productFinder;
        $this->session=$session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        
        $rtdata['message']="Get MergePackDetail Successful";
        $rtdata['error']=false;

        $rtdata['mpd_from_lots']=$this->finder->findMergePackDetailFromLots($params);
        $rtdata['mpd_from_merges']=$this->finder->findMergePackDetailFromMergePacks($params);

        if ($rtdata['mpd_from_lots'] != null && $rtdata['mpd_from_merges'] == null) {
            $rtdata['check_label_from_mpd']="lot";
        }else if($rtdata['mpd_from_merges'] != null &&  $rtdata['mpd_from_lots'] == null){
            $rtdata['check_label_from_mpd']="merge";

        }else if($rtdata['mpd_from_lots'] != null && $rtdata['mpd_from_merges'] != null) {
            $rtdata['check_label_from_mpd']="lot_and_merge";
        }

        return $this->responder->withJson($response, $rtdata);  

    }
}