<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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
$router->get('/hola', function () use ($router) {
    return 'hola tu2';
});
$router->post('/login', 'LoginController@login');


// GRUPO DE RUTAS QUE NECESITAN AUTENTICACION
$router->group(['middleware' => 'auth'], function () use ($router) {

    $router->get('/logout', 'LoginController@logout');

    // EMPRESA
    $router->get('/empresa', 'EmpresaController@get');
    $router->get('/empresa/{id}', 'EmpresaController@find');
    $router->post('/empresa', 'EmpresaController@new');
    $router->put('/empresa/{id}', 'EmpresaController@put');
    $router->patch('/empresa/{id}', 'EmpresaController@patch');
    $router->delete('/empresa/{id}', 'EmpresaController@delete');

    // TIPOPRODUCTO
    $router->get('/tipoproducto', 'TipoProductoController@get');
    $router->get('/tipoproducto/{id}', 'TipoProductoController@find');
    $router->post('/tipoproducto', 'TipoProductoController@new');
    $router->put('/tipoproducto/{id}', 'TipoProductoController@put');
    $router->patch('/tipoproducto/{id}', 'TipoProductoController@patch');
    $router->delete('/tipoproducto/{id}', 'TipoProductoController@delete');
	
	// TIPOCATEGORIA
    $router->get('/tipocategoria', 'TipoCategoriaController@get');
    $router->get('/tipocategoria/{id}', 'TipoCategoriaController@find');
    $router->post('/tipocategoria', 'TipoCategoriaController@new');
    $router->put('/tipocategoria/{id}', 'TipoCategoriaController@put');
    $router->patch('/tipocategoria/{id}', 'TipoCategoriaController@patch');
    $router->delete('/tipocategoria/{id}', 'TipoCategoriaController@delete');
	
	// CLASE
    $router->get('/clase', 'ClaseController@get');
    $router->get('/clase/{id}', 'ClaseController@find');
    $router->post('/clase', 'ClaseController@new');
    $router->put('/clase/{id}', 'ClaseController@put');
    $router->patch('/clase/{id}', 'ClaseController@patch');
    $router->delete('/clase/{id}', 'ClaseController@delete');
	
    // GRUPO
    $router->get('/grupo', 'GrupoController@get');
    $router->get('/grupo/{id}', 'GrupoController@find');
    $router->post('/grupo', 'GrupoController@new');
    $router->put('/grupo/{id}', 'GrupoController@put');
    $router->patch('/grupo/{id}', 'GrupoController@patch');
    $router->delete('/grupo/{id}', 'GrupoController@delete');
	
	//FEEDBACK
	$router->get('/feedback', 'FeedbackController@get');
    $router->get('/feedback/{id}', 'FeedbackController@find');
    $router->post('/feedback', 'FeedbackController@new');
    $router->put('/feedback/{id}', 'FeedbackController@put');
    $router->patch('/feedback/{id}', 'FeedbackController@patch');
    $router->delete('/feedback/{id}', 'FeedbackController@delete');
	
	//SERVICIO
	$router->get('/servicio', 'ServicioController@get');
    $router->get('/servicio/{id}', 'ServicioController@find');
    $router->post('/servicio', 'ServicioController@new');
    $router->put('/servicio/{id}', 'ServicioController@put');
    $router->patch('/servicio/{id}', 'ServicioController@patch');
    $router->delete('/servicio/{id}', 'ServicioController@delete');
	
	//OFERTA
	$router->get('/oferta', 'OfertaController@get');
    $router->get('/oferta/{id}', 'OfertaController@find');
    $router->post('/oferta', 'OfertaController@new');
    $router->put('/oferta/{id}', 'OfertaController@put');
    $router->patch('/oferta/{id}', 'OfertaController@patch');
    $router->delete('/oferta/{id}', 'OfertaController@delete');
	
	//NEWS
	$router->get('/news', 'NewsController@get');
    $router->get('/news/{id}', 'NewsController@find');
    $router->post('/news', 'NewsController@new');
    $router->put('/news/{id}', 'NewsController@put');
    $router->patch('/news/{id}', 'NewsController@patch');
    $router->delete('/news/{id}', 'NewsController@delete');
	
	//ATENCION
	$router->get('/atencion', 'AtencionController@get');
    $router->get('/atencion/{id}', 'AtencionController@find');
    $router->post('/atencion', 'AtencionController@new');
    $router->put('/atencion/{id}', 'AtencionController@put');
    $router->patch('/atencion/{id}', 'AtencionController@patch');
    $router->delete('/atencion/{id}', 'AtencionController@delete');
	
	// PRODUCTO
    $router->get('/producto', 'ProductoController@get');
    $router->get('/producto/{id}', 'ProductoController@find');
    $router->get('/paginated/producto', 'ProductoController@paginate');
    $router->post('/producto', 'ProductoController@new');
    $router->put('/producto/{id}', 'ProductoController@put');
    $router->patch('/producto/{id}', 'ProductoController@patch');
    $router->delete('/producto/{id}', 'ProductoController@delete');
    $router->get('/product-tree', 'ProductoController@tree');
	
	// CIUDAD
    $router->get('/ciudad', 'CiudadController@get');
    $router->get('/ciudad/{id}', 'CiudadController@find');
    $router->post('/ciudad', 'CiudadController@new');
    $router->put('/ciudad/{id}', 'CiudadController@put');
    $router->patch('/ciudad/{id}', 'CiudadController@patch');
    $router->delete('/ciudad/{id}', 'CiudadController@delete');

	
	
});