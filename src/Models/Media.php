<?php
namespace Ipsum\Media\Models;

use Ipsum\Core\Models\BaseModel;

class Media extends BaseModel {

    protected $table = 'media';

    public static $rules = array(
                                "titre" => "max:255",
                            );

    public function publications()
    {
        return $this->hasMany('Ipsum\Media\Models\MediaPublication');
    }

}