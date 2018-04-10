<?php
namespace Datlv\Kit\Traits\Model;

use Request;

/**
 * Class SearchQuery
 * @property-read string $table
 * @property-read array $searchable
 * @package Datlv\Kit\Traits\Model
 */
trait SearchQuery
{
    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $keyword
     * @param null|array $columns
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeSearchKeyword($query, $keyword, $columns = null)
    {
        $columns = $columns ?: $this->searchable;
        if ($keyword && $columns) {
            $query->where(function ($query) use ($keyword, $columns) {
                /** @var \Illuminate\Database\Query\Builder $query */
                $query->where($this->prefixTable(array_shift($columns)), "LIKE", "%$keyword%");
                foreach ($columns as $column) {
                    $query->orWhere($this->prefixTable($column), "LIKE", "%$keyword%");
                }
            });
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $column
     * @param string $operator
     * @param string|null $fn
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeSearchWhere($query, $column, $operator = '=', $fn = null)
    {
        $value = $this->getSearchInput($column);
        if ($value === '') {
            return $query;
        } else {
            return $query->where($column, $operator, mb_fn_fire($fn, $value));
        }
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $column
     * @param string $fn
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeSearchWhereIn($query, $column, $fn)
    {
        $value = $this->getSearchInput($column);
        if ($value === '') {
            return $query;
        } else {
            $values = mb_fn_fire($fn, $value);

            return count($values) ? $query->whereIn($column, $values) : $query;
        }
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $column
     * @param string|null $fn
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeSearchWhereBetween($query, $column, $fn = null)
    {
        $from = $this->getSearchInput($column, 'start');
        $to = $this->getSearchInput($column, 'end');
        if ($from === '' && $to === '') {
            return $query;
        } else {
            if ($to === '') {
                return $query->where($column, '>=', mb_fn_fire($fn, $from));
            } else {
                if ($from === '') {
                    return $query->where($column, '<=', mb_fn_fire($fn, $to));
                } else {
                    return $query->whereBetween($column, [mb_fn_fire($fn, $from), mb_fn_fire($fn, $to)]);
                }
            }
        }
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $column
     * @param string $column_dependent
     * @param string $fn
     * @param array $empty
     *
     * @return \Illuminate\Database\Query\Builder|mixed
     */
    public function scopeSearchWhereInDependent($query, $column, $column_dependent, $fn, $empty = [-1])
    {
        $dependent = $this->getSearchInput($column_dependent);
        if ($dependent === '') {
            return $query;
        } else {
            $list = mb_fn_list($fn, $dependent, $empty);

            return $query->whereIn($column, $list);
        }
    }

    /**
     * @param $column
     * @param string|null $suffix
     * @param string $default
     *
     * @return mixed
     */
    protected function getSearchInput($column, $suffix = null, $default = '')
    {
        if (strpos($column, '.') !== false) {
            $column = last(explode('.', $column));
        }
        $input_name = "filter_{$column}" . ($suffix === null ? '' : '_' . $suffix);

        return Request::get($input_name, $default);
    }

    /**
     * @param string $column
     *
     * @return string
     */
    protected function prefixTable($column)
    {
        return strpos($column, '.') === false ? "{$this->table}.{$column}" : $column;
    }
}
