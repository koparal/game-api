<?php

// Define routes to API

Route::run('POST','/api/v1/user/signup', 'API/v1/AuthController@signUp');
Route::run('POST','/api/v1/user/signin', 'API/v1/AuthController@signIn');
Route::run('POST','/api/v1/endgame', 'API/v1/GameController@endGame');
Route::run('GET','/api/v1/leaderboard', 'API/v1/GameController@leaderBoard');
