## laravel-js-routes.
Just an other way of loading Laravel routes into Javascript.
Nothing special!

## Install

`composer require cjpgdk/laravel-js-routes`

*Publish the JS files, you most likely want to change them!*

`php artisan vendor:publish --provider="Cjpgdk\Laravel\JsRoutes\ServiceProvider" --tag=js-routes-js`


*Publish the View files, Unless you just use your own in config `'bind_to_view' => ['js-routes::header']`*

`php artisan vendor:publish --provider="Cjpgdk\Laravel\JsRoutes\ServiceProvider" --tag=js-routes-js`


**NOTE** The js-routes service provider, is auto loaded in Laravel, you may disable this by adding the following code to your `composer.json`, if you need to load the package after other dependencies.

```json
{
    "extra": {
        "laravel": {
            "dont-discover": [
                "Cjpgdk\\Laravel\\JsRoutes\\ServiceProvider"
            ]
        }
    }
}
```


#### After install
load the header view `@include('js-routes::header')`, this will append all loaded routes 
into `window.jsLaravelRoutes`.

*load js in `app.js`* 

```javascript
/* plain JavaScript */
import JsRouter from "./js-routes/class.JsRouter";
window.JsRouter = new JsRouter(window.jsLaravelRoutes);
/* See the file for methods. */

// the VueJs file loads the file class.JsRouter, 
// so no need to load both, if only using Vue.

/* VueJS */
window.Vue = require('vue');
import VueJsRouter from "./js-routes/vue.JsRouter";
Vue.use(VueJsRouter, { 
    /* all routes or value of config: `js_routes_var` */
    routes: window.jsLaravelRoutes
});
/*
 * Vue.JsRouter : class instance, See the file for methods.
 * 
 * Vue.navigateTo('route-name', {
 *     // variables used in the domain, optional variables goes here as well.
 *     vars: {
 *         var: 'value',
 *         var2: 'value'
 *     },
 *     // variables used in the domain, if needed, then they are required.
 *     domain: {
 *         var: 'value',
 *         var2: 'value'
 *     }
 * });
 * 
 *  OR in components.
 * 
 *  this.$navigateTo('route-name', {...});
 *
 */

```


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
