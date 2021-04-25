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


$router->get('/paginated/producto', 'ProductoController@paginate');
$router->get('/paginated/near_empresa', 'EmpresaController@paginateNear');
$router->get('/paginated/empresa', 'EmpresaController@paginate');
$router->get('/product-tree', 'ProductoController@tree');
$router->get('/related-products/{id}', 'ProductoController@related');
$router->post('/usuario', 'UsuarioController@new');
$router->post('/upload/empresa', 'EmpresaController@upload');
$router->post('/upload/producto', 'ProductoController@upload');
$router->post('/upload/evento', 'EventoController@upload');
$router->post('/upload/news', 'NewsController@upload');
$router->post('/upload/oferta', 'OfertaController@upload');


// GRUPO DE RUTAS QUE NECESITAN AUTENTICACION
$router->group(['middleware' => 'auth'], function () use ($router) {

    $router->get('/logout', 'LoginController@logout');

    // EMPRESA
    $router->get('/empresa', 'EmpresaController@get');
    $router->get('/empresa/{id}', 'EmpresaController@find');
    $router->get('/empresa-comentarios/{id}', 'EmpresaController@findWithComments');
    $router->post('/empresa', 'EmpresaController@new');
    $router->put('/empresa/{id}', 'EmpresaController@put');
    $router->patch('/empresa/{id}', 'EmpresaController@patch');
    $router->delete('/empresa/{id}', 'EmpresaController@delete');
    $router->get('/prepare/empresa', 'EmpresaController@prepareNew');

    // TIPOPRODUCTO
    $router->get('/tipoproducto', 'TipoProductoController@get');
    $router->get('/tipoproducto/{id}', 'TipoProductoController@find');
    $router->post('/tipoproducto', 'TipoProductoController@new');
    $router->put('/tipoproducto/{id}', 'TipoProductoController@put');
    $router->patch('/tipoproducto/{id}', 'TipoProductoController@patch');
    $router->delete('/tipoproducto/{id}', 'TipoProductoController@delete');
    $router->get('/paginated/tipoproducto', 'TipoProductoController@paginate');
	
	// TIPOCATEGORIA
    $router->get('/tipocategoria', 'TipoCategoriaController@get');
    $router->get('/tipocategoria/{id}', 'TipoCategoriaController@find');
    $router->post('/tipocategoria', 'TipoCategoriaController@new');
    $router->put('/tipocategoria/{id}', 'TipoCategoriaController@put');
    $router->patch('/tipocategoria/{id}', 'TipoCategoriaController@patch');
    $router->delete('/tipocategoria/{id}', 'TipoCategoriaController@delete');
    $router->get('/paginated/tipocategoria', 'TipoCategoriaController@paginate');
	
	// CLASE
    $router->get('/clase', 'ClaseController@get');
    $router->get('/clase/{id}', 'ClaseController@find');
    $router->post('/clase', 'ClaseController@new');
    $router->put('/clase/{id}', 'ClaseController@put');
    $router->patch('/clase/{id}', 'ClaseController@patch');
    $router->delete('/clase/{id}', 'ClaseController@delete');
    $router->get('/paginated/clase', 'ClaseController@paginate');
	
    // GRUPO
    $router->get('/grupo', 'GrupoController@get');
    $router->get('/grupo/{id}', 'GrupoController@find');
    $router->post('/grupo', 'GrupoController@new');
    $router->put('/grupo/{id}', 'GrupoController@put');
    $router->patch('/grupo/{id}', 'GrupoController@patch');
    $router->delete('/grupo/{id}', 'GrupoController@delete');
    $router->get('/paginated/grupo', 'GrupoController@paginate');
	
    // EMPGRUPO
    $router->get('/grupoempresa', 'GrupoEmpresaController@get');
    $router->get('/grupoempresa/{id}', 'GrupoEmpresaController@find');
    $router->post('/grupoempresa', 'GrupoEmpresaController@new');
    $router->put('/grupoempresa/{id}', 'GrupoEmpresaController@put');
    $router->patch('/grupoempresa/{id}', 'GrupoEmpresaController@patch');
    $router->delete('/grupoempresa/{id}', 'GrupoEmpresaController@delete');
    $router->get('/paginated/grupoempresa', 'GrupoEmpresaController@paginate');
	
    // DETALLESERVICIO
    $router->get('/detalle_servicio', 'Detalle_ServicioController@get');
    $router->get('/detalle_servicio/{id}', 'Detalle_ServicioController@find');
    $router->post('/detalle_servicio', 'Detalle_ServicioController@new');
    $router->put('/detalle_servicio/{id}', 'Detalle_ServicioController@put');
    $router->patch('/detalle_servicio/{id}', 'Detalle_ServicioController@patch');
    $router->delete('/detalle_servicio/{id}', 'Detalle_ServicioController@delete');
    $router->get('/paginated/detalle_servicio', 'Detalle_ServicioController@paginate');
	
    //FEEDBACK
	$router->get('/feedback', 'FeedBackController@get');
    $router->get('/feedback/{id}', 'FeedBackController@find');
    $router->post('/feedback', 'FeedBackController@new');
    $router->put('/feedback/{id}', 'FeedBackController@put');
    $router->patch('/feedback/{id}', 'FeedBackController@patch');
    $router->delete('/feedback/{id}', 'FeedBackController@delete');
    $router->get('/paginated/feedback', 'FeedBackController@paginate');
	
	
	//SERVICIO
	$router->get('/servicio', 'ServicioController@get');
    $router->get('/servicio/{id}', 'ServicioController@find');
    $router->post('/servicio', 'ServicioController@new');
    $router->put('/servicio/{id}', 'ServicioController@put');
    $router->patch('/servicio/{id}', 'ServicioController@patch');
    $router->delete('/servicio/{id}', 'ServicioController@delete');
    $router->get('/paginated/servicio', 'ServicioController@paginate');
	
	//OFERTA
	$router->get('/oferta', 'OfertaController@get');
    $router->get('/oferta/{id}', 'OfertaController@find');
    $router->post('/oferta', 'OfertaController@new');
    $router->put('/oferta/{id}', 'OfertaController@put');
    $router->patch('/oferta/{id}', 'OfertaController@patch');
    $router->delete('/oferta/{id}', 'OfertaController@delete');
    $router->get('/paginated/oferta', 'OfertaController@paginate');
	
	//NEWS
	$router->get('/news', 'NewsController@get');
    $router->get('/news/{id}', 'NewsController@find');
    $router->post('/news', 'NewsController@new');
    $router->put('/news/{id}', 'NewsController@put');
    $router->patch('/news/{id}', 'NewsController@patch');
    $router->delete('/news/{id}', 'NewsController@delete');
    $router->get('/paginated/news', 'NewsController@paginate');
    
    //EVENTOS
	$router->get('/evento', 'EventoController@get');
    $router->get('/evento/{id}', 'EventoController@find');
    $router->post('/evento', 'EventoController@new');
    $router->put('/evento/{id}', 'EventoController@put');
    $router->patch('/evento/{id}', 'EventoController@patch');
    $router->delete('/evento/{id}', 'EventoController@delete');
    $router->get('/paginated/evento', 'EventoController@paginate');
    
    //ATENCION
	$router->get('/atencion', 'AtencionController@get');
    $router->get('/atencion/{id}', 'AtencionController@find');
    $router->post('/atencion', 'AtencionController@new');
    $router->put('/atencion/{id}', 'AtencionController@put');
    $router->patch('/atencion/{id}', 'AtencionController@patch');
    $router->delete('/atencion/{id}', 'AtencionController@delete');
    $router->get('/paginated/atencion', 'AtencionController@paginate');
	
	// PRODUCTO
    $router->get('/producto', 'ProductoController@get');
    $router->get('/producto/{id}', 'ProductoController@find');
    $router->post('/producto', 'ProductoController@new');
    $router->post('/producto-app', 'ProductoController@newFromApp');
    $router->put('/producto/{id}', 'ProductoController@put');
    $router->patch('/producto/{id}', 'ProductoController@patch');
    $router->delete('/producto/{id}', 'ProductoController@delete');
	
	// CIUDAD
    $router->get('/ciudad', 'CiudadController@get');
    $router->get('/ciudad/{id}', 'CiudadController@find');
    $router->post('/ciudad', 'CiudadController@new');
    $router->put('/ciudad/{id}', 'CiudadController@put');
    $router->patch('/ciudad/{id}', 'CiudadController@patch');
    $router->delete('/ciudad/{id}', 'CiudadController@delete');
    $router->get('/paginated/ciudad', 'CiudadController@paginate');

	// USERS
    $router->get('/usuario', 'UsuarioController@get');
    $router->get('/usuario/{id}', 'UsuarioController@find');
    $router->put('/usuario/{id}', 'UsuarioController@put');
    $router->patch('/usuario/{id}', 'UsuarioController@patch');
    $router->delete('/usuario/{id}', 'UsuarioController@delete');

	// REGISTRO
    $router->get('/registro', 'RegistroController@get');
    $router->get('/registro/{id}', 'RegistroController@find');
    $router->put('/registro/{id}', 'RegistroController@put');
    $router->post('/registro', 'RegistroController@new');
    $router->patch('/registro/{id}', 'RegistroController@patch');
    $router->delete('/registro/{id}', 'RegistroController@delete');

	// REGISTRO
    $router->get('/comentario', 'ComentarioController@get');
    $router->get('/comentario/{id}', 'ComentarioController@find');
    $router->put('/comentario/{id}', 'ComentarioController@put');
    $router->post('/comentario', 'ComentarioController@new');
    $router->patch('/comentario/{id}', 'ComentarioController@patch');
    $router->delete('/registro/{id}', 'ComentarioController@delete');

	// PEDIDO
    $router->post('/full/pedido', 'PedidoController@full');
	$router->get('/pedido', 'PedidoController@get');
	$router->get('/toconfirm/pedido/{phone}', 'PedidoController@toConfirm');
    $router->post('/confirm/pedido', 'PedidoController@confirm');
});