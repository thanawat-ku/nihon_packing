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
    $app->post('/add_merges_from_label', \App\Action\Web\MergeAddFromLabelAction::class)->add(UserAuthMiddleware::class);
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
    $app->post('/reprint_lot', \App\Action\Web\LotReprintAction::class)->add(UserAuthMiddleware::class);
    $app->post('/reverse_lot', \App\Action\Web\LotReverseAction::class)->add(UserAuthMiddleware::class);
    $app->get('/label_lot', \App\Action\Web\LotLabelAction::class)->add(UserAuthMiddleware::class);
    $app->get('/lots', \App\Action\Web\LotAction::class)->add(UserAuthMiddleware::class);

    $app->get('/lot_defect_detail', \App\Action\Web\LotDefectAction::class)->add(UserAuthMiddleware::class);
    $app->post('/add_lot_defect', \App\Action\Web\LotDefectAddAction::class)->add(UserAuthMiddleware::class);
    $app->post('/edit_lot_defect', \App\Action\Web\LotDefectEditAction::class)->add(UserAuthMiddleware::class);
    $app->post('/delete_lot_defect', \App\Action\Web\LotDefectDeleteAction::class)->add(UserAuthMiddleware::class);
    $app->get('/lot_defect_for_scrap', \App\Action\Web\LotdefectForScrapAction::class)->add(UserAuthMiddleware::class);

    $app->get('/labels', \App\Action\Web\LabelAction::class)->add(UserAuthMiddleware::class);
    $app->post('/void_label', \App\Action\Web\LabelVoidAction::class)->add(UserAuthMiddleware::class);
    $app->post('/reprint_label', \App\Action\Web\LabelReprintAction::class)->add(UserAuthMiddleware::class);
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

    $app->get('/packs', \App\Action\Web\PackAction::class)->add(UserAuthMiddleware::class);
    $app->post('/add_pack', \App\Action\Web\PackAddAction::class)->add(UserAuthMiddleware::class);
    $app->get('/select_label_for_packs', \App\Action\Web\SelectLabelForPackAction::class)->add(UserAuthMiddleware::class);

    $app->get('/pack_labels', \App\Action\Web\PackLabelAction::class)->add(UserAuthMiddleware::class);
    $app->post('/add_pack_label', \App\Action\Web\PackLabelAddAction::class)->add(UserAuthMiddleware::class);
    $app->post('/cancel_pack_label', \App\Action\Web\PackLabelCancelAction::class)->add(UserAuthMiddleware::class);
    $app->post('/remove_pack_label', \App\Action\Web\PackLabelRemoveAction::class)->add(UserAuthMiddleware::class);
    $app->post('/confirm_pack', \App\Action\Web\PackConfirmAction::class)->add(UserAuthMiddleware::class);
    $app->post('/confirm_pack_label', \App\Action\Web\PackLabelConfirmAction::class)->add(UserAuthMiddleware::class);
    $app->post('/edit_pack', \App\Action\Web\PackEditAction::class)->add(UserAuthMiddleware::class);
    $app->post('/delete_pack', \App\Action\Web\PackDeleteAction::class)->add(UserAuthMiddleware::class);
    $app->post('/reprint_pack', \App\Action\Web\PackReprintAction::class)->add(UserAuthMiddleware::class);

    $app->post('/api/login', \App\Action\ApiLoginSubmitAction::class);
    $app->get('/api/merges', \App\Action\Api\MergeAction::class);
    $app->get('/cpo_items', \App\Action\Web\CpoItemAction::class)->add(UserAuthMiddleware::class);
    $app->get('/cpo_item_check_temp_query', \App\Action\Web\CpoItemCheckTempQueryAction::class)->add(UserAuthMiddleware::class);
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
    $app->post('/accept_scrap', \App\Action\Web\ScrapAcceptAction::class)->add(UserAuthMiddleware::class);

    $app->get('/scrap_details', \App\Action\Web\ScrapDetailAction::class)->add(UserAuthMiddleware::class);
    $app->post('/delete_scrap_detail', \App\Action\Web\ScrapDetailDeleteAction::class)->add(UserAuthMiddleware::class);
    $app->post('/add_scrap_detail', \App\Action\Web\ScrapDetailAddAction::class)->add(UserAuthMiddleware::class);
    $app->post('/edit_scrap_detail', \App\Action\Web\ScrapDetailEditAction::class)->add(UserAuthMiddleware::class);

    $app->get('/defects', \App\Action\Web\DefectAction::class)->add(UserAuthMiddleware::class);
    $app->get('/sections', \App\Action\Web\SectionAction::class)->add(UserAuthMiddleware::class);

    $app->get('/tags', \App\Action\Web\TagAction::class)->add(UserAuthMiddleware::class);
    $app->post('/register_tags', \App\Action\Web\TagRegisterAction::class)->add(UserAuthMiddleware::class);
    
    $app->get('/printers', \App\Action\Web\PrinterAction::class)->add(UserAuthMiddleware::class);
    $app->post('/add_printer', \App\Action\Web\PrinterAddAction::class)->add(UserAuthMiddleware::class);
    $app->post('/edit_printer', \App\Action\Web\PrinterEditAction::class)->add(UserAuthMiddleware::class);
    $app->post('/delete_printer', \App\Action\Web\PrinterDeleteAction::class)->add(UserAuthMiddleware::class);

    $app->get('/invoices', \App\Action\Web\InvoiceAction::class)->add(UserAuthMiddleware::class);
    $app->get('/invoice_details', \App\Action\Web\InvoiceDetailAction::class)->add(UserAuthMiddleware::class);    

    //---------------------------Api-------------------------------

    $app->get('/api/users', \App\Action\Api\UserAction::class);

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
    $app->get('/api/find_labels_scan_in_lot', \App\Action\Api\LabelFindForScanInLotction::class);
    $app->get('/api/find_labels_scan_split', \App\Action\Api\LabelScanSplitAction::class);
    $app->get('/api/find_labels_scan_split_register', \App\Action\Api\LabelScanSplitRegisterAction::class);
    $app->post('/api/register_label', \App\Action\Api\LabelRegisterAction::class);
    $app->post('/api/gen_merge_labels', \App\Action\Api\GenMergeLabelBarcodeNoAction::class);
    $app->post('/api/up_status_merge_label', \App\Action\Api\UpStatusMergeLabelAction::class);
    $app->post('/api/check_label_scan', \App\Action\Api\CheckLabelScanAction::class);
    $app->post('/api/label_scan_merge', \App\Action\Api\LabelScanMergeAction::class);
    $app->get('/api/label_merge_packs', \App\Action\Api\LabelFromMergepackAction::class);
    $app->post('/api/check_lb_scan_rm', \App\Action\Api\CheckLabelScanRegisMergeAction::class);

    $app->get('/api/tags', \App\Action\Api\TagAction::class);
    $app->post('/api/register_tags_check', \App\Action\Api\TagRegisterCheckAction::class);
    $app->post('/api/register_tags', \App\Action\Api\TagRegisterAction::class);
    $app->post('/api/check_scan_tags', \App\Action\Api\CheckScanTagAction::class);

    $app->get('/api/split_labels',  \App\Action\Api\SplitLabelAction::class);
    $app->post('/api/add_split_label', \App\Action\Api\SplitLabelAddAction::class);
    $app->post('/api/register_split_label', \App\Action\Api\SplitLabelRegisterAction::class);
    $app->post('/api/delete_split_label', \App\Action\Api\SplitLabelDeleteAction::class);

    $app->get('/api/split_label_detail',  \App\Action\Api\SplitLabelDetailAction::class);
    $app->post('/api/add_split_label_detail', \App\Action\Api\SplitLabelDetailAddAction::class);

    $app->get('/api/merge_packs', \App\Action\Api\MergePackAction::class);

    $app->post('/api/add_merge_pack', \App\Action\Api\MergePackAddAction::class);
    // $app->post('/api/add_merge_pack_detail', \App\Action\Api\MergePackDetailAddAction::class);
    $app->post('/api/gen_label_merge_packs', \App\Action\Api\GenMergeLabelBarcodeNoAction::class);
    $app->post('/api/add_mn_from_lb', \App\Action\Api\AddMergeNoFromLabelAction::class);
    $app->post('/api/check_merge_pack_id', \App\Action\Api\CheckMergePackIDAction::class);
    $app->post('/api/cancel_label', \App\Action\Api\CancelLabelAction::class);
    // $app->post('/api/delete_merge_pack_detail', \App\Action\Api\MergePackDetailDeleteAction::class);
    $app->post('/api/get_qty_scan', \App\Action\Api\GetQtyScanAction::class);
    $app->post('/api/delete_merge_pack', \App\Action\Api\MergePackDeleteAction::class);

    $app->post('/api/label_search', \App\Action\Api\LabelsearchAction::class);

    $app->get('/api/cpo_items', \App\Action\Api\CpoItemAction::class);
    $app->get('/api/cpo_item_select', \App\Action\Api\CpoItemSelectAction::class);

    $app->get('/api/packs', \App\Action\Api\PackAction::class);
    $app->get('/api/register_tag_on_packs', \App\Action\Api\RegisterTagOnPackAction::class);
    $app->post('/api/add_pack', \App\Action\Api\PackAddAction::class);
    $app->post('/api/pack_row', \App\Action\Api\PackRowAction::class);
    $app->post('/api/pack_tag', \App\Action\Api\PackTagAction::class);
    $app->post('/api/up_status_pack', \App\Action\Api\PackUpStatusAction::class);
    $app->post('/api/get_qty_pack_scan', \App\Action\Api\GetQtyPackScanAction::class);
    $app->post('/api/edit_pack', \App\Action\Api\PackEditAction::class);
    $app->post('/api/delete_pack', \App\Action\Api\PackDeleteAction::class);
    $app->post('/api/complete_pack', \App\Action\Api\PackCompleteAction::class);
    $app->get('/api/mis_sync_invoice', \App\Action\Api\SyncInvoiceNoAction::class);

    $app->get('/api/product_for_packs', \App\Action\Api\ProductForPackAction::class);

    $app->get('/api/pack_cpo_items', \App\Action\Api\PackCpoItemAction::class);
    $app->post('/api/add_pack_cpo_item', \App\Action\Api\PackCpoItemAddAction::class);
    $app->post('/api/edit_pack_cpo_item', \App\Action\Api\PackCpoItemEditAction::class);
    $app->post('/api/delete_pack_cpo_item', \App\Action\Api\PackCpoItemDeleteAction::class);

    $app->get('/api/merge_pack_details', \App\Action\Api\MergePackDetailAction::class);
    $app->get('/api/merge_pack_detail_for_registers', \App\Action\Api\MergePackDetailForRegisterAction::class);
    $app->post('/api/complete_merge_pack', \App\Action\Api\CompleteMergePackAction::class);

    $app->get('/api/pack_labels', \App\Action\Api\PackLabelAction::class);
    $app->post('/api/check_pack_label_scan', \App\Action\Api\CheckPackLabelScanAction::class);
    $app->post('/api/cancel_pack_label', \App\Action\Api\CancelPackLabelAction::class);
    $app->post('/api/confirm_pack_label', \App\Action\Api\ConfirmPackLabelAction::class);
    $app->get('/api/mis_sync_lots', \App\Action\Api\LotSyncAction::class);
    $app->post('/api/check_scan_pack_labels', \App\Action\Api\CheckScanPackLabelAction::class);

    $app->get('/api/scraps', \App\Action\Api\ScrapAction::class);
    $app->post('/api/add_scrap', \App\Action\Api\ScrapAddAction::class);
    $app->post('/api/edit_scrap', \App\Action\Api\ScrapEditAction::class);
    $app->post('/api/delete_scrap', \App\Action\Api\ScrapDeleteAction::class);
    $app->post('/api/confirm_scrap', \App\Action\Api\ScrapConfirmAction::class);
    $app->post('/api/reject_scrap', \App\Action\Api\ScrapRejectAction::class);
    $app->post('/api/accept_scrap', \App\Action\Api\ScrapAcceptAction::class);

    $app->get('/api/scrap_details', \App\Action\Api\ScrapDetailAction::class);
    $app->post('/api/add_scrap_detail', \App\Action\Api\ScrapDetailAddAction::class);
    $app->post('/api/delete_scrap_detail', \App\Action\Api\ScrapDetailDeleteAction::class);
    $app->post('/api/edit_scrap_detail', \App\Action\Api\ScrapDetailEditAction::class);

    $app->get('/api/sections', \App\Action\Api\SectionAction::class);
    $app->get('/api/mis_sync_products', \App\Action\Api\ProductSyncAction::class);
    $app->get('/api/mis_sync_customers', \App\Action\Api\CustomerSyncAction::class);
    $app->get('/api/mis_sync_sections', \App\Action\Api\SectionSyncAction::class);
    $app->get('/api/mis_sync_defects', \App\Action\Api\DefectSyncAction::class);

    $app->get('/api/label_void_reasons', \App\Action\Api\LabelVoidReasonAction::class);
    $app->post('/api/add_label_void_reason', \App\Action\Api\LabelVoidReasonAddAction::class);
    $app->post('/api/edit_label_void_reason', \App\Action\Api\LabelVoidReasonEditAction::class);
    $app->post('/api/delete_label_void_reason', \App\Action\Api\LabelVoidReasonDeleteAction::class);

    $app->get('/api/printers', \App\Action\Api\PrinterAction::class);

    $app->get('/api/invoices', \App\Action\Api\InvoiceAction::class);
    $app->get('/api/invoice_details', \App\Action\Api\InvoiceDetailAction::class);

};
