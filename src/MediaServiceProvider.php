<?php
namespace Ipsum\Media;

use Illuminate\Support\ServiceProvider;

class MediaServiceProvider extends ServiceProvider {

    public function boot()
    {
        $this->package('ipsum/media', 'IpsumMedia', __DIR__);

        include __DIR__.'/routes.php';
    }

    public function register()
    {

    }
}
