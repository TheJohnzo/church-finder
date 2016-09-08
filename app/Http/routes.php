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

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');

//Mapping Piece
Route::get('/map', 'ChurchFinderController@index');
Route::get('/church/{id}', 'ChurchFinderController@churchDetail');
Route::get('/test', 'ChurchFinderController@test');

//TODO Below will need to be protected by login security

Route::get('/admin/church', 'ChurchAdminController@index');
Route::get('/admin/church/new', 'ChurchAdminController@newChurch');
Route::post('/admin/church/new', 'ChurchAdminController@insertChurch');
Route::get('/admin/church/edit/{id}', 'ChurchAdminController@editChurch');
Route::post('/admin/church/edit/{id}', 'ChurchAdminController@updateChurch');

Route::get('/admin/church/{id}/address', 'AddressAdminController@editChurchAddress');
Route::post('/admin/church/{id}/address/{address_id}', 'AddressAdminController@updateChurchAddress')->where('address_id', '[0-9]+');
Route::post('/admin/church/{id}/address/{new}', 'AddressAdminController@insertChurchAddress');
Route::get('/admin/church/{id}/address/delete/{address_id}', 'AddressAdminController@deleteChurchAddress');
Route::post('/admin/church/lookupaddress', 'AddressAdminController@lookupAddresses');

Route::any('/admin/church/{id}/tag', 'ChurchAdminController@editChurchTags');
Route::post('/admin/church/{id}/tag/save', 'ChurchAdminController@saveChurchTags');


Route::get('/admin/tag', 'TagAdminController@index');
Route::get('/admin/tag/new', 'TagAdminController@newTag');
Route::post('/admin/tag/new', 'TagAdminController@insertTag');
Route::get('/admin/tag/edit/{id}', 'TagAdminController@editTag');
Route::post('/admin/tag/edit/{id}', 'TagAdminController@updateTag');
Route::get('/admin/tag/delete/{id}', 'TagAdminController@deleteTag');

Route::get('/admin/church/edit/{id}/tag', 'ChurchAdminController@editChurchTags');

Route::get('/admin/church/{id}/meetingtime', 'MeetingTimeAdminController@index');
Route::get('/admin/church/{id}/meetingtime/new', 'MeetingTimeAdminController@newMeetingTime');
Route::post('/admin/church/{id}/meetingtime/new', 'MeetingTimeAdminController@insertMeetingTime');
Route::get('/admin/church/{id}/meetingtime/edit/{meeting_id}', 'MeetingTimeAdminController@editMeetingTime');
Route::post('/admin/church/{id}/meetingtime/edit/{meeting_id}', 'MeetingTimeAdminController@updateMeetingTime');
Route::get('/admin/church/{id}/meetingtime/delete/{meeting_id}', 'MeetingTimeAdminController@deleteMeetingTime');

Route::get('/admin/organization', 'OrganizationAdminController@index');
Route::get('/admin/organization/new', 'OrganizationAdminController@newOrganization');
Route::post('/admin/organization/new', 'OrganizationAdminController@insertOrganization');
Route::get('/admin/organization/edit/{id}', 'OrganizationAdminController@editOrganization');
Route::post('/admin/organization/edit/{id}', 'OrganizationAdminController@updateOrganization');

//Route::auth();
Auth::routes();
//dev password

