<?php

namespace App\Action\Web;


use App\Domain\Pack\Service\PackFinder;
use App\Domain\Pack\Service\PackUpdater;
use App\Domain\Product\Service\ProductFinder;
use App\Responder\Responder;
use PHPUnit\Framework\Constraint\Count;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class PackAddAction
{
    private $twig;
    private $finder;
    private $tempQueryFinder;
    private $responder;
    private $updater;
    private $findProcut;
    private $session;

    public function __construct(
        Twig $twig,
        Responder $responder,
        PackFinder $finder,
        PackUpdater $updater,
        ProductFinder $findProcut,
        Session $session
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->responder = $responder;
        $this->updater = $updater;
        $this->findProcut = $findProcut;
        $this->session = $session;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $data['ProductID'] = $data['product_id'];

        $id = $this->updater->insertPack($data);

        $rtPack['pack_id'] = $id;

        $packRow = $this->finder->findPackRow($id);

        $viewData = [
            'pack_id' => $packRow['id'],
            'product_id' => $packRow['product_id'],
            'search_product_id' => $data['id'],
            'search_pack_status' => $data['search_pack_status'],
        ];

        return $this->responder->withRedirect($response, "cpo_items", $viewData);
    }
}
