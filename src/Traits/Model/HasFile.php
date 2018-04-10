<?php
namespace Datlv\Kit\Traits\Model;
/**
 * Class HasFile
 * Áp dụng cho Model có attribute file, vd: Document, Ebook,...
 *
 * @property bool $exists
 * @method static deleting($callback, $priority = 0)
 * @package Datlv\Kit\Traits\Model
 */
trait HasFile
{
    /**
     * Cache cấu hình
     *
     * @var array
     */
    protected $file_config;

    /**
     * Cấu hình fields: ['name' => '', 'mime' => '','size' => '', 'dir' => '']
     *
     * @return string
     */
    abstract protected function fileConfig();

    /**
     * @param string|null $key
     *
     * @return string|null
     */
    protected function fileGetConfig($key = null)
    {
        if (is_null($this->file_config)) {
            $this->file_config = $this->fileConfig();
            if (empty($this->file_config['name']) || empty($this->file_config['mime']) ||
                empty($this->file_config['size']) || empty($this->file_config['dir'])
            ) {
                abort(500, 'HasFile: Invalid File config!');
            }
        }

        return array_get($this->file_config, $key);
    }

    /**
     * @param \Datlv\Kit\Extensions\Request|mixed $request
     */
    public function fileFill($request)
    {
        $cfg = $this->fileGetConfig();
        if ($file = $request->file($cfg['name'])) {
            $filename = xuuid() . '.' . strtolower($file->getClientOriginalExtension());
            $file = $file->move($cfg['dir'], $filename);
            $this->fileDelete();
            $this->{$cfg['name']} = $filename;
            $this->{$cfg['mime']} = $file->getMimeType();
            $this->{$cfg['size']} = $file->getSize();
        }
    }

    /**
     * Xóa file
     */
    public function fileDelete()
    {
        if ($path = $this->filePath()) {
            @unlink($path);
        }
    }

    /**
     * Full path của file
     *
     * @return null|string
     */
    public function filePath()
    {
        $attr = $this->fileGetConfig('name');

        return $this->exists ? $this->fileGetConfig('dir') . '/' . $this->{$attr} : null;
    }

    /**
     * Lấy tên file
     * @return string
     */
    public function fileName()
    {
        $name = $this->fileGetConfig('name');

        return $this->{$name};
    }

    /**
     * @return null|string
     */
    public function fileExtension()
    {
        if ($this->exists) {
            $attr = $this->fileGetConfig('name');
            $filename = $this->{$attr};

            return substr($filename, strrpos($filename, '.') + 1);
        } else {
            return null;
        }
    }

    /**
     * @return int
     */
    public function fileSize()
    {
        $size = $this->fileGetConfig('size');

        return $this->{$size};
    }

    /**
     * Khởi tạo trait
     */
    public static function bootHasFile()
    {
        // Trước khi xóa model, sẽ xóa file
        static::deleting(function ($model) {
            /** @var static $model */
            $model->fileDelete();
        });
    }
}
