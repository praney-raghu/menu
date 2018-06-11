<?php

Route::group(['middleware' => 'web', 'prefix' => 'dynamicmenu', 'namespace' => 'Modules\DynamicMenu\Http\Controllers'], function()
{
    Route::get('/', 'DynamicMenuController@index')->name('menu.home');
    
    // Ajax routes for new menu structure
    Route::post('/addcustommenu', array('as' => 'haddcustommenu', 'uses' => 'DynamicMenuController@addcustommenu'));
    Route::post('/deleteitemmenu', array('as' => 'hdeleteitemmenu', 'uses' => 'DynamicMenuController@deleteitemmenu'));
    Route::post('/deletemenug', array('as' => 'hdeletemenug', 'uses' => 'DynamicMenuController@deletemenug'));
    Route::post('/createnewmenu', array('as' => 'hcreatenewmenu', 'uses' => 'DynamicMenuController@createnewmenu'));
    Route::post('/generatemenucontrol', array('as' => 'hgeneratemenucontrol', 'uses' => 'DynamicMenuController@generatemenucontrol'));
    Route::post('/updateitem', array('as' => 'hupdateitem', 'uses' => 'DynamicMenuController@updateitem'));
});
