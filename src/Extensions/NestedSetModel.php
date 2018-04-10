<?php
namespace Datlv\Kit\Extensions;

use Baum\Node;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class NestedSetModel
 *
 * @property int $id
 * @property integer $parent_id
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property-read \Datlv\Kit\Extensions\NestedSetModel[] $children
 * @package Datlv\Kit\Extensions
 */
class NestedSetModel extends Node
{
    /**
     * Lấy nodes path từ root => $this
     *
     * @param array $columns
     * @param bool $self
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRootPath($columns = ['*'], $self = true)
    {
        return $self ? $this->getAncestorsAndSelf($columns) : $this->getAncestors($columns);
    }

    /**
     * Lấy nodes path từ root => $this
     * Không lấy root
     *
     * @param array $columns
     * @param bool $self
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRoot1Path($columns = ['*'], $self = true)
    {
        return $self ? $this->getAncestorsAndSelfWithoutRoot($columns) : $this->getAncestorsWithoutRoot($columns);
    }

    /**
     * Lấy node cha có depth = 1
     * Root thật sự (depth = 0, parent_id = null) dùng để phân loại (vd: category, user_group...)
     * Code tương tự getRoot(), khác điều kiện
     *
     * @return static
     */
    public function getRoot1()
    {
        if ($this->exists) {
            return $this->ancestorsAndSelf()->where($this->getDepthColumnName(), '=', 1)->first();
        } else {
            return $this;
        }
    }

    /**
     * Mở rộng tính năng hasMany() cho nested set
     * - Cho phép lấy các model 'liên quan' trực tiếp như bình thường
     * - Cộng với các model 'liên quan' đến các node con của nó
     *
     * @param string $related
     * @param null $foreignKey
     * @param null $localKey
     *
     * @return \Datlv\Kit\Extensions\HasManyNestedSet
     */
    public function hasManyNestedSet($related, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $instance   = new $related;
        $localKey   = $localKey ?: $this->getKeyName();

        return new HasManyNestedSet($instance->newQuery(), $this, $instance->getTable() . '.' . $foreignKey, $localKey);
    }

    /**
     * @param  string $related
     * @param  string $table
     * @param  string $foreignKey
     * @param  string $otherKey
     * @param  string $relation
     * @param  Builder $query
     * @param bool $immediate
     *
     * @return \Datlv\Kit\Extensions\BelongsToManyNestedSet
     */
    public function belongsToManyNestedSet(
        $related,
        $table = null,
        $foreignKey = null,
        $otherKey = null,
        $relation = null,
        $query = null,
        $immediate = false
    ) {
        if (is_null($relation)) {
            $relation = $this->getBelongsToManyCaller();
        }

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = new $related;

        $otherKey = $otherKey ?: $instance->getForeignKey();

        if (is_null($table)) {
            $table = $this->joiningTable($related);
        }

        $query = $query ?: $instance->newQuery();

        return $immediate ?
            new BelongsToMany($query, $this, $table, $foreignKey, $otherKey, $relation) :
            new BelongsToManyNestedSet($query, $this, $table, $foreignKey, $otherKey, $relation);
    }

    /**
     * @param string $attribute
     * @param mixed $value
     *
     * @return static
     */
    public static function findBy($attribute, $value)
    {
        return static::where($attribute, $value)->first();
    }
}
