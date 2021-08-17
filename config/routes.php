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
    $app->get('/products', \App\Action\Web\ProductAction::class)->add(UserAuthMiddleware::class);
    $app->get('/lots', \App\Action\Web\LotAction::class)->add(UserAuthMiddleware::class);
    $app->get('/merge_packs', \App\Action\Web\MergePackAction::class)->add(UserAuthMiddleware::class);
    $app->post('/add_lot', \App\Action\Web\LotAddAction::class)->add(UserAuthMiddleware::class);
    $app->post('/edit_lot', \App\Action\Web\LotEditAction::class)->add(UserAuthMiddleware::class);
    $app->post('/delete_lot', \App\Action\Web\LotDeleteAction::class)->add(UserAuthMiddleware::class);
    $app->post('/print_lot', \App\Action\Web\LotPrintAction::class)->add(UserAuthMiddleware::class);
    
    $app->post('/api/login', \App\Action\ApiLoginSubmitAction::class);
    $app->get('/api/lots', \App\Action\Api\LotAction::class);
    $app->get('/api/products', \App\Action\Api\ProductAction::class);
    $app->get('/api/customers', \App\Action\Api\CustomerAction::class);
    $app->get('/api/defects', \App\Action\Api\DefectAction::class);
    
    $app->post('/api/add_defect', \App\Action\Api\DefectAddAction::class);
    $app->post('/api/delete_defect', \App\Action\Api\DefectDeleteAction::class);
    $app->post('/api/edit_defect', \App\Action\Api\DefectEditAction::class);

    
    $app->get('/api/lot_defects', \App\Action\Api\LotDefectAction::class);
    $app->post('/api/add_lot_defect', \App\Action\Api\LotDefectAddAction::class);
    $app->post('/api/delete_lot_defect', \App\Action\Api\LotDefectDeleteAction::class);
    $app->post('/api/edit_lot_defect', \App\Action\Api\LotDefectEditAction::class);

    $app->get('/api/merge_packs', \App\Action\Api\MergePackAction::class);
    $app->get('/api/merge_labels', \App\Action\Api\MergeLabelAction::class);
    $app->get('/api/merge_pack_details', \App\Action\Api\MergePackDetailAction::class);

    $app->get('/api/label_nonfullys', \App\Action\Api\LabelNonfullyAction::class);
    $app->post('/api/update_label_non_to_merge', \App\Action\Api\UpdateLabelNonToMergeAction::class);


    // merge label
    $app->get('/api/select_product_to_merges', \App\Action\Api\SelectProductToMergeAction::class);
    $app->get('/api/labels', \App\Action\Api\LabelAction::class);
    $app->get('/api/label_pack_merges', \App\Action\Api\LabelPackMergeAction::class);
    // $app->get('/api/select_merge_pack_id', \App\Action\Api\SelectMergePackIDAction::class);
    // $app->get('/api/labels', \App\Action\Api\LabelAction::class);

    $app->post('/api/add_merge_packs', \App\Action\Api\MergePackAddAction::class);
    $app->post('/api/up_status_merge_packs', \App\Action\Api\UpStatusMergePackAction::class);
    $app->post('/api/up_status_merge_labels', \App\Action\Api\UpStatusMergeLabelAction::class);
    $app->post('/api/up_status_mergings', \App\Action\Api\UpStatusMergingAction::class);
  
};
