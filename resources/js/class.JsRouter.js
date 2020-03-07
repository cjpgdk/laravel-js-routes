const JsRouter = class {

    /**
     * Create a new instance of this class.
     *
     * @param {Object} routes the available routes.
     */
    constructor(routes)
    {
        /**
         * The available routes
         *
         * @var     {Object}
         * @private
         */
        this._routes = routes;
        /**
         * The current navigation route!
         *
         * @type    {string|null}
         * @private
         */
        this._navigatingRoute = null;
        /**
         * The method used for navigating.
         *
         * @param {string} url
         *
         * @var     {function}
         * @private
         */
        this._navigator = (url) => {
            location.assign(url);
        }
    }

    /**
     * Gets the current navigator to use when navigating the app!
     *
     * @returns {Promise}
     */
    navigator(url)
    {
        return new Promise((resolve, reject) => {
                if (!this._navigator) {
                    reject("missing navigator, to set the default call 'this.setNavigator((url) => { location.assign(url); })'");
                } else {
                    this._navigator(url);
                    resolve();
                }
        });
    }

    /**
     * Sets the current method to for navigation.
     *
     * @param {function} navigator (url) => { ... };
     *
     * @returns {function}
     */
    setNavigator(navigator)
    {
        return this._navigator = navigator;
    }

    /**
     * Get all available routes.
     *
     * @returns {Object}
     */
    get routes()
    {
        return this._routes;
    }

    /**
     * Get a route Object by the route name.
     *
     * @param {string} route
     *
     * @returns {boolean|Object}
     */
    getRoute(route)
    {
        if (!this._routes.hasOwnProperty(route)) {
            console.error(`Route named '${route}', not found`);
            return false;
        }
        return this._routes[route];
    }

    navigateTo(route, options = {})
    {
        this._navigatingRoute = route;
        /*
            options = {
                vars: {

                },
                domain: {
                    var: 'value'
                    var2: 'value'
                }
            }
         */
        let routeObject = this.getRoute(route);
        if (!routeObject) {
            return false;
        }

        // parse domain variables.
        let url = this._parseRouteDomain(routeObject, options);
        if (!url) { return url; }

        // parse URI.
        url = this._parseRouteUri(url, routeObject, options);
        if (!url) { return url; }

        this.navigator(url).then(
            ()=> { this._navigatingRoute = null; }
        );
    }

    _parseRouteUri(url, routeObject, options)
    {
        if (!routeObject.variables || routeObject.variables.length === 0) {
            return url;
        }

        if (!options.hasOwnProperty('vars') || !options.vars) {
            console.error(
                `Route named '${this._navigatingRoute}', require variables: `
                +`${Object.keys(routeObject.variables).join(', ')}`
            );
            return false;
        }

        for (let varName in routeObject.variables) {
            let varOptions = routeObject.variables[varName]
            /* required var! */
            if (varOptions.required && !options.hasOwnProperty(varName)) {
                console.error(
                    `Route named '${this._navigatingRoute}', require variables: `
                    +`${Object.keys(routeObject.variables).join(', ')}`
                );
                return false;
            } else if (!varOptions.required && !options.hasOwnProperty(varName)) {
                /* optional var! but not in the var list. */
                url = url.replace(`/{${varName}?}`, '');
                continue;
            }

            /* @todo test with regex (varOptions.regex) the vars, but for now we just, assume it valid data!  */
            url = url.replace(`{${varName}}`, options.vars[varName]);
        }
        return url;
    }

    /**
     * Parse the domain in a route, with the options required.
     *
     * @param {Object} routeObject
     * @param {Object} options
     *
     * @returns {string|boolean} Boolean false, on error. See the console.
     * @private
     */
    _parseRouteDomain(routeObject, options)
    {
        // replace domain variables, if any.
        if (routeObject.domain && routeObject.domainHasVariables) {
            // make sure we got options for the domain.
            if (!options.hasOwnProperty('domain')) {
                console.error(
                    `Route named '${this._navigatingRoute}', require domain variables: `
                    +`${Object.keys(routeObject.domainVariables).join(', ')}`
                );
                return false;
            }
            // replace vars in the domain separated from the uri,
            // to allow for variables to be named the same in domain and uri.
            //
            // However if you do that, you should re-think your design!
            // This is a fix to migrate an old project. Will be removed at some point.
            let domain = routeObject.domain;
            for (let varName in routeObject.domainVariables) {
                let varOptions = routeObject.domainVariables[varName];

                // locate the required variable.
                if (!options.domain.hasOwnProperty(varName)) {
                    console.error(`Route named '${this._navigatingRoute}', require the domain variable '${varName}'`);
                    return false;
                }
                domain = domain.replace(`{${varName}}`, options.domain[varName]);
            }

            // return the new url.
            return domain+'/'+routeObject.uri;
        }
        // return default url, no domain variables.
        return routeObject.url;
    }
};


export default JsRouter;
