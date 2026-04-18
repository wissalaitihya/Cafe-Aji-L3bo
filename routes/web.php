<?php

// ── Home ──
$router->get('/', 'AuthController@loginForm');

// ── Auth ──
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

// ── Dashboards ──
$router->get('/admin/dashboard', 'DashboardController@admin');
$router->get('/player/dashboard', 'DashboardController@player');

// ── Games ──
$router->get('/games', 'GameController@index');
$router->get('/games/create', 'GameController@create');
$router->post('/games', 'GameController@store');
$router->get('/games/{id}', 'GameController@show');
$router->get('/games/{id}/edit', 'GameController@edit');
$router->post('/games/{id}/update', 'GameController@update');
$router->post('/games/{id}/delete', 'GameController@destroy');

// ── Reservations ──
$router->get('/reservations', 'ReservationController@index');
$router->get('/reservations/availability', 'ReservationController@availability');
$router->get('/reservations/create', 'ReservationController@create');
$router->post('/reservations', 'ReservationController@store');
$router->get('/reservations/my', 'ReservationController@myReservations');
$router->post('/reservations/{id}/status', 'ReservationController@updateStatus');
$router->post('/reservations/{id}/cancel', 'ReservationController@cancelByPlayer');

// ── Tables (admin CRUD) ──
$router->get('/tables', 'TableController@index');
$router->get('/tables/create', 'TableController@create');
$router->post('/tables', 'TableController@store');
$router->get('/tables/{id}/edit', 'TableController@edit');
$router->post('/tables/{id}/update', 'TableController@update');
$router->post('/tables/{id}/delete', 'TableController@destroy');

// ── API: available tables & games for a date+time (AJAX) ──
$router->get('/api/available-tables', 'ReservationController@apiAvailableTables');
$router->get('/api/available-games', 'ReservationController@apiAvailableGames');

// ── Sessions ──
$router->get('/sessions', 'SessionController@dashboard');
$router->get('/sessions/create', 'SessionController@create');
$router->post('/sessions', 'SessionController@store');
$router->post('/sessions/{id}/end', 'SessionController@end');
$router->get('/sessions/history', 'SessionController@history');