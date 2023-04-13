import "../sass/app.scss"
window.global ||= window
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { directive as Tippy } from 'vue-tippy'
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m'
import Closable from "./Directives/Closable"
import mitt from 'mitt'
import * as Sentry from "@sentry/vue"
const emitter = mitt()

const app = createInertiaApp({
    id: 'app',
    progress: {
        color: '#0077FF',
    },
    title: (title) => title,
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
        .use(plugin)
        .use(ZiggyVue, Ziggy)
        .directive('tippy', Tippy)
        .directive('closable', Closable)
        .provide('emitter', emitter)
        .mount(el)
    },
})

// if (import.meta.env.VITE_SENTRY_LARAVEL_DSN) {
//     Sentry.init({
//         app,
//         dsn: import.meta.env.VITE_SENTRY_LARAVEL_DSN,
//         integrations: [
//             new Sentry.BrowserTracing({
//                 // routingInstrumentation: Sentry.vueRouterInstrumentation(router),
//                 // tracingOrigins: ["app.mintpad.co", "on.mintpad.co", "beta.mintpad.co", /^\//],
//                 tracePropagationTargets: ["app.mintpad.co", "on.mintpad.co", "beta.mintpad.co", /^\//],
//             }),
//         ],
//         // Set tracesSampleRate to 1.0 to capture 100%
//         // of transactions for performance monitoring.
//         // We recommend adjusting this value in production
//         tracesSampleRate: 0,
//     });
// }