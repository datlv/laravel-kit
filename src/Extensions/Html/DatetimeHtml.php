<?php
namespace Datlv\Kit\Extensions\Html;
/**
 * Class DatetimeHtml
 *
 * @package Datlv\Kit\Extensions\Presenter
 */
trait DatetimeHtml
{
    /**
     * @param mixed $model
     * @param array $options
     *
     * @return string
     */
    public function updatedAt($model, $options = [])
    {
        return $this->formatDatetime($model->updated_at, $options);
    }

    /**
     * @param mixed $model
     * @param array $options
     *
     * @return string
     */
    public function createdAt($model, $options = [])
    {
        return $this->formatDatetime($model->created_at, $options);
    }

    /**
     * @param mixed $model
     * @param array $options
     *
     * @return string
     */
    public function publishedAt($model, $options = [])
    {
        return $this->formatDatetime($model->published_at, $options);
    }

    /**
     * @param \DateTime|null $datetime
     * @param string $format
     *
     * @return string
     */
    public function formatDate($datetime, $format = 'd/m/Y')
    {
        return is_null($datetime) ? null : $datetime->format($format);
    }

    /**
     * @param \DateTime $datetime
     * @param array $options
     *
     * @return string
     */
    public function formatDatetime($datetime, $options = [])
    {
        if (is_null($datetime)) {
            return null;
        }

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
                ':date' => $datetime->format($options['date']),
                ':time' => $datetime->format($options['time']),
            ]
        );
    }
}
