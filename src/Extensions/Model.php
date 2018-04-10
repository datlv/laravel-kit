<?php

namespace Datlv\Kit\Extensions;

use Eloquent;

/**
 * Class Model
 *
 * @package Datlv\Kit\Extensions
 * @property integer $id
 */
abstract class Model extends Eloquent {
    /**
     * @var array Cấu hình model
     */
    public $config = [];

    /**
     * @param array $config
     */
    public function config( $config ) {
        $this->config = array_merge_recursive( $this->config, (array) $config );
    }

    /**
     * @return array
     */
    public function getAttributeNames() {
        return array_keys( $this->getAttributes() );
    }

    /**
     * Lấy giá trị thô của attribute
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getAttributeRaw( $key ) {
        if ( ! array_key_exists( $key, $this->attributes ) ) {
            abort( 500, 'Attribute not exists!' );
        }

        return $this->attributes[$key];
    }

    /**
     * id=null => trả về giá trị $default
     * $ignoreError = false và không tìm thấy model => error 404
     *
     * @param integer $id
     * @param string $attribute
     * @param mixed $default
     * @param bool $ignoreError
     *
     * @return mixed
     */
    public static function getAttributeById( $id, $attribute, $default = null, $ignoreError = false ) {
        if ( empty( $id ) ) {
            return $default;
        }
        if ( $model = static::find( $id ) ) {
            return $model->$attribute;
        } else {
            if ( $ignoreError ) {
                return $default;
            } else {
                abort( 404 );

                return null;
            }
        }
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public static function hasAttribute( $attribute ) {
        $attributes = ( new static() )->getAttributes();

        return isset( $attributes[$attribute] );
    }

    /**
     * @param string $attribute
     * @param mixed $value
     *
     * @return static
     */
    public static function findBy( $attribute, $value ) {
        return static::where( $attribute, $value )->first();
    }

    /**
     * Ids:
     * - null: Không bao gồm chính nó
     * - int: 1 id
     * - string: nhiều id
     * - array: nhiều id
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param integer|string|array $ids
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeExcept( $query, $ids = null ) {
        if ( is_null( $ids ) ) {
            $ids = $this->id ? [ $this->id ] : null;
        } else if ( is_numeric( $ids ) ) {
            $ids = [ $ids ];
        } else if ( is_string( $ids ) ) {
            $ids = explode( ',', trim( $ids ) );
        }

        return $ids ? $query->whereNotIn( "{$this->table}.id", $ids ) : $query;
    }

    /**
     * @param \Illuminate\Database\Query\Builder|static $query
     * @param array $attributes
     *
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function scopeWhereAttributes( $query, $attributes ) {
        foreach ( $attributes as $column => $value ) {
            $column = strpos( $column, '.' ) === false ? "{$this->table}.{$column}" : $column;
            if ( $value && is_array( $value ) ) {
                $query->whereIn( $column, $value );
            } else {
                $query->where( $column, '=', $value );
            }
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param string|array $columns
     * @param string $text
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeFindText( $query, $columns, $text ) {
        if ( $text ) {
            $columns = (array) $columns;
            foreach ( $columns as $column ) {
                $column = strpos( $column, '.' ) === false ? "{$this->table}.{$column}" : $column;
                $query->where( $column, 'LIKE', "%{$text}%" , 'or');
            }
        }

        return $query;
    }
}
