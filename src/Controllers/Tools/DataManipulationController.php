<?php namespace Datlv\Kit\Controllers\Tools;

use Datlv\Kit\Extensions\BackendController;
use DB;

/**
 * Class DataManipulationController
 *
 * @package Datlv\Kit\Controllers\Tools
 */
class DataManipulationController extends BackendController
{
    /**
     * @param string $table
     * @param int $limit
     * @param int $page
     * @param string $search
     */
    public function fetch($table, $limit = 20, $page = 1, $search = '')
    {
        $result = $this->fetchData($table, $limit, $page, $search)->all();
        dd($result);
    }

    /**
     * @param string $table
     * @param int $limit
     * @param int $page
     */
    public function preview($table, $limit = 20, $page = 1)
    {
        $config = $this->getTableConfig($table);
        $data = $this->fetchData($table, $limit, $page);
        $manipulate = $config['manipulate'];
        $result = $data->mapWithKeys(function ($value) use ($manipulate) {
            return [$value => $manipulate($value)];
        })->filter(function ($new, $old) {
            return $new != $old;
        })->all();
        dd($result);
    }

    /**
     * @param string $table
     * @param int $limit
     * @param int $page
     * @return string
     */
    public function manipulate($table, $limit = 20, $page = 1)
    {
        $config = $this->getTableConfig($table);
        $data = $this->getOriginalData($table, $limit, $page);
        $pattern = $config['search'];
        $fetch = $config['fetch'];
        $manipulate = $config['manipulate'];
        $column = $config['column'];
        $data->each(function ($value, $id) use ($table, $pattern, $fetch, $manipulate, $column) {
            preg_match_all($pattern, $value, $matches);
            $contents = (array) $fetch($matches);
            $search = [];
            $replace = [];
            foreach ($contents as $content) {
                $search[] = $content;
                $replace[] = $manipulate($content);
            }
            $value = str_replace($search, $replace, $value);
            DB::table($table)->where('id', $id)->update([$column => $value]);
        });

        return "Done {$data->count()} record...";
    }

    /**
     * @param string $table
     * @param int $limit
     * @param int $page
     * @param string $search
     * @return \Illuminate\Support\Collection
     */
    protected function fetchData($table, $limit = 20, $page = 1, $search = '')
    {
        $config = $this->getTableConfig($table);
        $data = $this->getOriginalData($table, $limit, $page);
        $result = $data->map(function ($value) use ($config) {
            preg_match_all($config['search'], $value, $matches);

            return call_user_func_array($config['fetch'], [$matches]);
        })->flatten();
        if ($search) {
            $result = $result->filter(function ($value) use ($search) {
                return str_is($search, $value);
            });
        }

        return $result;
    }

    /**
     * @param string $table
     * @param int $limit
     * @param int $page
     * @return \Illuminate\Support\Collection
     */
    protected function getOriginalData($table, $limit, $page)
    {
        $config = $this->getTableConfig($table);

        return DB::table($table)->skip(($page - 1) * $limit)->take($limit)->pluck($config['column'], 'id');
    }

    /**
     * @param string $name
     * @return array
     */
    protected function getTableConfig($name)
    {
        $config = config("kit.data_manipulations.{$name}");
        abort_unless($config, 404, 'Table Config Not Found!');

        return $config;
    }
}
