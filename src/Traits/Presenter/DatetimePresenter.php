<?php
namespace Datlv\Kit\Traits\Presenter;

use Carbon\Carbon;

/**
 * Class DatetimePresenterTrait
 *
 * @package Datlv\Kit\Traits\Presenter
 * @property-read mixed $entity
 * @mixin \Laracasts\Presenter\Presenter
 */
trait DatetimePresenter
{
    /**
     * @param string $field
     * @param array $options
     *
     * @return string
     */
    public function formattedDatetime($field, $options = [])
    {
        return $this->formatDatetime($this->entity->{$field}, $options);
    }

    /**
     * @param array $options
     *
     * @return string
     */
    public function createdAt($options = [])
    {
        return $this->formatDatetime($this->entity->created_at, $options);
    }

    /**
     * @param array $options
     *
     * @return string
     */
    public function updatedAt($options = [])
    {
        return $this->formatDatetime($this->entity->updated_at, $options);
    }

    /**
     * @param string $format
     *
     * @return string
     */
    public function createdDate($format = 'd/m/Y')
    {
        return $this->formatDate($this->entity->created_at, $format);
    }

    /**
     * @param string $format
     *
     * @return string
     */
    public function updatedDate($format = 'd/m/Y')
    {
        return $this->formatDate($this->entity->updated_at, $format);
    }

    /**
     * @param \DateTime|null $datetime
     * @param string $format
     *
     * @return string
     */
    protected function formatDate($datetime, $format = 'd/m/Y')
    {
        return is_null($datetime) ? null : $datetime->format($format);
    }

    /**
     * @param \Carbon\Carbon $datetime
     * @param array $options
     *
     * @return string
     */
    protected function formatDatetime($datetime, $options = [])
    {
        if (is_null($datetime)) {
            return null;
        }
        /** @var \Carbon\Carbon $datetime */
        $datetime = Carbon::instance($datetime);
        $default = [
            'date'     => 'd/m/Y',
            'time'     => 'H:i',
            'template' => ':day, :date | :time',
        ];
        $options = $options + $default;
        $day_of_week = trans('common.day_of_week');

        return strtr(
            $options['template'],
            [
                ':day'  => $day_of_week[(int)$datetime->format('w')],
                ':date' => $options['date'] == 'diff' ? $datetime->diffForHumans() : $datetime->format($options['date']),
                ':time' => $datetime->format($options['time']),
            ]
        );
    }
}
