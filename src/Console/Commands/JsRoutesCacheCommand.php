<?php

namespace Cjpgdk\Laravel\JsRoutes\Console\Commands;

use Cjpgdk\Laravel\JsRoutes\ServiceProvider as JsRoutesServiceProvider;
use Illuminate\Foundation\Console\RouteCacheCommand;
use Illuminate\Support\Str;

/**
 * Class JsRoutesCacheCommand
 *
 * @category Laravel
 * @package  Cjpgdk\Laravel\JsRoutes\Console\Commands
 * @author   Christian M. Jensen <cmj@cjpg.dk>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/cjpgdk/laravel-js-routes
 */
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

        $jsRoutes = JsRoutesServiceProvider::buildJsRoutes($routes);

        $this->files->put(
            $this->laravel->bootstrapPath('cache/js-routes.php'),
            "<?php\nreturn unserialize(base64_decode('".base64_encode(serialize($jsRoutes))."'));\n"
        );

        return $this->info('JS Routes cached successfully!');
    }
}
