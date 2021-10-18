<?php

namespace App\Action\Api;

use App\Domain\SellLabel\Service\SellLabelFinder;
use App\Domain\SellLabel\Service\SellLabelUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class CancelSellLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $findermpd;
    private $updater;
    private $upmergepackdetail;

    public function __construct(SellLabelFinder $finder,SellLabelUpdater $updater,
    Responder $responder,)
    {
        $this->finder=$finder;
        $this->updater=$updater;
        $this->responder = $responder;

       
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $SellLabelID=$data['id'];
        
        $this->updater->deleteLabelInSellLabel($SellLabelID);
        
        return $this->responder->withJson($response, $data);
    }
}