<?php


Route::group(['namespace' => 'LWS\ExportActions\Http\Controllers'], function () {
    Route::post('export', 'Exporter@export');
    Route::get('download','DownloadsController@csv')->name('csv');
});

