<?php

namespace Cjpgdk\Laravel\JsRoutes;

use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Str;
use Route;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

/**
 * Class ServiceProvider.
 *
 * @package Cjpgdk\Laravel\JsRoutes
 * @author  Christian M. Jensen <cmj@cjpg.dk>
 * @license MIT
 * @link https://github.com/cjpgdk/laravel-js-routes
 */
class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadConfigs();
        $this->registerRouteMacros();
        $this->loadViewsFrom(realpath(__DIR__).'/../resources/views', 'js-routes');

        if ($this->app->runningInConsole()) {
            $this->_publishing();
        }

        $this->app->booted(
            function () {
                $this->_booted();
            }
        );
    }

    /**
     * Loads the configs for this package.
     *
     * @return void
     */
    protected function loadConfigs()
    {
        $this->mergeConfigFrom(
            realpath(__DIR__).'/config/js-routes.php',
            'js-routes'
        );
    }

    /**
     * Register route macros
     *
     * @return void
     */
    protected function registerRouteMacros()
    {
        /**
         * $route->compileAndGetRoute();
         * Returns the compiled route. @see \Symfony\Component\Routing\CompiledRoute
         */
        Route::macro(
            config('js-routes.macros.compileAndGetRoute', 'compileAndGetRoute'),
            function () {
                $this->compileRoute();
                return $this->getCompiled();
            }
        );
        /**
         * $route->getOptionalParameters();
         * Returns the optional route parameters as an array.
         *
         * $optional = [ 'name' => null, ...];
         */
        Route::macro(
            config(
                'js-routes.macros.getOptionalParameters',
                'getOptionalParameters'
            ),
            function () {
                $matches = [];
                // From: Route::extractOptionalParameters().
                preg_match_all('/\{(\w+?)\?\}/', $this->uri(), $matches);
                return isset($matches[1]) ? array_fill_keys($matches[1], null) : [];
            }
        );
    }

    /**
     * Publishable assets.
     *
     * @return void
     */
    private function _publishing()
    {
        $rootPath = realpath(__DIR__.'/../').DIRECTORY_SEPARATOR;
        // views
        $this->publishes(
            [$rootPath.'resources'.DIRECTORY_SEPARATOR.'views' => resource_path('views'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'js-routes')],
            'js-routes-views'
        );
        // js
        $this->publishes(
            [$rootPath.'resources'.DIRECTORY_SEPARATOR.'js' => resource_path('js'.DIRECTORY_SEPARATOR.'js-routes')],
            'js-routes-js'
        );
    }

    /**
     * Run when the app is booted.
     * Needed to be this way to get all routes loaded
     *
     * @return void
     */
    private function _booted()
    {
        $events = &$this->app['events'];

        // load all routes.
        $jsRoutes = [];
        foreach ($this->_getRoutes() as $route) {
            $domain = trim($route->domain(), '/');
            $uri = $route->uri();
            $url = $domain ? '//'.$domain.'/'.$uri : '/'.ltrim($uri, '/');
            // builds the variable data for index.
            $variables = function ($route) {
                $compiled = $route->compileAndGetRoute();
                $optional = $route->getOptionalParameters();
                $pathVars = $compiled->getPathVariables();

                $vars = [];
                foreach ($compiled->getTokens() as $token) {
                    // var is in path vars?
                    $allowed = !isset($token[3]) || !in_array($token[3], $pathVars);
                    if ($token[0] !== 'variable' && $allowed) {
                        continue;
                    }
                    $vars[$token[3]] = [
                        'regex' => $token[2],
                        'required' => !array_key_exists($token[3], $optional),
                    ];
                }
                return empty($vars) ? null : $vars;
            };

            $action = $route->getAction();
            $uri = $uri === '/' ? $uri : str_replace('/', '-', $uri);
            $name = $action['as'] ?? Str::camel($uri);
            $matches = [];
            $jsRoutes[$name] = [
                /* variables used in the string. */
                'variables' => $variables($route),
                'domain' => $domain ?: null,
                'uri' => $route->uri(),
                'url' => $url,
                'domainHasVariables' => preg_match_all(
                    '/\{(\w+)?\}/',
                    $domain,
                    $matches
                ) > 0,
                'domainVariables' => $matches[1] ?? null,
            ];
        }


        $views = config('js-routes.bind_to_view', ['footer', 'header']);
        foreach ($views as $view) {
            $events->listen(
                "composing: {$view}",
                function () use ($jsRoutes) {
                    $js = config(
                        'js-routes.js_routes_var',
                        'window.jsLaravelRoutes'
                    );
                    $js .= '='.json_encode($jsRoutes).';';
                    echo "<script>{$js}</script>";
                }
            );
        }
    }

    /**
     * Just an alias of 'Route::getRoutes();'
     *
     * @return RouteCollection
     * @see    Route::getRoutes()
     */
    private function _getRoutes()
    {
        $routes = Route::getRoutes();
        return $routes->getRoutes();
    }
}
