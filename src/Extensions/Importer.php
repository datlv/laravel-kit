<?php namespace Datlv\Kit\Extensions;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

/**
 * Class Importer
 * @package Datlv\Kit\Extensions
 * @author Minh Bang
 */
abstract class Importer
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $data;
    /**
     * @var string
     */
    protected $filename;
    /**
     * @var array
     */
    protected $defaultValues;
    /**
     * @var array
     */
    protected $rules;
    /**
     * @var int
     */
    protected $count = 0;

    /**
     * @return array
     */
    abstract protected function rules();

    /**
     * @return string
     */
    abstract protected function resource();

    /**
     * @param array $attributes
     * @return \Datlv\Kit\Extensions\Model|false
     */
    abstract protected function saveRecord($attributes);

    /**
     * @return int
     */
    abstract protected function columnCount();

    public function __construct()
    {
        $this->data = new Collection();
        $this->defaultValues = $this->defaultValues();
        $this->rules = $this->rules();
    }

    /**
     * @return string
     */
    public function title()
    {
        return trans("{$this->resource()}.{$this->resource()}");
    }

    public function rules_hint()
    {
        return trans("{$this->resource()}.import_rules");
    }

    public function columnNames()
    {
        return array_keys($this->data->first(null, []));
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function get()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->data->all();
    }

    /**
     * @return string
     */
    public function filename()
    {
        return $this->filename;
    }

    public function filepath()
    {
        return data_path("tmp/{$this->filename}");
    }

    /**
     * @return bool
     */
    public function deleteTmpFile()
    {
        $filepath = $this->filepath();
        return $this->filename && is_file($filepath) ? @unlink($filepath) : false;
    }

    /**
     * @return int|false
     */
    public function import()
    {
        if ($this->data->isEmpty()) {
            return false;
        }
        $this->count = 0;
        $this->data->each(function ($row) {
            $row = $this->transform($row) + $this->defaultValues;
            $model = $this->saveRecord($row);
            $this->count += $model && $this->afterSaveRecord($model, $row) ? 1 : 0;
        });
        return $this->count ?: false;
    }

    /**
     * @param string|\Illuminate\Http\UploadedFile $file
     * @return \Datlv\Kit\Extensions\Importer
     */
    public function load($file)
    {
        if ($file instanceof UploadedFile) {
            $file = substr($file->store('tmp', 'data'), 4); // 4 = length('tmp/')
        }
        $this->filename = $file;
        $filepath = $this->filepath();
        abort_unless($file && is_file($filepath), 404, 'Data File not found!');
        try {
            $this->data = collect(app('excel')->selectSheetsByIndex(0)->load($filepath)
                ->takeColumns($this->columnCount())->ignoreEmpty()->get()->filter(function ($item) {
                    return $this->validate($item->toArray());
                })
                ->toArray());
        } catch (Exception $e) {
            unset($e);
            abort(400, 'Invalid Data File');
        }
        return $this;
    }

    /**
     * Mặc định ẩn các column khi preview
     * @return array
     */
    public function hidden()
    {
        return [];
    }

    /**
     * @param \Datlv\Kit\Extensions\Model $model
     * @param array $attributes
     * @return bool
     */
    protected function afterSaveRecord($model, $attributes)
    {
        return true;
    }

    /**
     * Đổi tên các columns
     * @return array
     */
    protected function mapping()
    {
        return [];
    }

    /**
     * Hàm transform 1 column của dữ liệu: transformColumnName
     * @param array $row
     * @return array
     */
    protected function transform($row)
    {
        $mapping = $this->mapping();
        return collect($row)->mapWithKeys(function ($value, $key) use ($mapping) {
            $method = 'transform' . ucfirst(camel_case($key));
            $value = method_exists($this, $method) ? call_user_func([$this, $method], $value) : $value;
            return [array_get($mapping, $key, $key) => $value];
        })->all();
    }

    /**
     * Các attributes mặc định
     * @return array
     */
    protected function defaultValues()
    {
        return [];
    }

    /**
     * @param array $row
     * @return bool
     */
    protected function validate($row)
    {
        return collect($row)->filter()->count() && app('validator')->make($row, $this->rules)->passes();
    }
}
