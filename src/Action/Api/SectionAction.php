<?php

namespace App\Action\Api;

use App\Domain\Section\Service\SectionFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class SectionAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $sectionFinder;
    

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(SectionFinder $sectionFinder,Responder $responder)
    {
        
        $this->sectionFinder=$sectionFinder;
        $this->responder = $responder;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $params = (array)$request->getQueryParams();
        
        $rtdata['message']="Get Section Successful";
        $rtdata['error']=false;
        $rtdata['sections']=$this->sectionFinder->findSections($params);

        return $this->responder->withJson($response, $rtdata);

    }
}