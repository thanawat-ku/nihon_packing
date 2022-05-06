<?php

namespace App\Action\Web;

use App\Domain\Tag\Service\TagFinder;
use App\Domain\Sell\Service\SellFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class TagAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $tagFinder;
    private $sellFinder;
    private $session;

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(Twig $twig, TagFinder $tagFinder, SellFinder $sellFinder, Session $session, Responder $responder)
    {
        $this->twig = $twig;
        $this->tagFinder = $tagFinder;
        $this->sellFinder = $sellFinder;
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
        $params = (array)$request->getQueryParams();
        $sellID = $params['sell_id'];

        $checkTagPrinted = "true";

        $rtTags = $this->tagFinder->findTags($params);
        for ($i = 0; $i < count($rtTags); $i++) {
            if ($rtTags[$i]['status'] != "PRINTED") {
                $checkTagPrinted = "false";
            }
        }

        $sellRow = $this->sellFinder->findSellRow($sellID);
        $sellRow['cpo_item_id']=$rtTags[0]['cpo_item_id'];
        $sellRow['total_qty']=$rtTags[0]['total_qty'];
        
        $viewData = [
            'checkTagPrinted' => $checkTagPrinted,
            'sellRow' => $sellRow,
            'tags' => $rtTags,
            'user_login' => $this->session->get('user'),
        ];

        return $this->twig->render($response, 'web/tags.twig', $viewData);
    }
}
