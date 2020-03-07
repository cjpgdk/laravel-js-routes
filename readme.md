## laravel-js-routes.


## Install

`composer require cjpgdk/laravel-js-routes`

*Publish the JS files, you most likely want to change them!*

`php artisan vendor:publish --provider="Cjpgdk\Laravel\JsRoutes\ServiceProvider" --tag=js-routes-js`


*Publish the View files, Unless you just use your own in config `'bind_to_view' => ['js-routes::header']`*

`php artisan vendor:publish --provider="Cjpgdk\Laravel\JsRoutes\ServiceProvider" --tag=js-routes-js`

#### After install
load the header view `@include('js-routes::header')`, this will append all loaded routes 
into `window.jsLaravelRoutes`.


load js....



#### Config
*js-routes.php* namespace `js-routes`

```php
return [
    /*
    |--------------------------------------------------------------------------
    | View to bind the JsLaravelRoutes to.
    |--------------------------------------------------------------------------
    |
    | Set this value to the name of the view that
    | you want to prepend the JavaScript to.
    | This can be a single view, or an array of views.
    | Default: header
    | Example: ['footer', 'header'] or ['footer']
    | Note:    if using an array make sure the views
    |          are not loaded on the same page.
    | Default: header
    |
    */
    'bind_to_view' => ['js-routes::header'],

    /*
    |--------------------------------------------------------------------------
    | Routers Namespace
    |--------------------------------------------------------------------------
    |
    | It's recommended that you change this to something ... anything.
    |
    | Default: window.jsLaravelRoutes
    |
    */
    'js_routes_var' => 'window.jsLaravelRoutes',

    /*
    |--------------------------------------------------------------------------
    | Router macros names.
    |--------------------------------------------------------------------------
    |
    | Only edit if the names are causing a conflict.
    |
    | Format: Internal-name => Alias.
    |
    */
    'macros' => [
        /**
         * $route->compileAndGetRoute();
         * Returns the compiled route. @see \Symfony\Component\Routing\CompiledRoute
         */
        'compileAndGetRoute' => 'compileAndGetRoute',
        /**
         * $route->getOptionalParameters();
         * Returns the optional route parameters as an array.
         *
         * $optional = [ 'name' => null, ...];
         */
        'getOptionalParameters' => 'getOptionalParameters',
    ]
];
```
