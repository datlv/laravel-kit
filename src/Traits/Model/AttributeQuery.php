<?php
namespace Datlv\Kit\Traits\Model;

/**
 * Class AttributeQuery
 *
 * @package Datlv\Kit\Traits\Model
 * @property-read string $table
 */
trait AttributeQuery
{
    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $str
     * @param string $attribute
     * @param string $boolean
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeHasStr($query, $str, $attribute = 'content', $boolean = 'and')
    {
        return $query->where("{$this->table}.$attribute", 'LIKE', '%' . $str . '%', $boolean);
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param mixed $value
     * @param string $attribute
     * @param string $boolean
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeExclusion($query, $value, $attribute = 'id', $boolean = 'and')
    {
        return $query->where("{$this->table}.$attribute", '<>', $value, $boolean);
    }
}
