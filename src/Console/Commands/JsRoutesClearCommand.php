<?php

namespace Cjpgdk\Laravel\JsRoutes\Console\Commands;

use Illuminate\Foundation\Console\RouteClearCommand;

/**
 * Class JsRouteClearCommand
 *
 * @category Laravel
 * @package  Cjpgdk\Laravel\JsRoutes\Console\Commands
 * @author   Christian M. Jensen <cmj@cjpg.dk>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/cjpgdk/laravel-js-routes
 */
class JsRoutesClearCommand extends RouteClearCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'js-routes:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the js-route cache file';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->files->delete($this->laravel->bootstrapPath('cache/js-routes.php'));
        $this->info('JS Route cache cleared!');
    }
}
