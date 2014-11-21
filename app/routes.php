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

Route::get('descriptors', 'QuestionOneController@getDescriptors');

// Question routes
Route::get('description', 'QuestionOneController@getDescriptions');
Route::get('consecutive', 'QuestionTwoController@checkConsecutive');
Route::get('news', 'QuestionThreeController@getTheNews');
