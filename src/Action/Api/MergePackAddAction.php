<?php

namespace App\Action\Api;

use App\Domain\MergePack\Service\MergePackFinder;
use App\Domain\MergePack\Service\MergePackUpdater;
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
final class MergePackAddAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;

    public function __construct(
        Twig $twig,
        MergePackFinder $finder,
        ProductFinder $productFinder,
        MergePackUpdater $updater,
        Session $session,
        Responder $responder
    ) {
        $this->finder = $finder;
        $this->updater = $updater;
        $this->productFinder = $productFinder;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $prodcutID = $data["product_id"];
        $user_id = $data["user_id"];

        if (isset($data['start_date'])) {
            $data['startDate'] = $data['start_date'];
            $data['endDate'] = $data['end_date'];
        }

        $data['check_notcomplete'] = true;
        $rtMergePack = $this->finder->findMergePacks($data);

        $checkMergePack = true;
        for ($i = 0; $i < count($rtMergePack); $i++) {
            if ($rtMergePack[$i]['product_id'] == $prodcutID) {
                $checkMergePack = false;
                break;
            }
        }

        if ($checkMergePack == true) {
            $id = $this->updater->insertMergePackApi($data, $user_id);

            $params1['id'] = $id;
            $rtdata = $this->finder->findMergePacks($params1);

            return $this->responder->withJson($response, $rtdata[0]);
        }else{
            $rtdata = null;
            return $this->responder->withJson($response, $rtdata[0]);
        }
    }
}
