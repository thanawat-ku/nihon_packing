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
    $app->get('/label_merges', \App\Action\Web\MergeLabelAction::class)->add(UserAuthMiddleware::class);
    $app->get('/merge_detail', \App\Action\Web\MergeDetailAction::class)->add(UserAuthMiddleware::class);
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

    $app->get('/labels', \App\Action\Web\LabelAction::class)->add(UserAuthMiddleware::class);
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

    //---------------------------Api-------------------------------

    $app->post('/api/login', \App\Action\ApiLoginSubmitAction::class);
    $app->get('/api/merges', \App\Action\Api\MergeAction::class);

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

    
    $app->get('/api/tags', \App\Action\Api\TagAction::class);
    $app->post('/api/register_tag', \App\Action\Api\TagRegisterAction::class);
    $app->post('/api/gen_tags', \App\Action\Api\GenTagBarcodeNoAction::class);

    $app->get('/api/split_labels',  \App\Action\Api\SplitLabelAction::class);
    $app->post('/api/add_split_label', \App\Action\Api\SplitLabelAddAction::class);
    $app->post('/api/register_split_label', \App\Action\Api\SplitLabelRegisterAction::class);

    $app->get('/api/split_label_detail',  \App\Action\Api\SplitLabelDetailAction::class);
    $app->post('/api/add_split_label_detail', \App\Action\Api\SplitLabelDetailAddAction::class);
    
    $app->get('/api/merge_packs', \App\Action\Api\MergePackAction::class);
    $app->post('/api/merge_label', \App\Action\Api\MergeLabelAction::class);
    $app->get('/api/merge_pack_details', \App\Action\Api\MergePackDetailAction::class);

    $app->get('/api/label_nonfullys', \App\Action\Api\LabelNonfullyAction::class);
    $app->post('/api/update_label_non_to_merge', \App\Action\Api\UpdateLabelNonToMergeAction::class);
    
    $app->get('/api/label_pack_merges', \App\Action\Api\LabelPackMergeAction::class);
    $app->get('/api/check_labels', \App\Action\Api\CheckLabelAction::class);
    $app->get('/api/select_merge_pack_id', \App\Action\Api\SelectMergePackIDAction::class);
    $app->get('/api/create_mn_from_lb', \App\Action\Api\CreateMergeNoFromLabelAction::class);

    $app->post('/api/add_merge_packs', \App\Action\Api\MergePackAddAction::class);
    $app->post('/api/up_status_merge_packs', \App\Action\Api\UpStatusMergePackAction::class);
    $app->post('/api/up_status_merge_labels', \App\Action\Api\UpStatusMergeLabelAction::class);
    $app->post('/api/up_status_mergings', \App\Action\Api\UpStatusMergingAction::class);
    $app->post('/api/up_status_merged', \App\Action\Api\UpStatusMergedAction::class);
    $app->post('/api/add_merge_pack_detail', \App\Action\Api\MergePackDetailAddAction::class);
    $app->post('/api/print_label_merge_packs', \App\Action\Api\PrintLabelMergePackAction::class);
    $app->post('/api/add_mn_from_lb', \App\Action\Api\AddMergeNoFromLabelAction::class);
    $app->post('/api/check_merge_pack_id', \App\Action\Api\CheckMergePackIDAction::class);
    $app->post('/api/cancel_all_labels', \App\Action\Api\CancelAllLabelAction::class);
    $app->post('/api/get_qty_mp_labels', \App\Action\Api\GetQtyMpLabelAction::class);
    $app->post('/api/delete_merge_pack_detail', \App\Action\Api\MergePackDetailDeleteAction::class);
    $app->post('/api/get_qty_scan', \App\Action\Api\GetQtyScanAction::class);
    


    $app->get('binary_search', \App\Action\Api\Binary_search::class);
    
};
