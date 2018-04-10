<?php namespace Datlv\Kit\Extensions;

use League\Fractal\TransformerAbstract;

/**
 * Class ModelTransformer
 *
 * @package Datlv\Kit\Extensions
 */
abstract class ModelTransformer extends TransformerAbstract
{
    protected $zone;

    public function __construct($zone = 'backend')
    {
        $this->zone = $zone;
    }
}
