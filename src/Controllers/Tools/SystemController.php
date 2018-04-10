<?php namespace Datlv\Kit\Controllers\Tools;

use Datlv\Kit\Extensions\BackendController;
use Route;
use Request;
use Session;
use App;
use Kit;

/**
 * Class SystemController
 *
 * @package Datlv\Kit\Controllers\Tools
 */
class SystemController extends BackendController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPhpinfo()
    {
        $this->buildHeading(['PHP', 'Informations'], 'fa-info-circle', ['#' => 'php_info()']);
        ob_start();
        phpinfo();
        $pinfo = ob_get_contents();
        ob_end_clean();

        $pinfo = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $pinfo);

        return view('kit::backend.tool.phpinfo', compact('pinfo'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPrettyRoutes()
    {
        $only_controller = Request::get('controller');
        $hide_methods = config('kit.pretty_routes.hide_methods');
        $allRoutes = Route::getRoutes();
        $count_allRoutes = count($allRoutes);
        $routes = [];
        $controllers = [];
        $current_controller = null;
        if ($only_controller) {
            $count_routes = 0;
            foreach ($allRoutes as $route) {
                $action = $route->getActionName();
                if (strpos($action, '@') !== false) {
                    list($controller, $action) = explode('@', $action, 2);
                    $controllers[$controller] = empty($controllers[$controller]) ? 1 : $controllers[$controller] + 1;
                    if ($controller == $only_controller) {
                        $name = $route->getName();
                        $routes[] = [
                            'action'     => $action,
                            'methods'    => array_diff($route->methods(), $hide_methods),
                            'uri'        => $route->uri(),
                            'name'       => substr($name, -1) == '.' ? '' : $name,
                            'middleware' => $route->middleware(),
                        ];
                        $count_routes++;
                    }
                }
            }
        } else {
            $item = [];
            $count_routes = $count_allRoutes;
            foreach ($allRoutes as $route) {
                $item['action'] = $route->getActionName();
                if (strpos($item['action'], '@') !== false) {
                    list($controller, $action) = explode('@', $item['action'], 2);
                    $controllers[$controller] = empty($controllers[$controller]) ? 1 : $controllers[$controller] + 1;
                    $item['action'] = $action;
                    if ($controller != $current_controller) {
                        $routes[] = ['controller' => $controller];
                        $current_controller = $controller;
                    }
                }
                $item['methods'] = array_diff($route->methods(), $hide_methods);
                $item['uri'] = $route->uri();
                $item['name'] = $route->getName();
                if (substr($item['name'], -1) == '.') {
                    $item['name'] = '';
                }
                $item['middleware'] = $route->middleware();
                $routes[] = $item;
            }
        }
        $this->buildHeading(['Routes', '(' . $count_routes . ')'], 'fa-sitemap', ['#' => 'routes']);

        $url = route('backend.tools.pretty_routes');
        $all_controllers = [null => 'All controllers'];

        foreach ($controllers as $controller => $count) {
            $all_controllers[$controller] = "$controller ($count)";
        }

        return view('kit::backend.tool.pretty_routes', compact('routes', 'url', 'all_controllers', 'only_controller'));
    }

    /**
     * @return \Illuminate\View\View
     */
    public function checkWriteable()
    {
        $this->buildHeading(
            trans('backend.check_dir_writeable'),
            'fa-check-square-o',
            ['#maintenance' => trans('backend.maintenance'), '#' => trans('backend.check_dir')]
        );
        $base_path = base_path();
        $base_path = substr($base_path, 0, strrpos($base_path, '/'));
        $paths = [];
        $ok = true;
        foreach (Kit::writeablePaths() as $info) {
            $exist = is_dir($info['path']) || is_file($info['path']);
            $writeable = $exist && is_writeable($info['path']);
            $path = str_replace($base_path, '--', $info['path']);
            $paths[$path] = [
                'name'      => $info['title'],
                'exist'     => $exist,
                'writeable' => $writeable,
            ];
            $ok = $ok && $exist;
        }

        return view('kit::backend.tool.writeable', compact('paths', 'base_path', 'ok'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fixWriteable()
    {
        foreach (Kit::writeablePaths() as $info) {
            if (!is_dir($info['path'])) {
                @mkdir($info['path']);
            }
            if (!is_writable($info['path'])) {
                @chmod($info['path'], 0777);
            }
        }
        Session::flash(
            'message',
            [
                'type'    => 'success',
                'content' => trans('backend.fix_writeable_success'),
            ]
        );

        return back();
    }

    // System Informations ---------------------------------

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function system_info()
    {
        $this->buildHeading('System Informations', 'fa-puzzle-piece',
            ['#tools' => trans('backend.tools'), '#' => 'System Informations'],
            [
                [
                    '#',
                    'Get System Report',
                    ['id' => 'btn-report', 'icon' => 'fa-quote-left', 'type' => 'info', 'size' => 'sm'],
                ],
                [
                    'https://github.com/lubusIN/laravel-decomposer/blob/master/report.md',
                    'Understand Report',
                    [
                        'id'     => 'btn-about-report',
                        'icon'   => 'fa-question-circle',
                        'type'   => 'white',
                        'size'   => 'sm',
                        'target' => "_blank",
                    ],
                ],
            ]
        );

        $json = file_get_contents(base_path('composer.json'));
        $composerArray = json_decode($json, true);
        $packagesArray = $composerArray['require'];

        foreach ($packagesArray as $key => $value) {
            if ($key !== 'php') {
                $json2 = file_get_contents(base_path("/vendor/{$key}/composer.json"));
                $dependenciesArray = json_decode($json2, true);
                $dependencies = array_key_exists('require',
                    $dependenciesArray) ? $dependenciesArray['require'] : 'No dependencies';

                $packages[] = [
                    'name'         => $key,
                    'version'      => $value,
                    'dependencies' => $dependencies,
                ];
            }
        }

        $laravelEnv = $this->getLaravelEnv();

        $serverEnv = $this->getServerEnv();

        return view('kit::backend.tool.system_info', compact('packages', 'laravelEnv', 'serverEnv'));
    }

    /**
     * Get Laravel environment details
     *
     * @return array
     */
    private function getLaravelEnv()
    {
        return [
            'version'              => App::version(),
            'timezone'             => config('app.timezone'),
            'debug_mode'           => config('app.debug'),
            'storage_dir_writable' => is_writable(base_path('storage')),
            'cache_dir_writable'   => is_writable(base_path('bootstrap/cache')),
            'app_size'             => $this->sizeFormat($this->folderSize(base_path())),
        ];
    }

    /**
     * Get PHP/Server environment details
     *
     * @return array
     */
    private function getServerEnv()
    {
        return [
            'version'                  => phpversion(),
            'server_software'          => $_SERVER['SERVER_SOFTWARE'],
            'server_os'                => php_uname(),
            'database_connection_name' => config('database.default'),
            'ssl_installed'            => $this->checkSslIsInstalled(),
            'cache_driver'             => config('cache.default'),
            'session_driver'           => config('session.driver'),
            'openssl'                  => extension_loaded('openssl'),
            'pdo'                      => extension_loaded('pdo'),
            'mbstring'                 => extension_loaded('mbstring'),
            'tokenizer'                => extension_loaded('tokenizer'),
            'xml'                      => extension_loaded('xml'),
        ];
    }

    /**
     * Check if SSL is installed or not
     *
     * @return boolean
     */
    private function checkSslIsInstalled()
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? true : false;
    }

    /**
     * Get the laravel app's size
     *
     * @param string $dir
     *
     * @return int
     */
    private function folderSize($dir)
    {
        $size = 0;
        foreach (glob(rtrim($dir, '/') . '/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : $this->folderSize($each);
        }

        return $size;
    }

    /**
     * Format the app's size in correct units
     *
     * @param int $bytes
     *
     * @return string
     */

    private function sizeFormat($bytes)
    {
        $kb = 1024;
        $mb = $kb * 1024;
        $gb = $mb * 1024;
        $tb = $gb * 1024;

        if (($bytes >= 0) && ($bytes < $kb)) {
            return $bytes . ' B';
        } elseif (($bytes >= $kb) && ($bytes < $mb)) {
            return ceil($bytes / $kb) . ' KB';
        } elseif (($bytes >= $mb) && ($bytes < $gb)) {
            return ceil($bytes / $mb) . ' MB';
        } elseif (($bytes >= $gb) && ($bytes < $tb)) {
            return ceil($bytes / $gb) . ' GB';
        } elseif ($bytes >= $tb) {
            return ceil($bytes / $tb) . ' TB';
        } else {
            return $bytes . ' B';
        }
    }
}
