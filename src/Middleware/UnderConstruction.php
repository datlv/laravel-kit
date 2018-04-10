<?php namespace Datlv\Kit\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

/**
 * Todo: Chưa dùng vì chưa giải quyết được redirect vô tận
 * Class UnderConstruction
 *
 * @package Datlv\Kit\Middleware
 */
class UnderConstruction
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * After Request Middleware
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (($request->path() != 'auth/login') && config('app.under_construction') && $this->auth->guest()) {
            return redirect()->guest(route('auth.login'));
        }

        return $next($request);
    }
}
