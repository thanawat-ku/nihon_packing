<?php

namespace App\Action\Api;

use App\Domain\LotDefect\Service\LotDefectFinder;
use App\Domain\LotDefect\Service\LotDefectUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class LotDefectAddAction
{
    private $responder;
    private $updater;
    private $finder;

<<<<<<< HEAD

    public function __construct(Responder $responder,  LotDefectUpdater $updater, LotDefectFinder $finder)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder=$finder;
=======
    public function __construct(Responder $responder, LotDefectUpdater $updater, LotDefectFinder $finder)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
>>>>>>> tae
    }

    public function __invoke(
        ServerRequestInterface $request,
<<<<<<< HEAD
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
       
        $params = (array)$request->getParsedBody();
        $user_id=$params["user_id"];

        $this->updater->insertLotDefectApi($params, $user_id);

        $rtdata['message']="Get Lot Defect Successful";
=======
        ResponseInterface $response): ResponseInterface {
        // Extract the form data from the request body
        $params = (array)$request->getParsedBody();
        $user_id = $params["user_id"];
        // Invoke the Domain with inputs and retain the result
        $this->updater->insertLotDefect($params, $user_id);

        $rtdata['message']="Insert Lot Defect Successful";
>>>>>>> tae
        $rtdata['error']=false;
        $rtdata['lot_defects']=$this->finder->findLotDefects($params);
        
        return $this->responder->withJson($response, $rtdata);
    }
}
<<<<<<< HEAD

=======
>>>>>>> tae
