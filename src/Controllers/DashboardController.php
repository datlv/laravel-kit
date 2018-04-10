<?php namespace Datlv\Kit\Controllers;

use Datlv\Kit\Extensions\BackendController;

/**
 * Class DashboardController
 * @package Datlv\Kit\Controllers
 */
class DashboardController extends BackendController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('kit::backend.dashboard');
    }
}
