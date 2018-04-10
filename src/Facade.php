<?php namespace Datlv\Kit;

use Illuminate\Support\Facades\Facade as BaseFacade;

/**
 * Class Facade
 *
 * @package Datlv\Kit
 */
class Facade extends BaseFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'kit';
    }
}
