<?php
namespace Datlv\Kit\Middleware;

use Closure;

/**
 * Class MinifyHtml
 * Minify mã HTML trước khi trả về cho browser
 *
 * @package Datlv\Kit\Middleware
 */
class MinifyHtml
{

    /**
     * After Request Middleware
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if (setting('system.minify_html') && $response instanceof \Illuminate\Http\Response) {
            $output = $response->getOriginalContent();
            $output = mb_html_minify($output);
            $response->setContent($output);
        }

        return $response;
    }
}
