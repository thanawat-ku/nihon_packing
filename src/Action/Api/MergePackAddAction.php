<?php

namespace App\Action\Api;

use App\Domain\MergePack\Service\MergePackFinder;
use App\Domain\MergePack\Service\MergePackUpdater;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class MergePackAddAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;

    public function __construct(Twig $twig,MergePackFinder $finder,ProductFinder $productFinder, MergePackUpdater $updater,
    Session $session,Responder $responder)
    {
        $this->twig = $twig;
        $this->finder=$finder;
        $this->updater=$updater;
        $this->productFinder=$productFinder;
        $this->session=$session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getParsedBody();
        $user_id=$params["user_id"];
        $product_id=$params["product_id"];
        $merge_status=$params["merge_status"];
        
        //ทำการสร้าง merge_no ขึ้นมาก่อน เเต่ merge_no ที่สร้างขึ้นมาใหม่นั้นนังไม่สามารถดึง product_code มาได้
        //หลังจากที่สร้างเสร็จแล้จึง update merge_no
        $rtdata['merge_packs']=$this->finder->findMergePacks($params); //ดึงข้อมูลมาจากฐานข้อมูล

        $countmerge=count($rtdata["merge_packs"]); //หาจำนวน array
        $arraynumnow = $countmerge - 1; //กำหนดตัวแปรเพื่อหา array สุดท้าย
        $araynumfu = $arraynumnow + 1; //กำหนดตัวแปรเพื่อหา array สุดท้าย สำหรับ update merge_no
        
        $mpafterID = $rtdata["merge_packs"][$arraynumnow]['id']; //ดึงค่า id ของ array สุดท้าย
        $mergeID = $mpafterID + 1; 
        
        //insert data in merge_packs
        $data1['merge_no']= "M".str_pad($mergeID, 5, "0", STR_PAD_LEFT); //สร้าง merge_no
        $data1['product_id']= $product_id;
        $data1['merge_status']= $merge_status;

        $this->updater->insertMergePackApi($data1, $user_id);

        //หลังจากสร้าง merge_no เสร็จ เเล้วก็ update merge_no ที่ได้ product_code เข้าไปอยู่ใน merge_no ที่สร้างขึ้น
        $rtdata['merge_packs']=$this->finder->findMergePacks($params);

        $product_code = $rtdata["merge_packs"][$araynumfu]['product_code'];//update merge_no
        $data1['merge_no']= "M".$product_code.str_pad($mergeID, 5, "0", STR_PAD_LEFT);
        
        $this->updater->updateMergePackApi($mergeID, $data1, $user_id);
       
        return $this->responder->withJson($response, $rtdata);

        

    }
}