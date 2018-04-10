# Laravel Kit
Kit cơ bản cho Laravel Application

## Packages cần có

* vinkla/hashids

## Install

* **Thêm vào file composer.json của app**
```json
	"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/datlv/laravel-kit"
        }
    ],
    "require": {
        "datlv/laravel-kit": "dev-master"
    }
```
``` bash
$ composer update
```

* **Thêm vào file config/app.php => 'providers'**
```php
	Datlv\Kit\ServiceProvider::class,
```

* **Thêm vào file app/Http/Kernel.php => $middleware** (cuối cùng)
```php
protected $middleware = [
	//...
	\Datlv\Kit\Middleware\MinifyHtml::class,
];
```
## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
