<?php
$api = app('Dingo\Api\Routing\Router');

// Version 1 of our API
$api->version('v1', function ($api) {

	// Set our namespace for the underlying routes
	$api->group(['namespace' => 'Api\Controllers', 'middleware' => '\Barryvdh\Cors\HandleCors::class'], function ($api) {

		// Login route
		$api->post('jwt/login', 'AuthController@authenticate');
		$api->post('register', 'AuthController@register'); //TODO this is boilerpate, update it as necessary

		// All routes in here are protected and thus need a valid token
		//$api->group( [ 'protected' => true, 'middleware' => 'jwt.refresh' ], function ($api) {
		$api->group( [ 'middleware' => 'jwt.auth' ], function ($api) {

			$api->get('users/me', 'AuthController@me');
			$api->get('validate_token', 'AuthController@validateToken');
			$api->get('jwt/refresh', 'AuthController@refreshToken');
			
		});

	});

});
