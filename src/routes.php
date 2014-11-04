<?php

Route::group(
    array(
        'prefix' => 'administration',
        'namespace' => '\Ipsum\Media\Controllers'
    ),
    function() {
        /* Patterns */
        Route::pattern('id', '\d+');

        /* Media */
        Route::get('media', array(
            'as'     => 'admin.media.index',
            'uses'   => 'AdminController@index'
        ));
        /*Route::post('media', array(
            'as'     => 'admin.media.store',
            'uses'   => 'AdminController@store'
        ));
        Route::get('media/{id}/edit', array(
            'as'     => 'admin.media.edit',
            'uses'   => 'AdminController@edit'
        ));
        Route::put('media/{id}', array(
            'as'     => 'admin.media.update',
            'uses'   => 'AdminController@update'
        ));*/
        Route::put('media/upload', array(
            'as'     => 'admin.media.upload',
            'uses'   => 'AdminController@upload'
        ));
        Route::delete('media/{id}/destroy', array(
            'as'     => 'admin.media.destroy',
            'uses'   => 'AdminController@destroy'
        ));
        Route::delete('media/{id}/detach/{publication_id}', array(
            'as'     => 'admin.media.detach',
            'uses'   => 'AdminController@detach'
        ));
    }
);

