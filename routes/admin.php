<?php
Route::get('/', ['as' => 'root', 'uses' => 'ArticlesController@index']);
Route::resource('articles', 'ArticlesController');
Route::resource('pages', 'PagesController', ['except' => ['create', 'index']]);
Route::get('pages/{parent_id}/create', 'PagesController@create')->name('pages.create');
Route::get('pages/{parent_id}/index', 'PagesController@index')->name('pages.index');
Route::resource('settings', 'SettingsController');
Route::resource('users', 'UsersController');
// // Articles : reorder
Route::post('articles/reorder', 'ArticlesController@reorder')->name('articles.reorder');
// Medias management
Route::get('medias/index/{media_type}/{mediatable_type}/{article_id}', 'mediasController@index')->name('medias.index');
Route::post('medias/store/{mediatable_type}/{article_id?}', 'mediasController@store')->name('medias.store');
