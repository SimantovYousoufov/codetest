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

Route::get('db', 'QuestionOneController@getDatabaseData');
Route::get('descriptors', 'QuestionOneController@getDescriptors');

Route::get('description', 'QuestionOneController@getDescriptions');

Route::get('twoelem', 'QuestionOneController@checkTwoElementCode');

Route::get('consecutive', 'QuestionTwoController@checkConsecutive');
