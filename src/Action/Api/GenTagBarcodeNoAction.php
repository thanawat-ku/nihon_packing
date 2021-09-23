<?php

namespace App\Action\Api;

use App\Domain\Tag\Service\TagFinder;
use App\Domain\Tag\Service\TagUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class GenTagBarcodeNoAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;

    public function __construct(Twig $twig,TagFinder $finder, TagUpdater $updater,
    Session $session,Responder $responder)
    {
        $this->twig = $twig;
        $this->finder=$finder;
        $this->updater=$updater;
        $this->session=$session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        // $merge_status = (string)($data['merge_status'] ?? '');
        $sell_id = (int)($data['sell_id'] ?? 1);
        $quantity = (int)$data['quantity'] ?? 1;
        $std_box = (int)$data['std_box'] ?? 1;
        $user_id = (int)$data['user_id'] ?? 1;
        $num_tags=ceil($quantity/$std_box);
        $num_full_tags=floor($quantity/$std_box);

        $tags=[];
        for($i=0; $i < $num_full_tags; $i++){
            $data1['sell_id']=$sell_id;
            $data1['tag_no']="X".str_pad( $i, 11, "0", STR_PAD_LEFT);
            $data1['quantity']=$std_box;
            $data1['status']="CREATED";
            $id=$this->updater->insertTagApi($data1,$user_id);
            $data1['tag_no']="T".str_pad( $id, 11, "0", STR_PAD_LEFT);
            $this->updater->updateTagApi($id, $data1,$user_id);
            $tag['id']=$id;
            $tag['tag_no']=$data1['tag_no'];
            $tag['quantity']=$data1['quantity'];
            array_push($tags,$tag);
        }
        if($num_full_tags!=$num_tags){
            $data1['sell_id']=$sell_id;
            $data1['tag_no']="X".str_pad( $i, 11, "0", STR_PAD_LEFT);
            $data1['quantity']=$quantity - ($num_full_tags * $std_box);
            $data1['status']="CREATED";
            $id=$this->updater->insertLabelApi($data1,$user_id);
            $data1['tag_no']="T".str_pad( $id, 11, "0", STR_PAD_LEFT);
            $this->updater->updateTagApi($id, $data1,$user_id);
            $tag['id']=$id;
            $tag['tag_no']=$data1['tag_no'];
            $tag['quantity']=$data1['quantity'];
            array_push($tags,$tag);
        }
        $rtdata['message']="Gen Tags Successful";
        $rtdata['error']=false;
        $rtdata['tags']=$tags;
        return $this->responder->withJson($response, $rtdata);
    }
}