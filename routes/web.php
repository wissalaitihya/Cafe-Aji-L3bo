<?php

// ── Home ──
$router->get('/', 'AuthController@loginForm');

// ── Auth ──
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

// ── Reservations ──
$router->get('/reservations', 'ReservationController@index');
$router->get('/reservations/availability', 'ReservationController@availability');
$router->get('/reservations/create', 'ReservationController@create');
$router->post('/reservations', 'ReservationController@store');
$router->get('/reservations/my', 'ReservationController@myReservations');
$router->post('/reservations/{id}/status', 'ReservationController@updateStatus');

