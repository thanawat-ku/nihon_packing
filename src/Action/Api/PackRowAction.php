<?php

namespace App\Action\Api;

use App\Domain\Pack\Service\PackFinder;
use App\Domain\PackCpoItem\Service\PackCpoItemFinder;
use App\Domain\Pack\Service\PackUpdater;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

use function DI\string;

/**
 * Action.
 */
final class PackRowAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $findersct;
    private $updater;
    private $twig;
    private $productFinder;
    private $session;

    public function __construct(
        Twig $twig,
        PackFinder $finder,
        ProductFinder $productFinder,
        PackUpdater $updater,
        Session $session,
        Responder $responder,
        PackCpoItemFinder $findersct
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->updater = $updater;
        $this->productFinder = $productFinder;
        $this->session = $session;
        $this->responder = $responder;
        $this->findersct = $findersct;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $packID=$data['pack_id'];
        $user_id=$data['user_id'];

        $packs = $this->findersct->findPackCpoItems($data);
        $this->updater->updatePackApi($packID, $packs, $user_id);
        $packRow = $this->finder->findPackRow($packID);

        $totalQty = (int)$packRow['total_qty'];

        if ($totalQty == 0) {
            $upStatus['up_status']="CREATED";
            $this->updater->updatePackStatus($packID, $upStatus, $user_id);
        }

        if ($packRow) {
            $rtdata['message'] = 'Login successfully';
            $rtdata['error'] = false;
            $rtdata['pack_row'] = $packRow;
        } else {
            $rtdata['message'] = 'Login fail';
            $rtdata['error'] = true;
            $rtdata['pack_row'] = null;
        }

        return $this->responder->withJson($response, $rtdata);
    }
}
