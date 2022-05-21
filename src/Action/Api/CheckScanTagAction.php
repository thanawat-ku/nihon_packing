<?php

namespace App\Action\Api;

use App\Domain\Tag\Service\TagFinder;
use App\Domain\Invoice\Service\InvoiceFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function DI\string;

/**
 * Action.
 */
final class CheckScanTagAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $findInvoice;


    public function __construct(

        TagFinder $finder,
        InvoiceFinder $findInvoice,
        Responder $responder

    ) {
        $this->finder = $finder;
        $this->findInvoice = $findInvoice;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        if (isset($data['tags'])) {

            $data['findTags'] = true;
            $rtInvoice = $this->findInvoice->findInvoiceTagAndLabels($data);

            $tags = $data['tags'];

            $checkError = true;

            $arrtags = explode("#", $tags);

            $rtdata['tags'] = [];

            for ($i = 1; $i < count($arrtags); $i++) {
                $arrTagNo = str_split($arrtags[$i]);

                for ($j = 0; $j < 1; $j++) {
                    if ($arrTagNo[0] == "T") {
                        $tag_no = $arrtags[$i];
                    } else if ($arrTagNo[0] == "P") {
                        $tag_no = 'not_add';
                    }
                }
                if ($tag_no != 'not_add') {
                    $tagNo['tag_no'] = explode(",", $tag_no)[0];
                    $tagRow = $this->finder->findTagSingleTable($tagNo);


                    if ($tagRow != null) {
                        for ($k = 0; $k < count($rtInvoice); $k++) {
                            if ($tagRow[0]['id'] == $rtInvoice[$k]['tag_id']) {
                                $checkError = false;
                            }
                        }

                        $tagRow[0]['check_error'] = $checkError;

                        $rtdata['message'] = "Get Label Successful";
                        $rtdata['error'] = false;
                        array_push($rtdata['tags'], $tagRow[0]);
                        $checkError = true;
                    } else {
                        $rtdata['message'] = "Get Label Successful";
                        $rtdata['tag_no'] = $tagRow[0]['tag_no'];
                        $rtdata['error'] = true;
                        break;
                    }
                }
            }
        }

        $rtdata['message'] = "Get Label Successful";
        $rtdata['error'] = false;
        return $this->responder->withJson($response, $rtdata);
    }
}
