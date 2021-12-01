<?php

// Define app routes

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Tuupola\Middleware\HttpBasicAuthentication;
use App\Middleware\UserAuthMiddleware;

return function (App $app) {
    // Redirect to Swagger documentation
    $app->get('/login', \App\Action\LoginAction::class)->setName('login');
    $app->post('/login', \App\Action\LoginSubmitAction::class);
    $app->get('/logout', \App\Action\LogoutAction::class)->setName('logout');

    // Swagger API documentation
    $app->get('/docs/v1', \App\Action\Documentation\SwaggerUiAction::class)->setName('docs');

    $app->get('/', \App\Action\Web\HomeAction::class)->setName('home')->add(UserAuthMiddleware::class);
    $app->get('/users', \App\Action\Web\UserAction::class)->add(UserAuthMiddleware::class);
    $app->post('/edit_user', \App\Action\Web\UserEditAction::class)->add(UserAuthMiddleware::class);
    $app->post('/add_user', \App\Action\Web\UserAddAction::class)->add(UserAuthMiddleware::class);

    $app->get('/customers', \App\Action\Web\CustomerAction::class)->add(UserAuthMiddleware::class);
    $app->post('/add_customer', \App\Action\Web\CustomerAddAction::class)->add(UserAuthMiddleware::class);
    $app->post('/edit_customer', \App\Action\Web\CustomerEditAction::class)->add(UserAuthMiddleware::class);
    $app->post('/delete_customer', \App\Action\Web\CustomerDeleteAction::class)->add(UserAuthMiddleware::class);

    $app->get('/products', \App\Action\Web\ProductAction::class)->add(UserAuthMiddleware::class);
    $app->post('/add_product', \App\Action\Web\ProductAddAction::class)->add(UserAuthMiddleware::class);
    $app->post('/edit_product', \App\Action\Web\ProductEditAction::class)->add(UserAuthMiddleware::class);
    $app->post('/delete_product', \App\Action\Web\ProductDeleteAction::class)->add(UserAuthMiddleware::class);

    $app->get('/merges', \App\Action\Web\MergeAction::class)->add(UserAuthMiddleware::class);
    $app->post('/add_merges', \App\Action\Web\MergeAddAction::class)->add(UserAuthMiddleware::class);
    $app->post('/edit_merges', \App\Action\Web\MergeEditAction::class)->add(UserAuthMiddleware::class);
    $app->post('/confirm_merges', \App\Action\Web\MergeConfirmAction::class)->add(UserAuthMiddleware::class);
    $app->post('/register_merge', \App\Action\Web\MergeRegisterAction::class)->add(UserAuthMiddleware::class);
    $app->post('/delete_merges', \App\Action\Web\MergeDeleteAction::class)->add(UserAuthMiddleware::class);

    $app->get('/merge_detail', \App\Action\Web\MergeDetailAction::class)->add(UserAuthMiddleware::class);
    $app->get('/label_merges', \App\Action\Web\MergeLabelAction::class)->add(UserAuthMiddleware::class);
    $app->get('/label_merge_news', \App\Action\Web\MergeLabelNewAction::class)->add(UserAuthMiddleware::class);
    $app->post('/add_label_merge', \App\Action\Web\MergeLabelAddAction::class)->add(UserAuthMiddleware::class);
    $app->post('/delete_merge_detail', \App\Action\Web\MergeDetailDeleteAction::class)->add(UserAuthMiddleware::class); //page2

    $app->post('/edit_lot', \App\Action\Web\LotEditAction::class)->add(UserAuthMiddleware::class);
    $app->post('/delete_lot', \App\Action\Web\LotDeleteAction::class)->add(UserAuthMiddleware::class);
    $app->post('/print_lot', \App\Action\Web\LotPrintAction::class)->add(UserAuthMiddleware::class);
    $app->post('/register_lot', \App\Action\Web\LotRegisterAction::class)->add(UserAuthMiddleware::class);
    $app->get('/label_lot', \App\Action\Web\LotLabelAction::class)->add(UserAuthMiddleware::class);
    $app->get('/lots', \App\Action\Web\LotAction::class)->add(UserAuthMiddleware::class);

    $app->get('/lot_defect_detail', \App\Action\Web\LotDefectAction::class)->add(UserAuthMiddleware::class);
    $app->post('/add_lot_defect', \App\Action\Web\LotDefectAddAction::class)->add(UserAuthMiddleware::class);
    $app->post('/edit_lot_defect', \App\Action\Web\LotDefectEditAction::class)->add(UserAuthMiddleware::class);
    $app->post('/delete_lot_defect', \App\Action\Web\LotDefectDeleteAction::class)->add(UserAuthMiddleware::class);
    $app->get('/lot_defect_for_scrap', \App\Action\Web\LotdefectForScrapAction::class)->add(UserAuthMiddleware::class);

    $app->get('/labels', \App\Action\Web\LabelAction::class)->add(UserAuthMiddleware::class);
    $app->post('/void_label', \App\Action\Web\LabelVoidAction::class)->add(UserAuthMiddleware::class);
    $app->post('/split_label', \App\Action\Web\LabelSplitAction::class)->add(UserAuthMiddleware::class);
    $app->post('/add_label', \App\Action\Web\LabelAddAction::class)->add(UserAuthMiddleware::class);
    $app->post('/edit_label', \App\Action\Web\LabelEditAction::class)->add(UserAuthMiddleware::class);
    $app->post('/delete_label', \App\Action\Web\LabelDeleteAction::class)->add(UserAuthMiddleware::class);

    $app->get('/splitLabels', \App\Action\Web\SplitLabelAction::class)->add(UserAuthMiddleware::class);
    $app->get('/label_splitlabel', \App\Action\Web\SplitLabelDetailAction::class)->add(UserAuthMiddleware::class);
    $app->post('/splitLabel_register', \App\Action\Web\SplitLabelRegisterAction::class)->add(UserAuthMiddleware::class);
    $app->post('/delete_splitLabel', \App\Action\Web\SplitLabelDeleteAction::class)->add(UserAuthMiddleware::class);

    $app->get('/label_void_reasons', \App\Action\Web\LabelVoidReasonAction::class)->add(UserAuthMiddleware::class);
    $app->post('/add_label_void_reason', \App\Action\Web\LabelVoidReasonAddAction::class)->add(UserAuthMiddleware::class);
    $app->post('/edit_label_void_reason', \App\Action\Web\LabelVoidReasonEditAction::class)->add(UserAuthMiddleware::class);
    $app->post('/delete_label_void_reason', \App\Action\Web\LabelVoidReasonDeleteAction::class)->add(UserAuthMiddleware::class);

    $app->get('/sells', \App\Action\Web\SellAction::class)->add(UserAuthMiddleware::class);
    $app->post('/add_sell', \App\Action\Web\SellAddAction::class)->add(UserAuthMiddleware::class);
    $app->get('/select_label_for_sells', \App\Action\Web\SelectLabelForSellAction::class)->add(UserAuthMiddleware::class);

    $app->get('/sell_labels', \App\Action\Web\SellLabelAction::class)->add(UserAuthMiddleware::class);
    $app->post('/add_sell_label', \App\Action\Web\SellLabelAddAction::class)->add(UserAuthMiddleware::class);
    $app->post('/cancel_sell_label', \App\Action\Web\SellLabelCancelAction::class)->add(UserAuthMiddleware::class);
    $app->post('/remove_sell_label', \App\Action\Web\SellLabelRemoveAction::class)->add(UserAuthMiddleware::class);
    $app->post('/confirm_sell', \App\Action\Web\SellConfirmAction::class)->add(UserAuthMiddleware::class);
    $app->post('/confirm_sell_label', \App\Action\Web\SellLabelConfirmAction::class)->add(UserAuthMiddleware::class);
    $app->post('/edit_sell', \App\Action\Web\SellEditAction::class)->add(UserAuthMiddleware::class);
    $app->post('/delete_sell', \App\Action\Web\SellDeleteAction::class)->add(UserAuthMiddleware::class);

    $app->post('/api/login', \App\Action\ApiLoginSubmitAction::class);
    $app->get('/api/merges', \App\Action\Api\MergeAction::class);
    $app->get('/cpo_items', \App\Action\Web\CpoItemAction::class)->add(UserAuthMiddleware::class);
    $app->get('/cpo_item_selects', \App\Action\Web\CpoItemSelectAction::class)->add(UserAuthMiddleware::class);
    $app->post('/add_cpo_item', \App\Action\Web\CpoItemAddAction::class)->add(UserAuthMiddleware::class);
    $app->post('/edit_cpoidtem', \App\Action\Web\CpoItemEditAction::class)->add(UserAuthMiddleware::class);
    $app->post('/delete_CpoItem', \App\Action\Web\CpoItemDeleteAction::class)->add(UserAuthMiddleware::class);

    $app->get('/scraps', \App\Action\Web\ScrapAction::class)->add(UserAuthMiddleware::class);
    $app->post('/add_scrap', \App\Action\Web\ScrapAddAction::class)->add(UserAuthMiddleware::class);
    $app->post('/delete_scrap', \App\Action\Web\ScrapDeleteAction::class)->add(UserAuthMiddleware::class);
    $app->post('/edit_scrap', \App\Action\Web\ScrapEditAction::class)->add(UserAuthMiddleware::class);
    $app->post('/confirm_scrap', \App\Action\Web\ScrapConfirmAction::class)->add(UserAuthMiddleware::class);
    $app->post('/reject_scrap', \App\Action\Web\ScrapRejectAction::class)->add(UserAuthMiddleware::class);

    $app->get('/scrap_details', \App\Action\Web\ScrapDetailAction::class)->add(UserAuthMiddleware::class);
    $app->post('/delete_scrap_detail', \App\Action\Web\ScrapDetailDeleteAction::class)->add(UserAuthMiddleware::class);
    $app->post('/add_scrap_detail', \App\Action\Web\ScrapDetailAddAction::class)->add(UserAuthMiddleware::class);

    $app->get('/defects', \App\Action\Web\DefectAction::class)->add(UserAuthMiddleware::class);
    $app->get('/sections', \App\Action\Web\SectionAction ::class)->add(UserAuthMiddleware::class);

    //---------------------------Api-------------------------------

    $app->get('/api/lots', \App\Action\Api\LotAction::class);
    $app->post('/api/confirm_lot_check', \App\Action\Api\LotConfirmCheckAction::class);
    $app->post('/api/confirm_lot', \App\Action\Api\LotConfirmAction::class);
    $app->post('/api/save_lot', \App\Action\Api\LotSaveAction::class);

    $app->get('/api/lot_defects', \App\Action\Api\LotDefectAction::class);
    $app->post('/api/add_lot_defect', \App\Action\Api\LotDefectAddAction::class);
    $app->post('/api/edit_lotdefects', \App\Action\Api\LotDefectEditAction::class);
    $app->post('/api/delete_lotdefects', \App\Action\Api\LotDefectDeleteAction::class);


    $app->get('/api/defects', \App\Action\Api\DefectAction::class);
    $app->post('/api/add_defect', \App\Action\Api\DefectAddAction::class);
    $app->post('/api/delete_defect', \App\Action\Api\DefectDeleteAction::class);
    $app->post('/api/edit_defect', \App\Action\Api\DefectEditAction::class);

    $app->get('/api/products', \App\Action\Api\ProductAction::class);

    $app->get('/api/customers', \App\Action\Api\CustomerAction::class);


    $app->get('/api/labels', \App\Action\Api\LabelAction::class);
    $app->get('/api/find_labels_scan', \App\Action\Api\LabelFindForScanAction::class);
    $app->post('/api/register_label', \App\Action\Api\LabelRegisterAction::class);
    $app->post('/api/gen_merge_labels', \App\Action\Api\GenMergeLabelBarcodeNoAction::class);
    $app->post('/api/up_status_merge_label', \App\Action\Api\UpStatusMergeLabelAction::class);
    $app->post('/api/check_label_scan', \App\Action\Api\CheckLabelScanAction::class);
    $app->post('/api/label_scan_merge', \App\Action\Api\LabelScanMergeAction::class);
    $app->get('/api/label_merge_packs', \App\Action\Api\LabelFromMergepackAction::class);
    $app->post('/api/check_lb_scan_rm', \App\Action\Api\CheckLabelScanRegisMergeAction::class);



    $app->get('/api/tags', \App\Action\Api\TagAction::class);
    $app->post('/api/register_tag', \App\Action\Api\TagRegisterAction::class);
    $app->post('/api/gen_tags', \App\Action\Api\GenTagBarcodeNoAction::class);

    $app->get('/api/split_labels',  \App\Action\Api\SplitLabelAction::class);
    $app->post('/api/add_split_label', \App\Action\Api\SplitLabelAddAction::class);
    $app->post('/api/register_split_label', \App\Action\Api\SplitLabelRegisterAction::class);
    $app->post('/api/delete_split_label', \App\Action\Api\SplitLabelDeleteAction::class);

    $app->get('/api/split_label_detail',  \App\Action\Api\SplitLabelDetailAction::class);
    $app->post('/api/add_split_label_detail', \App\Action\Api\SplitLabelDetailAddAction::class);

    $app->get('/api/merge_packs', \App\Action\Api\MergePackAction::class);

    $app->get('/api/select_merge_pack_id', \App\Action\Api\SelectMergePackIDAction::class);

    $app->post('/api/add_merge_pack', \App\Action\Api\MergePackAddAction::class);
    $app->post('/api/add_merge_pack_detail', \App\Action\Api\MergePackDetailAddAction::class);
    $app->post('/api/gen_label_merge_packs', \App\Action\Api\GenMergeLabelBarcodeNoAction::class);
    $app->post('/api/add_mn_from_lb', \App\Action\Api\AddMergeNoFromLabelAction::class);
    $app->post('/api/check_merge_pack_id', \App\Action\Api\CheckMergePackIDAction::class);
    $app->post('/api/cancel_label', \App\Action\Api\CancelLabelAction::class);
    $app->post('/api/delete_merge_pack_detail', \App\Action\Api\MergePackDetailDeleteAction::class);
    $app->post('/api/get_qty_scan', \App\Action\Api\GetQtyScanAction::class);
    $app->post('/api/delete_merge_pack', \App\Action\Api\MergePackDeleteAction::class);



    $app->post('/api/label_search', \App\Action\Api\LabelsearchAction::class);

    $app->get('/api/cpo_items', \App\Action\Api\CpoItemAction::class);
    $app->get('/api/cpo_item_select', \App\Action\Api\CpoItemSelectAction::class);

    $app->get('/api/sells', \App\Action\Api\SellAction::class);
    $app->post('/api/add_sell', \App\Action\Api\SellAddAction::class);
    $app->post('/api/sell_row', \App\Action\Api\SellRowAction::class);
    $app->post('/api/up_status_sell', \App\Action\Api\SellUpStatusAction::class);
    $app->post('/api/get_qty_sell_scan', \App\Action\Api\GetQtySellScanAction::class);
    $app->post('/api/delete_sell', \App\Action\Api\SellDeleteAction::class);

    $app->get('/api/product_for_sells', \App\Action\Api\ProductForSellAction::class);

    $app->get('/api/sell_cpo_items', \App\Action\Api\SellCpoItemAction::class);
    $app->post('/api/add_sell_cpo_item', \App\Action\Api\SellCpoItemAddAction::class);
    $app->post('/api/edit_sell_cpo_item', \App\Action\Api\SellCpoItemEditAction::class);
    $app->post('/api/delete_sell_cpo_item', \App\Action\Api\SellCpoItemDeleteAction::class);

    $app->get('/api/merge_pack_details', \App\Action\Api\MergePackDetailAction::class);
    $app->get('/api/merge_pack_detail_for_registers', \App\Action\Api\MergePackDetailForRegisterAction::class);
    $app->post('/api/complete_merge_pack', \App\Action\Api\CompleteMergePackAction::class);



    $app->get('/api/sell_labels', \App\Action\Api\SellLabelAction::class);
    $app->post('/api/check_sell_label_scan', \App\Action\Api\CheckSellLabelScanAction::class);
    $app->post('/api/cancel_sell_label', \App\Action\Api\CancelSellLabelAction::class);
    $app->post('/api/confirm_sell_label', \App\Action\Api\ConfirmSellLabelAction::class);
    $app->get('/api/mis_sync_lots', \App\Action\Api\LotSyncAction::class);
    $app->get('/api/mis_sync_products', \App\Action\Api\ProductSyncAction::class);
    $app->get('/api/mis_sync_customers', \App\Action\Api\CustomerSyncAction::class);
    $app->get('/api/mis_sync_sections', \App\Action\Api\SectionSyncAction::class);
    $app->get('/api/mis_sync_defects', \App\Action\Api\DefectSyncAction::class);
};
