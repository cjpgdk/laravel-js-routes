<?php

namespace Cjpgdk\Laravel\JsRoutes\Console\Commands;

use Illuminate\Foundation\Console\RouteCacheCommand;
use Illuminate\Support\Str;


class JsRoutesCacheCommand extends RouteCacheCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'js-routes:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a js-route cache file for faster route registration';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('js-routes:clear');

        $routes = $this->getFreshApplicationRoutes();

        if (count($routes) === 0) {
            return $this->error("Your application doesn't have any routes.");
        }


        $jsRoutes = [];
        foreach ($routes as $route) {
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

        $this->files->put(
            $this->laravel->bootstrapPath('cache/js-routes.php'),
            "<?php\nreturn unserialize(base64_decode('".base64_encode(serialize($jsRoutes))."'));\n"
        );

        $this->info('JS Routes cached successfully!');
        return null;
    }
}
