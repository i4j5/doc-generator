<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    // return redirect()->route('home');
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('document-templates/{document_template}/download', 'DocumentTemplateController@download');
Route::resource('document-templates', 'DocumentTemplateController');

Route::get('documents/{document}/download', 'DocumentController@download');
Route::resource('documents', 'DocumentController')->except([
  'update', 'edit'
]);
