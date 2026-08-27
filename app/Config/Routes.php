<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Home/Dashboard
$routes->get('/', 'Home::index');
$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/dashboard/data', 'Dashboard::getData');

// Auth
$routes->get('/auth', 'Auth::index');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/auth/logout', 'Auth::logout');

// Booking
$routes->get('/booking', 'Booking::index');
$routes->get('/booking/create', 'Booking::create');
$routes->post('/booking/store', 'Booking::store');
$routes->get('/booking/detail/(:num)', 'Booking::detail/$1');
$routes->post('/booking/update-status/(:num)', 'Booking::updateStatus/$1');

// Approval
$routes->get('/approval', 'Approval::index');
$routes->get('/approval/detail/(:num)', 'Approval::detail/$1');
$routes->post('/approval/process/(:num)', 'Approval::process/$1');

// Vehicle
$routes->get('/vehicle', 'Vehicle::index');
$routes->get('/vehicle/detail/(:num)', 'Vehicle::detail/$1');
$routes->post('/vehicle/add-fuel-log', 'Vehicle::addFuelLog');
$routes->post('/vehicle/add-service-log', 'Vehicle::addServiceLog');

// Report
$routes->get('/report', 'Report::index');
$routes->get('/report/export', 'Report::export');
$routes->get('/report/export-excel', 'Report::exportExcel');

// Logs
$routes->get('/logs', 'Logs::index');
$routes->get('/logs/action/(:segment)', 'Logs::byAction/$1');
