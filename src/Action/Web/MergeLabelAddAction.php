<?php

namespace App\Action\Web;

use App\Domain\MergePack\Service\MergePackFinder;
use App\Domain\MergePack\Service\MergePackUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Domain\Product\Service\ProductFinder;

/**
 * Action.
 */
final class MergeLabelAddAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $finder;
    private $updater;
    private $session;

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(
        Twig $twig,
        MergePackFinder $finder,
        Session $session,
        Responder $responder,
        MergePackUpdater  $updater,
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->session = $session;
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
        $data = (array)$request->getParsedBody(); 
        $user_id = $this->session->get('user')["id"];
        $this->updater->insertMergePackApi($data,$user_id);
        
        return $this->responder->withRedirect($response, "merges");
    }
}
