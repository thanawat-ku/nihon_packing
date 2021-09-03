<?php

namespace App\Action\Web;

use App\Domain\Label\Service\LabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Domain\Lot\Service\LotFinder;

/**
 * Action.
 */
final class LabelAddAction
{
    private $responder;
    private $updater;
    private $finder;

    public function __construct(Responder $responder, LabelUpdater $updater,LotFinder $finder)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        // Extract the form data from the request body

        $data = (array)$request->getParsedBody();

        //find lotID from lot_no 
        $lotNo = $data['lot_no'];
        $params['lot_no']=$lotNo;
        $lotsIdFind = $this->finder->findLots($params);
        $data['lot_id'] = $lotsIdFind[0]['id'];

        // Invoke the Domain with inputs and retain the result
        $this->updater->insertLabel( $data,);

        // Build the HTTP response
        return $this->responder->withRedirect($response,"labels");
    }
}
