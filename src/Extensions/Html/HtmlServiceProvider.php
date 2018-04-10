<?php
namespace Datlv\Kit\Extensions\Html;

/**
 * Class HtmlServiceProvider
 *
 * @package Datlv\Kit\Extensions\Presenter
 */
class HtmlServiceProvider extends \Collective\Html\HtmlServiceProvider
{
    /**
     * Register my custom html builder instance.
     *
     * @return void
     */
    protected function registerHtmlBuilder()
    {
        $this->app->singleton('html', function ($app) {
            return new HtmlBuilder($app['url'], $app['view']);
        });
    }

    /**
     * Register my custom form builder instance.
     *
     * @return void
     */
    protected function registerFormBuilder()
    {
        $this->app->singleton('form', function ($app) {
            $form = new FormBuilder($app['html'], $app['url'], $app['view'], $app['session.store']->token());

            return $form->setSessionStore($app['session.store']);
        });
    }
}
