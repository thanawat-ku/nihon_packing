<?php

namespace App\Action\Web;

use App\Domain\Tag\Service\TagFinder;
use App\Domain\Tag\Service\TagUpdater;
use App\Domain\Pack\Service\PackFinder;
use App\Domain\Pack\Service\PackUpdater;
use App\Domain\Packing\Service\PackingFinder;
use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Domain\CpoItem\Service\CpoItemUpdater;
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
    private $findPacking;
    private $findCpoItem;
    private $updateCpoItem;
    private $session;

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(TagFinder $tagFinder, TagUpdater $updateTag, Session $session, PackUpdater $updater, PackFinder $finder, Responder $responder, PackingFinder $findPacking, CpoItemFinder $findCpoItem, CpoItemUpdater $updateCpoItem)
    {
        $this->tagFinder = $tagFinder;
        $this->updateTag = $updateTag;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->findPacking = $findPacking;
        $this->findCpoItem = $findCpoItem;
        $this->updateCpoItem = $updateCpoItem;
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
        $packID = $data['id'];

        $rtPack = $this->finder->findPackRow($packID);

        if ($rtPack['is_completed'] == 'Y') {

            $upStatus['status'] = "BOXED";
            $this->updateTag->updateTagFronPackID($packID, $upStatus);

            $data['pack_status'] = "TAGGED";
            $this->updater->updatePack($packID, $data);

        } else if ($rtPack['is_completed'] == 'N') {
            
            $upStatus['status'] = "BOXED";
            $this->updateTag->updateTagFronPackID($packID, $upStatus);

            $data['pack_status'] = "COMPLETE";
            $this->updater->updatePack($packID, $data);
        }

        return $this->responder->withRedirect($response, "packs");
    }
}
