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
final class MergePackDeleteAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;

    public function __construct(Twig $twig,MergePackFinder $finder,ProductFinder $productFinder, MergePackUpdater $updater,
    Session $session,Responder $responder)
    {
        $this->finder=$finder;
        $this->updater=$updater;
        $this->productFinder=$productFinder;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $mergePackID = $data['merge_pack_id'];
        $user_id=$data["user_id"];

        $data['is_delete']="Y";
        $this->updater->updateMergePackApi($mergePackID,$data, $user_id);

        $rtdata=$this->finder->findMergePacks($data);
       
        return $this->responder->withJson($response, $rtdata[0]);
        

    }
}