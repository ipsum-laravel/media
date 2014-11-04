<?php
namespace Ipsum\Media\Models;

use Ipsum\Core\Models\BaseModel;

/*
 * Ce modéle permet de lié un media à une publication (article, produit...)
 * On aurait pu faire une relations polymorphique plusieurs vers plusieurs
 * mais il y aurait fallut mettre des dépendances au autre modéle dans me modéle Media
 */

class MediaPublication extends BaseModel {

    protected $table = 'media_publication';

    public $timestamps = false;

    public function media()
    {
         return $this->belongsTo('Ipsum\Media\Models\Media');
    }

    public function publication()
    {
        return $this->morphTo();
    }

}