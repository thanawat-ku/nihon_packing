<?php

namespace App\Action\Api;

use App\Domain\PackLabel\Service\PackLabelFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class PackLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $session;
    

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(
        PackLabelFinder $finder,
        Responder $responder, 
        Session $session)
    {
        
        $this->finder=$finder;
        $this->responder = $responder;
        $this->session = $session;
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

        $rtdata['message']="Get MergePackDetail Successful";
        $rtdata['error']=false;

        $rtdata['mpd_from_lots'] = $this->finder->findPackLabelForlots($params);
        $rtdata['mpd_from_merges'] = $this->finder->findPackLabelForMergePacks($params);

        if ($rtdata['mpd_from_lots'] != null && $rtdata['mpd_from_merges'] == null) {
            $rtdata['check_label_from_sl']="lot";
        }else if($rtdata['mpd_from_merges'] != null &&  $rtdata['mpd_from_lots'] == null){
            $rtdata['check_label_from_sl']="merge";
        }else if($rtdata['mpd_from_lots'] != null && $rtdata['mpd_from_merges'] != null) {
            $rtdata['check_label_from_sl']="lot_and_merge";
        } 

        return $this->responder->withJson($response, $rtdata);
    }
}