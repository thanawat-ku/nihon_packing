<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class UpStatusMergedAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;

    public function __construct(Twig $twig,LabelFinder $finder, LabelUpdater $updater,
    Session $session,Responder $responder)
    {
        
        $this->finder=$finder;
        $this->updater=$updater;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getParsedBody();
        $user_id=$params["user_id"];
        $id=$params["id"];
        $labelNO=$params["label_no"];
        $mergepackid=$params["merge_pack_id"];

        $rtdata=$this->finder->findLabelNonfullys($params);
        $countlabel=count($rtdata);

        if($id == 0 && $mergepackid != 0){
            for ($i=0; $i < $countlabel; $i++) { 
                if ($labelNO == $rtdata[$i]['label_no']) {
                    $id = $rtdata[$i]['id'];
                    break;
                }
            }
        }
       
        $this->updater->updateLabelApi($id, $params, $user_id);

        $rtdata['message']="Get MergePack Successful";
        $rtdata['error']=false;
        $rtdata['labels']=$this->finder->findLabelNonfullys($params);

        return $this->responder->withJson($response, $rtdata);

        

    }
}