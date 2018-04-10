<?php
namespace Datlv\Kit\Extensions;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class HasManyNestedSet
 *
 * @property \Baum\Node $parent
 * @package Datlv\Kit\Extensions
 */
class HasManyNestedSet extends HasMany
{
    /**
     * - Cho phép lấy các model 'liên quan' trực tiếp như bình thường
     * - Cộng với các model 'liên quan' đến các node con của nó
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            $this->query->whereNotNull($this->foreignKey);
            // ... của tất cả các con cháu và chính nó
            $parentKeys = $this->parent->descendantsAndSelf()->pluck($this->localKey)->all();
            $this->query->whereIn($this->foreignKey, $parentKeys);
        }
    }

}
