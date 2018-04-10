<?php namespace Datlv\Kit\Controllers\Tools;

use Datlv\Kit\Extensions\BackendController;
use DB;

/**
 * Class MigrateController
 *
 * @package Datlv\Kit\Controllers\Tools
 */
class MigrateController extends BackendController
{
    /**
     * Chuyển Bài viết từ web cũ
     *
     * @param string $from
     * @param string $table
     * @param int $limit
     * @param string $ids
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function migrate($from, $table = null, $limit = 10, $ids = null)
    {
        $config = $this->buildConnection($from, $table);
        $tables = $config['tables'];
        $table = $config['table_curent'];
        $query = DB::connection($config['connection'])->table($table);
        $migrated = $ids ? $this->doMigrate($from, $config, $ids, $limit) : $this->getMigrated($from, $table);
        $models = $query->when($migrated, function ($query) use ($migrated) {
            return $query->whereNotIn('id', $migrated);
        })->take($limit)->get()->map(function ($item) {
            return (array) $item;
        })->all();

        $this->buildHeading([trans('kit::migrate.migrate'), $from], 'fa-truck', [
            route("{$this->route_prefix}backend.dashboard") => trans('backend.dashboard'),
            '#' => trans('kit::migrate.migrate'),
        ]);

        return view('kit::backend.tool.migrate', compact('from', 'table', 'limit', 'tables', 'models', 'migrated'));
    }

    /**
     * @param string $from
     * @param array $config
     * @param string $ids
     * @param int $limit
     * @return array
     */
    protected function doMigrate($from, $config, $ids, $limit = 10)
    {
        $table = $config['table_curent'];
        $query = DB::connection($config['connection'])->table($table);
        $migrated = $this->getMigrated($from, $table);
        if ($ids == 'all') {
            $models = $query->when($migrated, function ($query) use ($migrated) {
                return $query->whereNotIn('id', $migrated);
            })->take($limit)->get();
        } else {
            $ids = array_diff(explode(',', $ids), $migrated);
            $models = $ids ? $query->whereIn('id', $ids)->get() : null;
        }
        $columns = $config['transfer'][$table]['columns'];
        $filters = $config['transfer'][$table]['filters'];
        $records = [];
        if ($models) {
            foreach ($models as $model) {
                $record = [];
                foreach ($columns as $old_col => $new_col) {
                    $record[$new_col] = $model->{$old_col};
                    if (isset($filters[$old_col])) {
                        $record[$new_col] = is_callable($filters[$old_col]) ? call_user_func($filters[$old_col], $record[$new_col]) : $filters[$old_col];
                    }
                }
                $records[] = $record;
                $migrated[] = $model->id;
            }
            DB::table($config['transfer'][$table]['to'])->insert($records);
            DB::table('my_migrations')->where('name', $from)->where('table', $table)->update(['ids' => implode(',', $migrated)]);
        }

        return $migrated;
    }

    /**
     * Các record đã migrate
     *
     * @param $from
     * @param $table
     * @return array
     */
    protected function getMigrated($from, $table)
    {
        $migrated = DB::table('my_migrations')->where('name', $from)->where('table', $table)->first();
        if ($migrated) {
            $ids = $migrated->ids ? explode(',', $migrated->ids) : [];
        } else {
            DB::table('my_migrations')->insert(['name' => $from, 'table' => $table, 'ids' => '']);
            $ids = [];
        }

        return $ids;
    }

    /**
     * @param string $from
     * @param string $table
     * @return array
     */
    protected function buildConnection($from, $table = null)
    {
        $config = config("kit.migrations.{$from}");
        abort_unless($config, 404, 'Migrate Config Not found!');
        $connection_cfg = config("database.connections.{$config['connection']}");
        abort_unless($connection_cfg, 404, 'Migrate Config Invalid!');

        $connection_cfg = $config['custom'] + $connection_cfg;
        $connection_name = "{$config['connection']}_{$from}";
        config(["database.connections.{$connection_name}" => $connection_cfg]);
        DB::reconnect($connection_name);
        $tables = array_keys($config['transfer']);
        $table = $table ?: $tables[0];
        abort_unless(isset($config['transfer'][$table]), 404, 'Migrate Config Invalid!');

        $config['tables'] = $tables;
        $config['table_curent'] = $table;
        $config['connection'] = $connection_name;

        return $config;
    }
}
