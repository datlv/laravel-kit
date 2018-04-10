<?php namespace Datlv\Kit\Support;

use Route;

/**
 * Trait HasRouteAttribute
 * Dùng cho các Obj có thuộc tính là route
 * @package Datlv\Kit\Support
 */
trait HasRouteAttribute
{
    /**
     * @param bool $frontend
     *
     * @return array
     */
    public function getRoutes($frontend = true)
    {
        $route_names = array_keys(Route::getRoutes()->getRoutesByName());
        if ($frontend) {
            $route_names = array_filter($route_names, function ($name) {
                return ! str_is('backend*', $name);
            });
        }

        return array_combine($route_names, $route_names);
    }

    /**
     * @param string $name
     * @param array $params
     * @param bool $absolute
     * @return string
     */
    protected function getRouteUrl($name, $params = [], $absolute = true)
    {
        return $name && Route::has($name) ? route($name, $params, $absolute) : "#{$name}";
    }
}
