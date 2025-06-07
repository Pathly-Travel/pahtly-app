<?php

namespace Src\Support\Helpers;

class DomainHelper
{
    /**
     * Resolve a domain action from the container
     */
    public static function resolveAction(string $actionClass): mixed
    {
        return app($actionClass);
    }

    /**
     * Execute a domain action with error handling
     */
    public static function executeAction(string $actionClass, mixed ...$parameters): mixed
    {
        $action = self::resolveAction($actionClass);

        return $action(...$parameters);
    }
}
