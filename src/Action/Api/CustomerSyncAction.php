<?php

namespace App\Action\Api;

use App\Domain\Customer\Service\CustomerFinder;
use App\Domain\Customer\Service\CustomerUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

final class CustomerSyncAction
{
    /**
     * @var Responder
     */ 
    private $responder;
    private $finder;
    private $updater;
    private $twig;

    public function __construct(
        Twig $twig,
        CustomerFinder $finder,
        CustomerUpdater $updater,
        Responder $responder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();

        $max_id=$this->finder->getLocalMaxCustomerId();

        $customers = $this->finder->getSyncCustomers($max_id);
        $rtData=[];
        for($i=0;$i<count($customers);$i++)
        {
            $params1['id']=$customers[$i]["CustomerID"];
            $params1['customer_code']=$customers[$i]["CustomerCode"];
            $params1['customer_name']=$customers[$i]["CustomerName"];
            $params1['address1']=$customers[$i]["Address1"];
            $params1['address2']=$customers[$i]["Address2"];
            $params1['address3']=$customers[$i]["Address3"];
            $this->updater->insertCustomer($params1);
            $rtData=[];
            array_push($rtData, $customers[$i]);
        }

        return $this->responder->withJson($response, $rtData);
    }
}