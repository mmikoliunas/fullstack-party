<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get( '/', 'PagesController@showIndex' );
Route::get( '/github_callback', 'PagesController@processGithubCallback' );
Route::get( '/main', 'PagesController@showMain' );
Route::get( '/issue', 'PagesController@showIssue' );
Route::get( '/logout', 'PagesController@logout' );