<?php

use App\Http\Controllers\MemberController;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->post('/register', 'MemberController@register');
$router->post('/login', 'MemberController@authenticate');

//$router->post('/annotate', 'ProcessorController@annotateUrls');
// protected routes.
$router->group(
    ['middleware' => 'jwt.auth:user',], 
    function($router)  {
        //User profile
        $router->post('/annotate', 'ProcessorController@annotateUrls');
        $router->get('/members', 'MemberController@getMemberAll');
        $router->get('/annotation', 'ProcessorController@getAnnotation');
        
    });