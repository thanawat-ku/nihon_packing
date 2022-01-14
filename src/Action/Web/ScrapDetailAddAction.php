<?php

namespace App\Action\Web;

use App\Domain\ScrapDetail\Service\ScrapDetailUpdater;
use App\Domain\ScrapDetail\Service\ScrapDetailFinder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Responder\Responder;

/**
 * Action.
 */
final class ScrapDetailAddAction
{

    private $updateScrapDetail;

    private $responder;
    private $session;

    public function __construct(
        ScrapDetailUpdater $updateScrapDetail,
        ScrapDetailFinder $scrapDetailFinder,
        Session $session,
        Responder $responder
    ) {

        $this->updateScrapDetail = $updateScrapDetail;
        $this->scrapDetailFinder = $scrapDetailFinder;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = (array)$request->getParsedBody();
        $scrapID = $data['scrap_id'];

        $this->updateScrapDetail->insertScrapDetail($data);

        $viewData = [
            'scrap_id' => $scrapID,
            'user_login' => $this->session->get('user'),
        ];

        return $this->responder->withRedirect($response, "scrap_details", $viewData);
    }
}
