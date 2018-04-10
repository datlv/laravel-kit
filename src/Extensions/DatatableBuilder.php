<?php namespace Datlv\Kit\Extensions;

use Collective\Html\FormBuilder;
use Collective\Html\HtmlBuilder;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\UrlGenerator;
use Yajra\DataTables\Html\Builder;

class DatatableBuilder extends Builder
{
    /**
     * Script template
     *
     * @var string
     */
    protected $template = 'kit::_datatables_script';
    protected $tableAttributes = ['class' => 'table table-striped table-bordered', 'id' => 'dataTableBuilder'];

    public function __construct(
        Repository $config,
        Factory $view,
        HtmlBuilder $html,
        UrlGenerator $url,
        FormBuilder $form
    ) {
        $this->attributes = [
            'dom'      => "<'dataTables_tools'rfl>t<'row'<'col-md-6'i><'col-md-6'p>>",
            'language' => trans('datatables.language'),
        ];
        parent::__construct($config, $view, $html, $url, $form);
    }

    /**
     * Cho phép định nghĩa column class name
     * - ví dụ: ['data' => 'id', 'name' => 'id', 'title' => 'Id', 'class' => 'min-width'],
     *
     * @param array $columns
     *
     * @return \Datlv\Kit\Extensions\DatatableBuilder|\Yajra\DataTables\Html\Builder
     */
    public function columns(array $columns)
    {
        $classes = [];
        foreach ($columns as $key => &$value) {
            if (is_array($value) && isset($value['class'])) {
                if (isset($classes[$value['class']])) {
                    $classes[$value['class']][] = $key;
                } else {
                    $classes[$value['class']] = [$key];
                }
                unset($value['class']);
            }
        }
        if ($classes) {
            $this->attributes['columnDefs'] = array_map(function ($key, $value) {
                return ['className' => $key, 'targets' => $value];
            }, array_keys($classes), $classes);
        }

        return parent::columns($columns);
    }

    /**
     * @param string $class
     */
    public function addTableClass($class)
    {
        $this->tableAttributes['class'] .= " $class";
    }
}
