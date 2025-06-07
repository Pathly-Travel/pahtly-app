<?php

namespace App\Foundation;

use Illuminate\Foundation\Application as BaseApplication;

class Application extends BaseApplication
{
    protected $namespace = 'App\\';

    /**
     * Create a new Illuminate application instance.
     *
     * @param  string|null  $basePath
     * @return void
     */
    public function __construct($basePath = null)
    {
        parent::__construct($basePath);

        // Set the app path for the custom directory structure
        $this->useAppPath('src/App');
    }

    /**
     * Get the namespace for the application.
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }
}
