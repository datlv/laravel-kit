<?php namespace Datlv\Kit;

use ReflectionClass;
use Illuminate\Database\Eloquent\Relations\Relation;
use Delight\Str\Str;

class Manager
{
    /**
     * @var string
     */
    protected $zone;

    /**
     * Danh sách tên (display name) của model class
     *
     * @var array
     */
    protected $titles = [];

    /**
     * Danh sách 'bí danh' của model class
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * Lưu ngược alias => class
     *
     * @var array
     */
    protected $classes = [];

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $paths;

    public function __construct()
    {
        $this->paths = collect(config('kit.writeable_paths'));
    }

    /**
     * @param string $str
     * @param string $charset
     * @return \Delight\Str\Str
     */
    public function s($str, $charset = 'UTF-8')
    {
        return new Str($str, $charset);
    }

    /**
     * Không có $value => get, ngược lại => set
     *
     * @param string $value
     *
     * @return string
     */
    public function currentZone($value = null)
    {
        return $this->zone = $value ?: $this->zone;
    }

    /**
     * Đăng ký một writeable $path với tên là $title
     *
     * @param string $path
     * @param string $title
     */
    public function writeablePath($path, $title)
    {
        $this->paths->put($path, $title);
    }

    /**
     * Lấy danh sách các writeable paths
     *
     * @return array
     */
    public function writeablePaths()
    {
        return $this->paths->map(function ($title, $path) {
            return ['title' => mb_fn_str($title), 'path' => mb_get_path($path)];
        })->all();
    }

    /**
     * @param string|mixed $model
     * @param string $title
     *
     * @return string
     */
    public function title($model, $title = null)
    {
        return empty($title) ? $this->getTitle($model) : $this->setTitle($model, $title);
    }

    /**
     * @param mixed $alias
     *
     * @return bool
     */
    public function isAlias($alias)
    {
        return is_string($alias) && isset($this->classes[$alias]);
    }

    /**
     * - Đăng ký alias: có $alias
     * - Lấy alias: bỏ qua $alias
     *
     * @param string|mixed $model
     * @param string $alias
     *
     * @return string
     */
    public function alias($model, $alias = null)
    {
        return empty($alias) ? $this->getAlias($model) : $this->setAlias($model, $alias);
    }

    /**
     * @param array $models
     *
     * @return string[]
     */
    public function aliases($models)
    {
        return array_map([$this, 'alias'], $models);
    }

    /**
     * Lấy alias từ baseClass
     *
     * @param string|mixed $model
     *
     * @return string
     */
    public function baseClass($model)
    {
        return strtolower((new ReflectionClass($model))->getShortName());
    }

    /**
     * @param string|mixed $model
     *
     * @return string
     */
    public function getClass($model)
    {
        return is_string($model) ? ($this->isAlias($model) ? $this->classes[$model] : $model) : get_class($model);
    }

    /**
     * @param array|mixed $models
     *
     * @return array
     */
    public function getClasses($models)
    {
        return array_map([$this, 'getClass'], (array) $models);
    }

    /**
     * @param string|mixed $model
     * @param string $alias
     *
     * @return string
     */
    protected function setAlias($model, $alias)
    {
        $class = $this->getClass($model);
        $this->classes[$alias] = $class;
        //Custom Polymorphic Types
        Relation::morphMap([$alias => $class]);

        return $this->aliases[$class] = $alias;
    }

    /**
     * @param string|mixed $model
     *
     * @return string
     */
    protected function getAlias($model)
    {
        if ($this->isAlias($model)) {
            return $model;
        } else {
            $class = $this->getClass($model);
            if (! isset($this->aliases[$class])) {
                $this->aliases[$class] = $this->baseClass($class);
            }

            return $this->aliases[$class];
        }
    }

    /**
     * @param string|mixed $model
     *
     * @return string
     */
    protected function getTitle($model)
    {
        $class = $this->isAlias($model) ? $this->classes[$model] : $this->getClass($model);
        if (! isset($this->titles[$class])) {
            $this->titles[$class] = ucfirst($this->baseClass($class));
        }

        return $this->titles[$class];
    }

    /**
     * @param string|mixed $model
     * @param string $title
     *
     * @return string
     */
    protected function setTitle($model, $title)
    {
        return $this->titles[$this->getClass($model)] = $title;
    }
}
