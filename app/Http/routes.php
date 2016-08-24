<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//Mapping Piece
Route::get('/map', 'ChurchFinderController@index');
Route::get('/test', 'ChurchFinderController@test');

//Below will need to be protected by login security

Route::get('/admin/church', 'ChurchAdminController@index');
Route::get('/admin/church/new', 'ChurchAdminController@newChurch');
Route::post('/admin/church/new', 'ChurchAdminController@insertChurch');
Route::get('/admin/church/edit/{id}', 'ChurchAdminController@editChurch');
Route::post('/admin/church/edit/{id}', 'ChurchAdminController@updateChurch');

Route::get('/admin/organization', 'OrganizationAdminController@index');
Route::get('/admin/organization/new', 'OrganizationAdminController@newOrganization');
Route::post('/admin/organization/new', 'OrganizationAdminController@insertOrganization');
Route::get('/admin/organization/edit/{id}', 'OrganizationAdminController@editOrganization');
Route::post('/admin/organization/edit/{id}', 'OrganizationAdminController@updateOrganization');

Route::auth();

Route::get('/', 'HomeController@index');
