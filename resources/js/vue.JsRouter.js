import JsRouter from "./class.JsRouter";

const VueJsRouter = {
    install: (Vue, options) => {
        /**
         * The JsRouter class.
         *
         * @type {JsRouter}
         */
        Vue.JsRouter = new JsRouter(options.routes);

        /**
         * Navigate to the given route.
         *
         * @param {string} route
         * @param {object} options
         */
        Vue.navigateTo = function (route, options = {}) {
            Vue.JsRouter.navigateTo(route, options);
        };

        /**
         * Navigate to the given route.
         *
         * @param {string} route
         * @param {object} options
         */
        Vue.prototype.$navigateTo = function (route, options = {}) {
            Vue.JsRouter.navigateTo(route, options);
        };
    }
};


export default VueJsRouter;
