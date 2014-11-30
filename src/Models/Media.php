<?php
namespace Ipsum\Media\Models;

use Ipsum\Core\Models\BaseModel;
use Config;

class Media extends BaseModel {

    protected $table = 'media';

    public static $rules = array(
                                "titre" => "max:255",
                            );

    public function publications()
    {
        return $this->hasMany('Ipsum\Media\Models\MediaPublication');
    }

    public function getPathAttribute()
    {
        return Config::get('IpsumMedia::path').($this->repertoire != '' ? $this->repertoire.'/'.$this->fichier : $this->fichier);
    }

    public function getIconeAttribute()
    {
        return Config::has('IpsumMedia::types.'.$this->type.'.icone') ? Config::get('IpsumMedia::types.'.$this->type.'.icone') : 'default.png';
    }

    public function isImage()
    {
        return $this->type == 'image';
    }

}