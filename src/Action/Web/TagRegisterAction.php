<?php

namespace App\Action\Web;

use App\Domain\Tag\Service\TagFinder;
use App\Domain\Tag\Service\TagUpdater;
use App\Domain\Sell\Service\SellFinder;
use App\Domain\Sell\Service\SellUpdater;
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
    public function __construct(TagFinder $tagFinder, TagUpdater $updateTag, Session $session, SellUpdater $updater, SellFinder $finder, Responder $responder, PackingFinder $findPacking, CpoItemFinder $findCpoItem, CpoItemUpdater $updateCpoItem)
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
        $sellID = $data['id'];

        $rtSell = $this->finder->findSellRow($sellID);

        if ($rtSell['is_completed'] == 'Y') {

            $rtSellRow = $this->finder->findSellRow($sellID);
            $rtPacking['PackingID'] = $rtSellRow['packing_id'];

            $sumQuantity = 0;
            $rtPackingItem = $this->findPacking->findPackingItem($rtPacking);
            for ($i=0; $i < count($rtPackingItem); $i++) { 
                $sumQuantity += $rtPackingItem[$i]['Quantity'];
            }
            
            $rtSearch['cpo_item_id'] = $rtPackingItem[0]['CpoItemID'];
            $rtCpoItem = $this->findCpoItem->findCpoItem($rtSearch);
            $isCpoItem['PackingQty'] = $sumQuantity + $rtCpoItem[0]['PackingQty']; 
        
            $this->updateCpoItem->updateCpoItem((int)$rtPackingItem[0]['CpoItemID'], $isCpoItem); 

            $upStatus['status'] = "BOXED";
            $this->updateTag->updateTagFronSellID($sellID, $upStatus);

            $data['sell_status'] = "TAGGED";
            $this->updater->updateSell($sellID, $data);

        } else if ($rtSell['is_completed'] == 'N') {

            $rtSellRow = $this->finder->findSellRow($sellID);
            $rtPacking['PackingID'] = $rtSellRow['packing_id'];

            $sumQuantity = 0;
            $rtPackingItem = $this->findPacking->findPackingItem($rtPacking);
            for ($i=0; $i < count($rtPackingItem); $i++) { 
                $sumQuantity += $rtPackingItem[$i]['Quantity'];
            }
            
            $rtSearch['cpo_item_id'] = $rtPackingItem[0]['CpoItemID'];
            $rtCpoItem = $this->findCpoItem->findCpoItem($rtSearch);
            $isCpoItem['PackingQty'] = $sumQuantity + $rtCpoItem[0]['PackingQty']; 
        
            $this->updateCpoItem->updateCpoItem((int)$rtPackingItem[0]['CpoItemID'], $isCpoItem); 
            
            $upStatus['status'] = "BOXED";
            $this->updateTag->updateTagFronSellID($sellID, $upStatus);

            $data['sell_status'] = "COMPLETE";
            $this->updater->updateSell($sellID, $data);
        }

        return $this->responder->withRedirect($response, "sells");
    }
}
