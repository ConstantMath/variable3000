<?php
// Admin specific routes
Route::get('/', ['as' => 'root', 'uses' => 'ArticlesController@index']);
Route::get('users/getdata', 'UsersController@getDataTable')->name('users.getdata');
Route::resource('users', 'UsersController');
Route::get('roles/getdata', 'RoleController@getDataTable')->name('roles.getdata');
Route::resource('roles', 'RoleController');
Route::get('permissions/getdata', 'PermissionController@getDataTable')->name('permissions.getdata');
Route::resource('permissions', 'PermissionController');
Route::resource('roles', 'RoleController');
Route::get('articles/getdata', 'ArticlesController@getDataTable')->name('articles.getdata');
Route::resource('articles', 'ArticlesController');


Route::get('pages/{parent_id}/getdata', 'PagesController@getDataTable')->name('pages.getdata');
Route::resource('pages', 'PagesController', ['except' => ['create', 'index']]);
Route::get('pages/{parent_id}/create', 'PagesController@create')->name('pages.create');
Route::get('pages/{parent_id}/index', 'PagesController@index')->name('pages.index');
Route::post('pages/reorder/{id}', 'PagesController@reorder')->name('pages.reorder');
Route::resource('settings', 'SettingsController');
//taxonomies
Route::get('taxonomies/getdata', 'TaxonomiesController@getDataTable')->name('taxonomies.getdata');
Route::resource('taxonomies', 'TaxonomiesController', ['except' => ['create']]);
Route::get('taxonomies/create/{parent_id}', 'TaxonomiesController@create')->name('taxonomies.create');
Route::post('taxonomies/reorder/{id}', 'TaxonomiesController@reorder')->name('taxonomies.reorder');
Route::post('{table_type}/reorder', 'AdminController@orderObject')->name('reorder');
// Medias
Route::get('medias/getdata', 'MediasController@getDataTable')->name('medias.getdata');
Route::resource('medias', 'MediasController');
Route::get('medias/quickdestroy/{id}', 'MediasController@quickDestroy')->name('medias.quickdestroy');
Route::get('mediasArticle/{model_type}/{article_id}/{collection_name}', 'MediasController@mediasArticle')->name('medias.article');
Route::post('medias/storeandlink/{mediatable_type}/{article_id?}', 'MediasController@storeAndLink')->name('medias.storeandlink');
Route::post('medias/reorder/{model_type}/{article_id}/{collection_name}', 'MediasController@reorder')->name('medias.reorder');
//Route::post('/admin/articles/{id?}/addmanymedia', 'Admin\ArticlesMediasController@addManyMedia')->name('admin.articles.addmanymedia');
// Route::post('medias/get', 'MediasController@getFromArray');
Route::post('medias/ajaxUpdate/{mediatable_type}', 'MediasController@AjaxUpdate')->name('medias.ajaxupdate');
// Route::post('fileupload', 'MediasController@fileUpload')->name('fileupload');
// Datatables
Route::get('datatable', 'DataTablesController@datatable');
Route::get('datatable/getArticles', 'DataTablesController@getArticles')->name('datatable/getdata');
