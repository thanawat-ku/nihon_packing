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
final class GenLabelBarcodeNoAction
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
        $lot_id = (int)($data['lot_id'] ?? 1);
        $real_qty = (int)$data['real_qty'] ?? 1;
        $std_pack = (int)$data['std_pack'] ?? 1;
        $user_id = (int)$data['user_id'] ?? 1;
        $num_packs=ceil($real_qty/$std_pack);
        $num_full_packs=floor($real_qty/$std_pack);

        $labels=[];
        for($i=0; $i < $num_full_packs; $i++){
            $data1['lot_id']=$lot_id;
            $data1['label_no']="X".str_pad( $i, 11, "0", STR_PAD_LEFT);
            $data1['label_type']="FULLY";
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
            $data1['lot_id']=$lot_id;
            $data1['label_no']="X".str_pad( $i, 11, "0", STR_PAD_LEFT);
            $data1['label_type']="NONFULLY";
            $data1['quantity']=$real_qty - ($num_full_packs * $std_pack);
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

        $labelNO['lot_id'] = $data['lot_id'];
        $findlabel = $this->finder->findLabels($labelNO);

        // $rtdata['message']="Gen Labels Successful";
        // $rtdata['error']=false;
        // $rtdata['labels']=$labels;
        $rtdata['message']="Gen Labels Successful";
        $rtdata['error']=false;
        $rtdata['labels']=$findlabel;

        return $this->responder->withJson($response, $rtdata);
    }
}