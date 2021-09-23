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
final class GenMergeLabelBarcodeNoAction
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
        $merge_pack_id = (int)($data['merge_pack_id'] ?? 1);
        $quantity = (int)$data['quantity'] ?? 1;
        $std_pack = (int)$data['std_pack'] ?? 1;
        $user_id = (int)$data['user_id'] ?? 1;
        $num_packs=ceil($quantity/$std_pack);
        $num_full_packs=floor($quantity/$std_pack);

        $labels=[];
        for($i=0; $i < $num_full_packs; $i++){
            $data1['merge_pack_id']=$merge_pack_id;
            $data1['label_no']="X".str_pad( $i, 11, "0", STR_PAD_LEFT);
            $data1['label_type']="MERGE_FULLY";
            $data1['quantity']=$std_pack;
            $data1['status']="CREATED";
            $id=$this->updater->insertLabelApi($data1,$user_id);
            $data1['label_no']="P".str_pad( $id, 11, "0", STR_PAD_LEFT);
            $this->updater->updateLabelApi($id, $data1,$user_id);
            $label['id']=$id;
            $label['label_no']=$data1['label_no'];
            $label['label_type']=$data1['label_type'];
            $label['quantity']=$data1['quantity'];
            array_push($labels,$label);
        }
        if($num_full_packs!=$num_packs){
            $data1['merge_pack_id']=$merge_pack_id;
            $data1['label_no']="X".str_pad( $i, 11, "0", STR_PAD_LEFT);
            $data1['label_type']="MERGE_NONFULLY";
            $data1['quantity']=$quantity - ($num_full_packs * $std_pack);
            $data1['status']="CREATED";
            $id=$this->updater->insertLabelApi($data1,$user_id);
            $data1['label_no']="P".str_pad( $id, 11, "0", STR_PAD_LEFT);
            $this->updater->updateLabelApi($id, $data1,$user_id);
            $label['id']=$id;
            $label['label_no']=$data1['label_no'];
            $label['label_type']=$data1['label_type'];
            $label['quantity']=$data1['quantity'];
            array_push($labels,$label);
        }
        $rtdata['message']="Gen Merge Labels Successful";
        $rtdata['error']=false;
        $rtdata['labels']=$labels;
        return $this->responder->withJson($response, $rtdata);
    }
}