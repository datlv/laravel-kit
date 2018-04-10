<?php
namespace Datlv\Kit\Extensions;

use Illuminate\Support\Collection;
use Datatable as Builder;

/**
 * Class Datatable
 *
 * @package Datlv\Kit\Extensions
 */
abstract class Datatable
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $columns;
    /**
     * Current zone
     *
     * @var string
     */
    protected $zone;
    /**
     * Resource name
     *
     * @var string
     */
    protected $name;
    /**
     * @var array
     */
    protected $settings;
    /**
     * @var mixed
     */
    protected $html;

    /**
     * Định nghĩa các columns
     * CHÚ Ý:
     * - Khai báo đủ các thuộc tính
     * - Column đầu (#), nếu sử dụng tính năng reorder, data phải trả về $model->id
     *
     * [
     *     'name' => [
     *          'title' => string,
     *          'data' => string|function
     *      ],
     * ]
     *
     * @return array
     */
    abstract public function columns();


    /**
     * Cấu hình datatables cho các zones, vd: backend, my,...
     * PHẢI KHAI BÁO ĐỦ CÁC THUỘC TÍNH
     * [
     *     'name' => [
     *          'table' => [],
     *          'options' => [],
     *          'columns' => ['name1', 'name2',...]
     *          'search' => [] | string
     *      ],
     * ]
     *
     * @return array
     */
    abstract public function zones();


    /**
     * Datatable constructor.
     *
     * @param string $zone
     * @param string $name
     * @param mixed $html
     */
    public function __construct($zone, $name, $html = null)
    {
        $this->columns = new Collection($this->columns());
        $this->settings = array_get($this->zones(), $zone);
        $this->zone = $zone;
        $this->name = $name;
        $this->html = $html;
    }

    /**
     * KHÔNG KIỂM TRA HAS ZONE!
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function make($query)
    {
        $datatable = Builder::query($query);
        list($columns,) = $this->parserSettings($this->getSetting('columns'));
        $columns = $this->columns->only($columns)->all();
        foreach ($columns as $column => $setting) {
            call_user_func_array([$datatable, 'addColumn'], [$column, $setting['data']]);
        }
        if ($order = $this->getSetting('order')) {
            $order = explode(':', $order, 2);
            $direction = empty($order[1]) ? 'desc' : $order[1];
            $order = $order[0];
            $query->orderBy($order, $direction);
        }

        return $datatable->searchColumns($this->getSetting('search'))->make();
    }

    /**
     * KHÔNG KIỂM TRA HAS ZONE!
     *
     * @throws \Exception
     */
    public function share()
    {
        $tableOptions = $this->getSetting('table');
        list($columns, $options) = $this->parserSettings($this->getSetting('columns'));
        $table = Builder::table()->setCustomValues($tableOptions)->setOptions($options);
        call_user_func_array([$table, 'addColumn'], $this->getColumnTitles($columns));
        view()->share(compact('tableOptions', 'options', 'table'));
    }

    /**
     * @param array $columnNames
     *
     * @return array
     */
    protected function getColumnTitles($columnNames)
    {
        return $this->columns->only($columnNames)->pluck('title')->all();
    }

    /**
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    protected function getSetting($name, $default = null)
    {
        return array_get($this->settings, $name, $default);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    protected function parserSettings($params)
    {
        $columns = [];
        $options = ['aoColumnDefs' => []];
        $i = 0;
        foreach ($params as $key => $value) {
            if (is_numeric($key)) {
                $columns[] = $value;
            } else {
                $columns[] = $key;
                $options['aoColumnDefs'][] = ['sClass' => $value, 'aTargets' => [$i]];
            }
            $i++;
        }

        return [$columns, $options];
    }
}
