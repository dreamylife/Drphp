<?php

Route::get('/','Index@xxx');

Route::get('blog', 'BlogController@index');
Route::get('blog/all', 'BlogController@index');

Route::post('user/setting/{id}','User@set');

//print_r(Route::all()) ;