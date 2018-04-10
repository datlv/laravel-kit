<?php
namespace Datlv\Kit\Traits\Presenter;

use Html;

/**
 * Class FilePresenter
 *
 * @property \Datlv\Kit\Traits\Model\HasFile|mixed $entity
 * @package Datlv\Kit\Traits\Presenter
 */
trait FilePresenter
{
    public static $file_icons = [
        'pdf'     => 'fa fa-file-pdf-o text-danger',
        'doc'     => 'fa fa-file-word-o text-primary',
        'docx'    => 'fa fa-file-word-o text-primary',
        'ppt'     => 'fa fa-file-powerpoint-o text-warning',
        'pptx'    => 'fa fa-file-powerpoint-o text-warning',
        'xls'     => 'fa fa-file-excel-o text-success',
        'xlsx'    => 'fa fa-file-excel-o text-success',
        'rtf'     => 'fa fa-file-text-o',
        'default' => 'fa fa-file-o',
    ];

    /**
     * Icon theo file ext
     *
     * @return string
     */
    public function fileicon()
    {
        $ext = $this->entity->fileExtension();
        if ( ! isset(static::$file_icons[$ext])) {
            $ext = 'default';
        }

        return '<i class="' . static::$file_icons[$ext] . '"></i>';
    }

    /**
     * @return string
     */
    public function filesize()
    {
        return mb_format_bytes($this->entity->fileSize(), 1);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function fileinfo($name = 'name')
    {
        if ($name) {
            $filename = ($name == 'name' ? $this->entity->fileName() : $this->entity->{$name}) . ' — ';
        } else {
            $filename = '';
        }

        return $this->fileicon() . ' ' . $filename . $this->filesize();
    }

    /**
     * @param string $route
     * @param string $name
     * @param array $options
     *
     * @return string
     */
    public function filelink($route, $name = 'name', $options = [])
    {
        return Html::link(route($route, ['']), $this->fileinfo($name), $options);
    }
}
