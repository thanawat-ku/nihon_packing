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
final class GenSplitLabelBarcodeNoAction
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
        $label_type = $data['label_type'] ?? "FULLY";
        $lot_id = (int)($data['lot_id'] ?? 1);
        $merge_pack_id = (int)($data['merge_pack_id'] ?? 1);
        $quantity1 = (int)$data['quantity1'] ?? 1;
        $quantity2 = (int)$data['quantity2'] ?? 1;
        $user_id = (int)$data['user_id'] ?? 1;

        $labels=[];
        if($label_type=="FULLY"||$label_type=="NONFULLY"){
            $data1['lot_id']=$lot_id;
            $data1['label_no']="X".str_pad( $i, 11, "0", STR_PAD_LEFT);
            $data1['label_type']="NONFULLY";
            $data1['quantity']=$quantity1;
            $data1['status']="CREATED";
            $id=$this->updater->insertLabelApi($data1,$user_id);
            $data1['label_no']="P".str_pad( $id, 11, "0", STR_PAD_LEFT);
            $this->updater->updateLabelApi($id, $data1,$user_id);
            $label['id']=$id;
            $label['label_no']=$data1['label_no'];
            $label['label_type']=$data1['label_type'];
            $label['quantity']=$data1['quantity'];
            array_push($labels,$label);

            $data1['lot_id']=$lot_id;
            $data1['label_no']="X".str_pad( $i, 11, "0", STR_PAD_LEFT);
            $data1['label_type']="NONFULLY";
            $data1['quantity']=$quantity2;
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
        else if($label_type=="MERGE_FULLY"||$label_type=="MERGE_NONFULLY"){
            $data1['merge_pack_id']=$merge_pack_id;
            $data1['label_no']="X".str_pad( $i, 11, "0", STR_PAD_LEFT);
            $data1['label_type']="MERGE_NONFULLY";
            $data1['quantity']=$quantity1;
            $data1['status']="CREATED";
            $id=$this->updater->insertLabelApi($data1,$user_id);
            $data1['label_no']="P".str_pad( $id, 11, "0", STR_PAD_LEFT);
            $this->updater->updateLabelApi($id, $data1,$user_id);
            $label['id']=$id;
            $label['label_no']=$data1['label_no'];
            $label['label_type']=$data1['label_type'];
            $label['quantity']=$data1['quantity'];
            array_push($labels,$label);

            $data1['merge_pack_id']=$merge_pack_id;
            $data1['label_no']="X".str_pad( $i, 11, "0", STR_PAD_LEFT);
            $data1['label_type']="MERGE_NONFULLY";
            $data1['quantity']=$quantity2;
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
        $rtdata['message']="Gen Split Labels Successful";
        $rtdata['error']=false;
        $rtdata['labels']=$labels;
        return $this->responder->withJson($response, $rtdata);
    }
}