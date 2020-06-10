<?php

use Illuminate\Http\Request;
use App\Services\JsonRpcService;
use App\Http\Controllers\Controller;

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

$router->get('/', function (Request $request, JsonRpcService $server, Controller $controller) {
    return $server->handle($request, $controller);
});

$router->post('/', function (Request $request, JsonRpcService $server, Controller $controller) {
    return $server->handle($request, $controller);
});

