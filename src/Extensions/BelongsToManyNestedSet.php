<?php namespace Datlv\Kit\Extensions;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class BelongsToManyNestedSet
 *
 * @package Datlv\Kit\Extensions
 */
class BelongsToManyNestedSet extends BelongsToMany
{
    /**
     * - Cho phép lấy các model attached trực tiếp như bình thường
     * - Cộng với các model attched vowis các node con cháu của nó
     *
     * @return void
     */
    public function addConstraints()
    {
        $this->setJoin();

        if (static::$constraints) {
            $this->setWhereIn();
        }
    }

    /**
     * Set the where In clause for the relation query.
     *
     * @return $this
     */
    protected function setWhereIn()
    {
        $foreign    = $this->getForeignKey();
        $parentKeys = $this->parent->descendantsAndSelf()->pluck($this->parent->getKeyName())->all();
        $this->query->whereIn($foreign, $parentKeys);

        return $this;
    }
}
