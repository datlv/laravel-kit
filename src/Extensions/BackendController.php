<?php

namespace Datlv\Kit\Extensions;

use Kit;

/**
 * Class BackendController
 * Các controllers thuộc khu vựa backend, mặc định user phải đăng nhập
 *
 * @package Datlv\Kit\Extensions
 */
abstract class BackendController extends Controller
{
    /**
     * @var string
     */
    protected $layout = 'kit::backend.layouts.master';

    /**
     * Init for web actions
     */
    protected function init()
    {
        $this->middleware('auth');
        $this->home_url = route($this->route_prefix . 'backend.dashboard');
        $this->home_label = trans('backend.dashboard');
        Kit::currentZone('backend');
    }
}
