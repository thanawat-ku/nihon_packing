<?php

namespace App\Action\Web;

use App\Domain\SparePartIssue\Service\SparePartIssueFinder;
use App\Domain\User\Service\UserFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Action.
 */
final class HomeAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $twig;
    private $userFinder;
    private $sparePartIssueFinder;
    private $session;

    /**
     * The constructor.
     *
     * @param Responder $responder The responder
     */
    public function __construct(Twig $twig,SparePartIssueFinder $sparePartIssueFinder,
        UserFinder $userFinder,Session $session,Responder $responder)
    {
        $this->twig = $twig;
        $this->userFinder=$userFinder;
        $this->sparePartIssueFinder=$sparePartIssueFinder;
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
        $params = (array)$request->getQueryParams();
        if(!isset($params['startDate'])){
            $params['startDate']=date('Y-m-d',strtotime('-30 days',strtotime(date('Y-m-d'))));
            $params['endDate']=date('Y-m-d');
        }
        $params['user_login'] = $this->session->get('user');
        $viewData = [
            'spare_part_issues' => $this->sparePartIssueFinder->findMySparePartIssues($params),
            'user_login' => $this->session->get('user'),
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
        ];
        

        return $this->twig->render($response, 'web/home.twig',$viewData);
    }
}
