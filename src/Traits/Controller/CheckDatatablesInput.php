<?php namespace Datlv\Kit\Traits\Controller;

/**
 * Theo cảnh báo của Acunetix Vulnerability Scanner, kiểm tra trong ajax method data() của DataTables,
 * Trait CheckDatatablesInput
 *
 * @package Datlv\Kit\Traits\Controller
 */
trait CheckDatatablesInput
{
    /**
     * @param \Illuminate\Http\Request $request
     */
    protected function filterDatatablesParametersOrAbort($request)
    {

        $parameters = $request->query->all();
        $this->castDatatablesInput($parameters, '_', 'string');
        $this->castDatatablesInput($parameters, 'draw', 'int');
        $this->castDatatablesInput($parameters, 'start', 'int');
        $this->castDatatablesInput($parameters, 'length', 'int');
        $this->castDatatablesInput($parameters, 'search', 'search');

        if (isset($parameters['order']) && is_array($parameters['order'])) {
            foreach ($parameters['order'] as &$order) {
                $this->castDatatablesInput($order, 'column', 'int');
                $this->castDatatablesInput($order, 'dir', 'dir');
            }
        }

        if (isset($parameters['columns']) && is_array($parameters['columns'])) {
            foreach ($parameters['columns'] as &$column) {
                abort_unless(
                    is_array($column) &&
                    $this->isValidDatatablesColumnName($column, 'name') &&
                    $this->isValidDatatablesColumnName($column, 'data'),
                    400
                );
                $this->castDatatablesInput($column, 'searchable', 'bool');
                $this->castDatatablesInput($column, 'orderable', 'bool');
                $this->castDatatablesInput($column, 'search', 'search');
            }
        }
        if (isset($parameters['filter_form'])) {
            foreach ($parameters as $key => $value) {
                if (str_is('filter_*', $key)) {
                    $this->castDatatablesInput($parameters, $key, 'string');
                }
            }
        }

        $request->query->replace($parameters);
    }

    /**
     * Empty hoặc chỉ cho phép: ký tự, số, dấu chấm . và dấu gạch dưới _
     *
     * @param array $input
     * @param string $name
     * @return bool
     */
    protected function isValidDatatablesColumnName($input, $name)
    {
        return empty($input[$name]) || (is_string($input[$name]) && preg_match("/^[a-zA-Z0-9_\.]+$/", $input[$name]));
    }

    /**
     * @param array $input
     * @param string $name
     * @param string $type
     */
    protected function castDatatablesInput(&$input, $name, $type)
    {
        if (is_array($input) && ! empty($input[$name])) {
            switch ($type) {
                case 'int':
                    $input[$name] = (int) $input[$name];
                    break;
                case 'bool':
                    $input[$name] = in_array($input[$name], ['false', 'true']) ? $input[$name] : 'false';
                    break;
                case 'string':
                    if (is_array($input[$name])) {
                        $input[$name] = current($input[$name]);
                    }
                    $input[$name] = filter_var((string) $input[$name], FILTER_SANITIZE_STRING);
                    break;
                case 'dir':
                    $input[$name] = in_array($input[$name], ['asc', 'desc']) ? $input[$name] : 'desc';
                    break;
                case 'search':
                    $this->castDatatablesInput($input[$name], 'value', 'string');
                    $this->castDatatablesInput($input[$name], 'regex', 'bool');
                    break;
            }
        }
    }
}
