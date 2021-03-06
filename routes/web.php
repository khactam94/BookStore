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
//front-end page
Route::name('homepage')->get('/', 'FrontEnd\HomeController@index');

Route::name('contact')->get('/contact', 'FrontEnd\HomeController@contact');

Route::name('show')->get('/show/{id}', 'FrontEnd\HomeController@show');
Route::name('category')->get('/category/{id}', 'FrontEnd\HomeController@getBookByCategory');
Route::name('list-books')->get('/list-books', 'FrontEnd\HomeController@listBook');
Route::get('search', 'FrontEnd\HomeController@searchBook');

Route::get('/admin', 'HomeController@index');

Auth::routes();

Route::get('search-book', 'BookController@searchBook');
Route::get('get-book', 'BookController@getBook');

//back-end page
Route::group(['middleware' => 'auth'], function()
{
    Route::get('/home', 'HomeController@index');

    Route::resource('users','UserController', ['middleware' => 'permission:user-manager']);

    Route::resource('permissions', 'PermissionController', ['middleware' => 'permission:permission-manage']);

    Route::resource('roles', 'RoleController',['middleware' => 'permission:role-manager']);

    Route::resource('books', 'BookController', ['middleware' => ['permission:book']]);

    Route::group(['middleware' => 'permission:book'], function() {
        Route::resource('books', 'BookController');
        Route::get('books/create/file', 'BookController@create_file')->name('books.file');
        Route::get('books/download/excel', 'BookController@downloadExcel')->name('books.export');
        Route::post('books/import/excel', 'BookController@importExcel')->name('books.import');

    });

    Route::resource('stores', 'StoreController', ['omly' => ['index'], 'middleware' => ['permission:store-view']]);
    Route::post('books/updateSale', 'BookController@updateSale', ['middleware' => ['permission:update-sale']])->name('books.updateSale');

    Route::group(['middleware' => 'permission:import-book'], function() {
        Route::get('importBooks/create/file', 'ImportBookController@createFile')->name('import_books.file');;
        Route::get('importBooks/download/excel', 'ImportBookController@downloadExcel')->name('import_books.export');;
        Route::post('importBooks/import/excel', 'ImportBookController@importExcel')->name('import_books.import');;
        Route::resource('importBooks', 'ImportBookController');
    });

    Route::resource('bills', 'BillController', ['middleware' => ['permission:bill']]);

    Route::group(['middleware' => 'permission:other-items'], function()
    {
        Route::resource('categories', 'CategoryController');
        Route::resource('types', 'TypeController');
        Route::resource('publishers', 'PublisherController');
        Route::resource('authors', 'AuthorController');
        Route::resource('suppliers', 'SupplierController');
        Route::resource('issuers', 'IssuerController');
        Route::resource('languages', 'LanguageController');
    });

    Route::group(['prefix' => 'statistics', 'middleware' => ['permission:statistic']], function () {
        Route::get('/daily', 'StatisticController@daily')->name('statistics.daily');
        Route::get('/monthly', 'StatisticController@monthly')->name('statistics.monthly');
        Route::get('/quarterly', 'StatisticController@quarterly')->name('statistics.quarterly');
    });

    Route::get('profiles', 'ProfileController@index')->name('profiles.index');
    Route::match(['put', 'patch'], 'profiles/update', 'ProfileController@update')->name('profiles.update');
    Route::get('profiles/edit', 'ProfileController@edit')->name('profiles.edit');
});



