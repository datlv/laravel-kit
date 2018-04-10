<?php

namespace Datlv\Kit\Traits\Model;

use DB;

/**
 * Class PositionTrait
 *
 * @property integer $id
 * @property integer $position
 * @property string $table
 * @property mixed $positionWhere điều kiện thêm để xét position
 * @method static \Illuminate\Database\Query\Builder orderPosition( $direction = 'asc' )
 * @method static \Illuminate\Database\Query\Builder queryDefault()
 * @method static mixed max( $attribute )
 * @method static bool save()
 *
 * @package Datlv\Kit\Traits\Model
 */
trait PositionTrait {
    public function fillNextPosition() {
        $this->position = $this->getPositionQuery()->max( 'position' ) + 1;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $direction
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeOrderPosition( $query, $direction = 'asc' ) {
        return $query->orderBy( "{$this->table}.position", $direction );
    }

    /**
     * Cập nhật tât cả position => 1,2,3...
     */
    public function repairPosition() {
        $ids = $this->getPositionQuery()->pluck( 'id' );
        foreach ( $ids as $i => $id ) {
            DB::table( $this->table )->where( 'id', $id )->update( [ 'position' => $i + 1 ] );
        }
    }

    /**
     * Thay đổi position để $this nằm SAU $model (position lớn hơn)
     *
     * @param mixed $model
     * @param bool $repair
     */
    public function moveAfter( $model, $repair = false ) {
        $this->moveTo( $model, false, $repair );
    }

    /**
     * Thay đổi position để $this nằm TRƯỚC $model (position nhỏ hơn)
     *
     * @param mixed $model
     * @param bool $repair
     */
    public function moveBefore( $model, $repair = false ) {
        $this->moveTo( $model, true, $repair );
    }

    /**
     * @param static $target
     * @param bool $before
     * @param bool $repair
     */
    protected function moveTo( $target, $before, $repair ) {
        $query = $this->getPositionQuery()->where( 'position', '>', $target->position );
        if ( $before ) {
            $query->orWhere( 'id', $target->id );
        }
        $step = $before ? 1 : 2;
        $ids = $query->pluck( 'id' );
        foreach ( $ids as $id ) {
            DB::table( $this->table )->where( 'id', $id )->increment( 'position', $step );
        }
        $this->position = $target->position + ( $before ? 0 : 1 );
        $this->save();
        if ( $repair ) {
            $this->repairPosition();
        }
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getPositionQuery() {
        $query = static::orderPosition();
        // Xét thêm điều kiện khi xét position
        if ( $this->positionWhere ) {
            if ( is_string( $this->positionWhere ) ) {
                $this->positionWhere = [ $this->positionWhere ];
            }
            foreach ( $this->positionWhere as $attr ) {
                $query->where( "{$this->table}.{$attr}", $this->$attr );
            }
        }

        return $query;
    }
}
