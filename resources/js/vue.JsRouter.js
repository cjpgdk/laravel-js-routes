import JsRouter from "./class.JsRouter";

/**
 * A simple plugin for VueJS to ouse the JsRouter class.
 *
 * @type {{install: VueJsRouter.install}}
 */
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
         * @param {*}  event used to prevent default like any other click event.
         */
        Vue.navigateTo = function (route, options = {}, event) {
            Vue.JsRouter.navigateTo(route, options, event);
        };

        /**
         * Navigate to the given route.
         *
         * @param {string} route
         * @param {object} options
         * @param {*}  event used to prevent default like any other click event.
         */
        Vue.prototype.$navigateTo = function (route, options = {}, event) {
            Vue.JsRouter.navigateTo(route, options, event);
        };
    }
};


export default VueJsRouter;
