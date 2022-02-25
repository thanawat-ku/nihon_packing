<?php

namespace App\Action\Web;

use App\Domain\Tag\Service\TagFinder;
use App\Domain\Tag\Service\TagUpdater;
use App\Domain\Sell\Service\SellFinder;
use App\Domain\Sell\Service\SellUpdater;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
    private $updateTag;
    private $updater;
    private $finder;
    private $session;

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(TagFinder $tagFinder, TagUpdater $updateTag, Session $session, SellUpdater $updater, SellFinder $finder, Responder $responder)
    {
        $this->tagFinder = $tagFinder;
        $this->updateTag = $updateTag;
        $this->updater = $updater;
        $this->finder = $finder;
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
        $sellID = $data['id'];

        $rtSell = $this->finder->findSellRow($sellID);

        if ($rtSell['is_completed'] == 'Y') {

            $upStatus['status'] = "BOXED";
            $this->updateTag->updateTagFronSellID($sellID, $upStatus);

            $data['sell_status'] = "TAGGED";
            $this->updater->updateSell($sellID, $data);

        } else if ($rtSell['is_completed'] == 'N') {
         
            $upStatus['status'] = "BOXED";
            $this->updateTag->updateTagFronSellID($sellID, $upStatus);

            $data['sell_status'] = "COMPLETE";
            $this->updater->updateSell($sellID, $data);
        }

        return $this->responder->withRedirect($response, "sells");
    }
}
