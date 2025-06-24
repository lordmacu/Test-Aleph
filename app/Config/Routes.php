<?php

// Página principal
$routes->get('/',  'CategoriaController::index');

// Categorías
$routes->get('categorias', 'CategoriaController::index');

// Registros CMDB por categoría
$routes->get('cmdb/(:num)', 'CmdbController::index/$1');

// Más adelante puedes agregar estas si las necesitas:
$routes->post('cmdb/importar', 'CmdbController::importar');
$routes->get('cmdb/exportar/(:num)', 'CmdbController::exportar/$1');
$routes->get('cmdb/importar-vista/(:num)', 'CmdbController::importarVista/$1');
$routes->post('/categorias/importar-ajax', 'CategoriaController::importarCategoriasAjax');
