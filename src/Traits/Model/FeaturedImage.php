<?php

namespace Datlv\Kit\Traits\Model;

/**
 * Class FeaturedImage
 * Áp dụng cho Model có hình đại diện
 *
 * @property string $featured_image
 * @property array $config
 * @property-read string $featured_image_url
 * @property-read string $featured_image_sm_url
 * @property-read string $featured_image_full_url
 * @package Datlv\Kit\Traits\Model
 */
trait FeaturedImage
{
    /**
     * Khởi tạo trait
     */
    public static function bootFeaturedImage()
    {
        // Trước khi xóa $model, sẽ xóa Featured Image của nó
        static::deleting(function ($model) {
            /** @var static $model */
            $model->deleteFeaturedImage();
        });
    }

    /**
     * @param null $image
     * @return bool
     */
    public function isExternalFeaturedImage($image = null)
    {
        $image = $image ?: $this->featured_image;
        return str_is('http*', $image);
    }

    /**
     * Xóa Featured Image
     */
    public function deleteFeaturedImage()
    {
        if ($this->featured_image && !$this->isExternalFeaturedImage()) {
            $path = $this->featuredImageDir();
            @unlink("$path/{$this->featured_image}");
            @unlink("$path/sm-{$this->featured_image}");
            @unlink("$path/full-{$this->featured_image}");
        }
    }

    /**
     * Thư mục chứa hình đại diện
     *
     * @param bool $full
     *
     * @return string
     */
    public function featuredImageDir($full = true)
    {
        return ($full ? upload_path() : '/upload') . "/{$this->config['featured_image']['dir']}";
    }

    /**
     * $model->featured_image_url
     *
     * @return string
     */
    public function getFeaturedImageUrlAttribute()
    {
        return $this->featured_image ? $this->featuredImageUrl() : null;
    }

    /**
     * @param string $ver
     * @param string|null $image
     *
     * @return string
     */
    public function featuredImageUrl($ver = '', $image = null)
    {
        $image = $image ?: $this->featured_image;
        if (!$image) {
            return null;
        }

        if ($this->isExternalFeaturedImage($image)) {
            return $image;
        }

        $ver = $this->featuredImageVer($ver);
        if (!$this->featuredImageExists($ver, $image)) {
            $ver = '';
        }

        return "{$this->featuredImageDir(false)}/{$ver}{$image}";
    }

    /**
     * @param string $ver
     * @param null $image
     * @return bool
     */
    public function featuredImageExists($ver = '', $image = null)
    {
        return $this->isExternalFeaturedImage($image) ||
            is_file($this->featuredImagePath($this->featuredImageVer($ver), $image));
    }

    /**
     * Path(full?) của hình đại diện
     *
     * @param string $ver '' | 'sm-' | 'full-'
     * @param string|null $image
     *
     * @return string
     */
    public function featuredImagePath($ver = '', $image = null)
    {
        $image = $image ?: $this->featured_image;
        $ver = $this->featuredImageVer($ver);

        return $image ? "{$this->featuredImageDir()}/{$ver}{$image}" : null;
    }

    /**
     * $model->featured_image_sm_url
     *
     * @return string
     */
    public function getFeaturedImageSmUrlAttribute()
    {
        return $this->featured_image ? $this->featuredImageUrl('sm-') : null;
    }

    /**
     * $model->featured_image_full_url
     *
     * @return string
     */
    public function getFeaturedImageFullUrlAttribute()
    {
        return $this->featured_image ? $this->featuredImageUrl('full-') : null;
    }

    /**
     * Xử lý hình đại diện
     * - SEO tên file, thêm date time
     * - move đúng thư mục
     * - nếu edit thì xóa file cũ
     *
     * Tham số $request:
     * - Request object: hình ảnh upload
     * - array: hình ảnh từ file, [path, original file name]
     * - Imagick: hình ảnh 'on the fly', ví dụ từ PDF
     *
     * @param string|\Datlv\Kit\Extensions\Request|array|\Imagick $request
     * @param bool $full
     */
    public function fillFeaturedImage($request, $full = false)
    {
        if (is_string($request)) { //External Featured Image
            $this->featured_image = $request;
        } else {
            $method = empty($this->config['featured_image']['method']) ? 'fit' : $this->config['featured_image']['method'];
            $versions = [
                'main' => [
                    'width' => $this->config['featured_image']['width'],
                    'height' => $this->config['featured_image']['height'],
                    'method' => $method,
                ],
                'sm' => [
                    'width' => $this->config['featured_image']['width_sm'],
                    'height' => $this->config['featured_image']['height_sm'],
                    'method' => $method,
                ],
            ];
            $old_images = [$this->featuredImagePath(), $this->featuredImagePath('sm')];
            if ($full) {
                $versions['full'] = [
                    'width' => setting('display.image_width_max', 1600),
                    'height' => setting('display.image_height_max', 1024),
                    'method' => 'max',
                ];
                $old_images[] = $this->featuredImagePath('full');
            }
            $this->featured_image = save_image($request, 'image', $this->featured_image ? [
                $this->featuredImagePath(),
                $this->featuredImagePath('sm'),
                $this->featuredImagePath('full'),
            ] : null, $this->featuredImageDir(), $versions, [], $this->featured_image);
        }
    }

    /**
     * @param string $ver
     * @return string
     */
    protected function featuredImageVer($ver = '')
    {
        return $ver ? str_finish($ver, '-') : '';
    }
}
