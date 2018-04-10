<?php

namespace Datlv\Kit\Traits\Model;

use Carbon\Carbon;

/**
 * Dùng cho Model có timestamps
 * Class DatetimeQuery
 *
 * @package Datlv\Kit\Traits\Model
 * @property-read string $table
 */
trait DatetimeQuery
{
    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $direction
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeOrderCreated($query, $direction = 'desc')
    {
        return $query->orderBy("{$this->table}.created_at", $direction);
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $direction
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeOrderUpdated($query, $direction = 'desc')
    {
        return $query->orderBy("{$this->table}.updated_at", $direction);
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param \Carbon\Carbon|string|null $start ngày|tháng theo PHP Date Formats hoặc Carbon
     * @param \Carbon\Carbon|string|null $end ngày|tháng theo PHP Date Formats hoặc Carbon
     * @param string $field
     * @param bool $end_if_day
     * @param bool $is_month tham số là tháng?
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopePeriod(
        $query,
        $start = null,
        $end = null,
        $field = 'created_at',
        $end_if_day = false,
        $is_month = false
    ) {
        if (is_null($start)) {
            $start = (new Carbon())->startOfDay();
        } else {
            if (is_string($start)) {
                $start = new Carbon($start);
            }
        }
        if (is_null($end)) {
            $end = new Carbon();
        } else {
            if (is_string($end)) {
                $end = new Carbon($end);
            }
        }
        $end = $is_month ? $end->endOfMonth() : $end;
        $end = $end_if_day ? $end->endOfDay() : $end;

        return $query->where("{$this->table}.{$field}", '>=', $start)
            ->where("{$this->table}.{$field}", '<=', $end);
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $field
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeToday($query, $field = 'created_at')
    {
        return $this->scopePeriod($query, null, null, $field, true);
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param bool $same_time tính đến thời điểm như hiện tại
     * @param string $field
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeYesterday($query, $same_time = false, $field = 'created_at')
    {
        $start = Carbon::now()->subDay()->startOfDay();
        $end = Carbon::now()->subDay();
        if ($same_time === false) {
            $end = $end->endOfDay();
        }

        return $this->scopePeriod($query, $start, $end, $field);
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $field
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeThisWeek($query, $field = 'created_at')
    {
        return $this->scopePeriod($query, Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek(), $field);
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $field
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeThisMonth($query, $field = 'created_at')
    {
        return $this->scopePeriod($query, Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth(), $field);
    }
}
