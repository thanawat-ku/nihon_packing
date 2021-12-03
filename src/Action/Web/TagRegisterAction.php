<?php

namespace App\Action\Web;

use App\Domain\Tag\Service\TagFinder;
use App\Domain\Tag\Service\TagUpdater;
use App\Domain\Sell\Service\SellUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class TagRegisterAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $updateTag;
    private $updater;
    private $session;

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(Twig $twig,TagFinder $tagFinder,TagUpdater $updateTag, Session $session,SellUpdater $updater,  Responder $responder)
    {
        $this->twig = $twig;
        $this->tagFinder=$tagFinder;
        $this->updateTag = $updateTag;
        $this->updater=$updater;
        $this->session=$session;
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
        $sellID = $data['id'];

        $data['sell_status'] = "TAGGED";
        $this->updater->updateSell($sellID, $data);

        $upStatus['status'] = "BOXED";
        $this->updateTag-> updateTagFronSellID($sellID, $upStatus);

        return $this->responder->withRedirect($response, "sells");
    }
}
