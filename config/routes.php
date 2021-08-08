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
    
    $app->get('/lots', \App\Action\Web\LotAction::class)->add(UserAuthMiddleware::class);
    $app->post('/add_lot', \App\Action\Web\LotAddAction::class)->add(UserAuthMiddleware::class);
    $app->post('/edit_lot', \App\Action\Web\LotEditAction::class)->add(UserAuthMiddleware::class);
    $app->post('/delete_lot', \App\Action\Web\LotDeleteAction::class)->add(UserAuthMiddleware::class);
    $app->post('/print_lot', \App\Action\Web\LotPrintAction::class)->add(UserAuthMiddleware::class);
    
    $app->get('/labels', \App\Action\Web\LabelAction::class)->add(UserAuthMiddleware::class);
    $app->post('/add_label', \App\Action\Web\LabelAddAction::class)->add(UserAuthMiddleware::class);
    $app->post('/edit_label', \App\Action\Web\LabelEditAction::class)->add(UserAuthMiddleware::class);
    $app->post('/delete_label', \App\Action\Web\LabelDeleteAction::class)->add(UserAuthMiddleware::class);

    $app->post('/api/login', \App\Action\ApiLoginSubmitAction::class);
    
    $app->get('/api/lots', \App\Action\Api\LotAction::class);

    $app->get('/api/customers', \App\Action\Api\CustomerAction::class);

    $app->get('/api/products', \App\Action\Api\ProductAction::class);
    
    $app->get('/api/lot_defects', \App\Action\Api\LotDefectAction::class);
    $app->get('/api/defects', \App\Action\Api\DefectAction::class);
    $app->post('/api/add_lot_defect', \App\Action\Api\LotDefectAddAction::class);
    $app->post('/api/delete_lot_defect', \App\Action\Api\LotDefectDeleteAction::class);
    $app->post('/api/edit_lot_defects', \App\Action\Api\LotDefectEditAction::class);
};
