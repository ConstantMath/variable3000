<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Auth
Auth::routes();

// Language switcher
Route::get('lang/{language}', ['as' => 'lang.switch', 'uses' => 'LanguageController@switchLang']);
// Homepage
Route::get('/', 'HomeController@index')->name('homepage');
// Pages index
Route::get('/pages/{article_slug}', 'PagesController@show')->name('pages.show');
Route::get('/pages', 'PagesController@show')->name('pages.index');
// Article : view
Route::get('/{article_slug}', 'ArticlesController@show')->name('articles.show');
