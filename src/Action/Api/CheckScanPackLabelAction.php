<?php

namespace App\Action\Api;

use App\Domain\Label\Service\LabelFinder;
use App\Domain\Label\Service\LabelUpdater;
use App\Domain\MergePack\Service\MergePackUpdater;
use App\Domain\PackLabel\Service\PackLabelFinder;
use App\Domain\Invoice\Service\InvoiceFinder;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function DI\string;

/**
 * Action.
 */
final class CheckScanPackLabelAction
{
    /**
     * @var Responder
     */
    private $responder;
    private $finder;
    private $updater;
    private $findPackLabel;
    private $findInvoice;


    public function __construct(

        LabelFinder $finder,
        MergepackUpdater $updater,
        PackLabelFinder $findPackLabel,
        InvoiceFinder $findInvoice,
        Responder $responder

    ) {
        $this->finder = $finder;
        $this->updater = $updater;
        $this->findPackLabel = $findPackLabel;
        $this->findInvoice = $findInvoice;
        $this->responder = $responder;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        if (isset($data['label'])) {
            $data['findLabels'] = true;
            $rtInvoice = $this->findInvoice->findInvoiceTagAndLabels($data);

            // for ($l = 0; $l < count($rtInvoice); $l++) {
            // $data['pack_id'] = $rtInvoice[$l]['pack_id'];
            $label = $data['label'];

            $checkError = true;

            $arrlabel = explode("#", $label);

            $rtdata['mpd_from_lots'] = [];
            $rtdata['mpd_from_merges'] = [];
            $rtdata['label_no'] = "Null";

            for ($i = 1; $i < count($arrlabel); $i++) {

                $arrPackLabelNo = str_split($arrlabel[$i]);

                for ($j = 0; $j < 1; $j++) {
                    if ($arrPackLabelNo[0] == "P") {
                        $label_no = $arrlabel[$i];
                    } else if ($arrPackLabelNo[0] == "T") {
                        $label_no = 'not_add';
                        break;
                    }
                }

                if ($label_no != 'not_add') {
                    $labelNo['label_no'] = explode(",", $label_no)[0];
                    $labelRow = $this->finder->findLabelSingleTable($labelNo);

                    for ($k = 0; $k < count($rtInvoice); $k++) {
                        if ($labelRow[0]['id'] == $rtInvoice[$k]['label_id']) {
                            $checkError = false;
                        }
                    }

                    if ($labelRow != null) {
                        $labelID['id'] = $labelRow[0]['id'];
                        if ($labelRow[0]['merge_pack_id'] == 0) {
                            if ($labelRow[0]['lot_id'] != 0) {

                                // $rtPackLabel = $this->findPackLabel->findPackLabels($data);
                                // for ($k = 0; $k < count($rtPackLabel); $k++) {
                                //     if ($labelRow[0]['id'] == $rtPackLabel[$k]['label_id']) {
                                //         $checkError = false;
                                //     }
                                // }

                                $rtdata['message'] = "Get Label Successful";
                                $rtdata['error'] = false;
                                $labelLots = $this->finder->findCreateMergeNoFromLabels($labelID);
                                $labelLots[0]['check_error'] = $checkError;


                                array_push($rtdata['mpd_from_lots'], $labelLots[0]);

                                $checkError = true;
                            }
                        } else {

                            // $rtPackLabel = $this->findPackLabel->findPackLabels($data);
                            // for ($k = 0; $k < count($rtPackLabel); $k++) {
                            //     if ($labelRow[0]['id'] == $rtPackLabel[$k]['label_id']) {
                            //         $checkError = false;
                            //     }
                            // }

                            $rtdata['message'] = "Get Label Successful";
                            $rtdata['error'] = false;
                            $labelMergePacks = $this->finder->findLabelFromMergePacks($labelID);
                            $labelMergePacks[0]['check_error'] = $checkError;

                            array_push($rtdata['mpd_from_merges'], $labelMergePacks[0]);

                            $checkError = true;
                        }
                    } else {
                        $rtdata['message'] = "Get Label Successful";
                        $rtdata['label_no'] = $labelNo['label_no'];
                        $rtdata['error'] = true;
                        break;
                    }
                }
            }
            // }
        }

        $rtdata['message'] = "Get Label Successful";
        $rtdata['error'] = false;
        return $this->responder->withJson($response, $rtdata);
    }
}
