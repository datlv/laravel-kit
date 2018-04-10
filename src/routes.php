<?php
// Quản trị
// ---------------------------------------------------------------------------------------------------------------------
Route::group([
    'prefix' => 'backend',
    'as' => 'backend.',
    'namespace' => 'Datlv\Kit\Controllers',
    'middleware' => config('kit.middleware.backend'),
], function () {
    Route::get('/', 'DashboardController@index')->name('dashboard');
});

Route::group([
    'prefix' => 'backend/tools',
    'as' => 'backend.tools.',
    'namespace' => 'Datlv\Kit\Controllers\Tools',
    'middleware' => config('kit.middleware.backend_tools'),
], function () {
    // Import
    Route::group(['prefix' => 'import', 'as' => 'import.'], function () {
        Route::get('{resource}/step1', ['as' => 'step1', 'uses' => 'ImportController@step1']);
        Route::post('{resource}/step2', ['as' => 'step2', 'uses' => 'ImportController@step2']);
        Route::post('{resource}/step3', ['as' => 'step3', 'uses' => 'ImportController@step3']);
    });
    // System
    Route::get('phpinfo', ['as' => 'phpinfo', 'uses' => 'SystemController@getPhpinfo']);
    Route::get('pretty_routes', ['as' => 'pretty_routes', 'uses' => 'SystemController@getPrettyRoutes']);
    Route::get('writeable', ['as' => 'writeable', 'uses' => 'SystemController@checkWriteable']);
    Route::post('writeable', 'SystemController@fixWriteable');
    Route::get('system_info', ['as' => 'system_info', 'uses' => 'SystemController@system_info']);
    // Migrate
    Route::get('migrate/{from}/{table?}/{limit?}/{ids?}', ['as' => 'migrate', 'uses' => 'MigrateController@migrate']);

    // Data Manipulation
    Route::get('fetch/{table}/{limit?}/{page?}/{search?}', [
        'as' => 'fetch',
        'uses' => 'DataManipulationController@fetch',
    ]);

    Route::get('preview/{table}/{limit?}/{page?}', [
        'as' => 'preview',
        'uses' => 'DataManipulationController@preview',
    ]);
    Route::get('manipulate/{table}/{limit?}/{page?}', [
        'as' => 'manipulate',
        'uses' => 'DataManipulationController@manipulate',
    ]);
});
