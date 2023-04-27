<?php

namespace App\Action\Api;

use App\Domain\CpoItem\Service\CpoItemFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class CpoItemSelectAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $twig;
    private $session;

    public function __construct(
        Twig $twig,
        CpoItemFinder $finder,
        Session $session,
        Responder $responder
    ) {
        $this->twig = $twig;
        $this->finder = $finder;
        $this->session = $session;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = (array)$request->getQueryParams();
        $params['startDate'] = $params['start_date'];
        $params['endDate'] = $params['end_date'];

        $rtdata['message'] = "Get CpoItem Successful";
        $rtdata['error'] = false;
        $rtdata['cpo_items'] = $this->finder->findCpoItemSelect($params);

        //split date and time
        for ($i = 0; $i < count($rtdata['cpo_items']); $i++) {
            $timestamp = $rtdata['cpo_items'][$i]['DueDate'];
            $date = date('Y-m-d', strtotime($timestamp));
            $rtdata['cpo_items'][$i]['DueDate'] = $date;
        }

        return $this->responder->withJson($response, $rtdata);
    }
}
