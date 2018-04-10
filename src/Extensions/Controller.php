<?php

namespace Datlv\Kit\Extensions;

use Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use View;
use ReflectionClass;
use Kit;

/**
 * Class Controller
 *
 * @package Datlv\Kit\Extensions
 */
abstract class Controller extends BaseController
{
    use ValidatesRequests;

    /**
     * @var string
     */
    protected $home_url;

    /**
     * @var string
     */
    protected $home_label;

    /**
     * @var string
     */
    protected $layout;

    /**
     * @var string
     */
    public $route_prefix;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        if (config('app.under_construction') && ! Request::is('auth*')) {
            $this->middleware('auth');
        }
        $this->init();
        $this->bootTraits();
        View::share('home_url', $this->home_url);
        View::share('home_label', $this->home_label);
        View::share('layout', $this->layout);
        View::share('route_prefix', $this->route_prefix);
        View::share('page_heading', null);
        View::share('page_icon', null);
        View::share('page_buttons', null);
        View::share('breadcrumbs', null);
        if (app()->has('menu-manager')) {
            app('menu-manager')->addItems(config('app.menus'));
        }
    }

    protected function init()
    {
        $this->home_url = url('/');
        $this->home_label = trans('common.home');
        Kit::currentZone('frontend');
    }

    /**
     * Boot all of the bootable traits on the controller.
     * - Ý tưởng giống boot của Eloquent, nhưng không static
     * - Bootable traits: trait có method tên bootTênTrait,
     *   vd: trait HasPermission => method bootHasPermission
     */
    protected function bootTraits()
    {
        foreach (class_uses_recursive(get_class($this)) as $trait) {
            if (method_exists($this, $method = 'boot'.class_basename($trait))) {
                call_user_func([$this, $method]);
            }
        }
    }

    /**
     * @param string|array $breadcrumbs
     * @param bool $homeItem
     *
     * @return array
     */
    protected function buildBreadcrumbs($breadcrumbs, $homeItem = true)
    {
        if (is_string($breadcrumbs)) {
            $breadcrumbs = ['#' => $breadcrumbs];
        }
        foreach ($breadcrumbs as &$label) {
            $label = str_limit($label, 40);
        }
        if ($homeItem) {
            $breadcrumbs = [$this->home_url => $this->home_label] + $breadcrumbs;
        }
        View::share('breadcrumbs', $breadcrumbs);

        return $breadcrumbs;
    }

    /**
     * @param string|array $heading
     * @param string $icon
     * @param array $breadcrumbs
     * @param null|array $buttons
     * @param bool $homeItem
     */
    protected function buildHeading($heading, $icon, $breadcrumbs, $buttons = null, $homeItem = true)
    {
        if (is_array($heading)) {
            $heading = implode('|||', $heading);
        }
        View::share('page_heading', $heading);
        View::share('page_icon', $icon);
        View::share('page_buttons', $buttons);
        $this->buildBreadcrumbs($breadcrumbs, $homeItem);
    }

    /**
     * @param string $layout
     */
    protected function layout($layout)
    {
        $this->layout = $layout;
        View::share('layout', $layout);
    }

    /**
     * Create new class instance:
     * - $arg1: class name
     * - $arg2..$argN: class constructor params
     *
     * @return object
     */
    protected function newClassInstance()
    {
        $args = func_get_args();
        abort_unless($args, 500, '[Controller::newClassInstance] class name is empty!');
        $class = array_shift($args);
        $reflection_class = new ReflectionClass($class);

        return $reflection_class->newInstanceArgs($args);
    }

    /**
     * Get current request action name
     *
     * @return string
     */
    protected function getActionName()
    {
        return app()->runningInConsole() ? null : str_replace(static::class.'@', '', Request::route()->getActionName());
    }
}
