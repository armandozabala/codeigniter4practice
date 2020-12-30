<?php namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->get('hello', 'Home::hello');


$routes->post('sumar', 'Home::sumar');

//insertar
$routes->post('insertar', 'Home::insertar');


$routes->post('tareaseliminadas', 'Home::getTareasEliminadas');
//delete
$routes->post('eliminar', 'Home::eliminar');
//update
$routes->post('editar', 'Home::editar');
//buscar
$routes->post('buscar', 'Home::buscar');


$routes->post('titulos', 'Home::tareasTitulo');


//excel
$routes->post('subir', 'Excel::uploadExcel');
$routes->post('cargarcliente', 'Excel::uploadExcelCliente');
$routes->post('downloadexcel', 'Excel::exportarExcel');




$routes->post('saludo', 'Home::saludo');
$routes->get('saludo', 'Home::saludo');


//insertar usuario
$routes->post('usuario', 'User::insertar');
$routes->post('login', 'User::login');

$routes->post('updateorden', 'User::updateOrden');
$routes->post('updatecustomers', 'User::updateCustomers');
$routes->post('updateruta', 'User::updateRuta');
$routes->post('clientesxrutas', 'User::getClientesRutas');
$routes->post('customersnoorden', 'User::getClientesNoOrden');
$routes->post('customersnoroute', 'User::getClientesNoRuta');
$routes->post('deletecustomer', 'User::deleteCustomer');
$routes->get('totalcustomer', 'User::getCountCustomer');


//rutas
$routes->get('rutas', 'Rutas::getRutas');
$routes->post('auth', 'Auth::create');



//ordenes
$routes->post('ordenestoday', 'Orden::getOrdenesToday');
$routes->post('deleteorder', 'Orden::deleteOrden');
$routes->post('deleteallorden', 'Orden::deleteAllOrden');



//JWT
$routes->resource('api/auth', ['controller' => 'Auth']);
$routes->resource('api/home', ['controller' => 'Home']);
$routes->resource('api/excel', ['controller' => 'Excel']);
$routes->resource('api/rutas', ['controller' => 'Rutas']);
$routes->resource('api/user', ['controller' => 'User']);
$routes->resource('api/orden', ['controller' => 'Orden']);

/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
