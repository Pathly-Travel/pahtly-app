<?php

namespace App\Foundation;

use Illuminate\Foundation\Application as BaseApplication;

class  Application extends BaseApplication
{
    /**
     * Get the namespace for the application.
     *
     * @return string
     */
    public function getNamespace()
    {
        return 'App\\';
    }
}
