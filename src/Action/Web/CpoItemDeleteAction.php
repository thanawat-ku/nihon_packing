<?php

namespace App\Action\Web;

use App\Domain\CpoItem\Service\CpoItemUpdater;
use App\Domain\TempQuery\Service\TempQueryFinder;
use App\Domain\Sell\Service\SellFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Slim\Views\Twig;


/**
 * Action.
 */
final class CpoItemDeleteAction
{
    private $twig;
    private $responder;
    private $updater;
    private $finder;
    private $tempQueryFinder;
    private $session;

    public function __construct(Twig $twig, Responder $responder,TempQueryFinder $tempQueryFinder, CpoItemUpdater $updater, SellFinder $finder,Session $session)
    {
        $this->responder = $responder;
        $this->updater = $updater;
        $this->finder = $finder;
        $this->tempQueryFinder=$tempQueryFinder;
        $this->session=$session;
        $this->twig = $twig;
    }


    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $sellID = (string)$data["sell_id"];
        $id = $data["id"];

        $sellRow = $this->finder->findSellRow($sellID);

        $this->updater->deleteCpoItem($id);

        $viewData = [
            'sellRow'=>$sellRow,
            'CpoItem' => $this->tempQueryFinder->findTempQuery($data),
            'user_login' => $this->session->get('user'),
        ];
        
        return $this->twig->render($response, 'web/cpoItem.twig',$viewData);
    }
}
