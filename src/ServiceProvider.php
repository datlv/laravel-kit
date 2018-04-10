<?php

namespace Datlv\Kit;

use Collective\Html\FormFacade;
use Collective\Html\HtmlFacade;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Datlv\Kit\Extensions\Html\HtmlServiceProvider;
use Datlv\Kit\Middleware\MinifyHtml;
use Schema;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        Schema::defaultStringLength(191);
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'kit');
        $this->loadViewsFrom(realpath(__DIR__.'/../views'), 'kit');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->publishes([
            __DIR__.'/../config/kit.php' => config_path('kit.php'),
        ]);
        if($this->app->has('menu-manager')){
            app('menu-manager')->addItems(config('kit.menus'));
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/kit.php', 'kit');
        $this->app->singleton('kit', function () {
            return new Manager();
        });

        $this->app->register(HtmlServiceProvider::class);
        // add Kit, Presenter,Form alias
        $this->app->booting(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('Kit', Facade::class);
            $loader->alias('Html', HtmlFacade::class);
            $loader->alias('Form', FormFacade::class);
        });
        app('router')->pushMiddlewareToGroup('web', MinifyHtml::class);

        if ($this->app->environment() == 'local') {
            $this->app->register('Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider');
            $this->app->register('Clockwork\Support\Laravel\ClockworkServiceProvider');
            app('Illuminate\Contracts\Http\Kernel')->pushMiddleware('Clockwork\Support\Laravel\ClockworkMiddleware');
        }
    }

    public function provides()
    {
        return ['kit'];
    }
}
