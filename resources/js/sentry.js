import * as Sentry from "@sentry/vue";
import { BrowserTracing } from "@sentry/tracing";

export default function initSentry(Vue) {
    if (process.env.MIX_SENTRY_ENABLED == 'true') {
        Sentry.init({
            Vue,
            dsn: process.env.MIX_SENTRY_LARAVEL_DSN,
            integrations: [
                new BrowserTracing({
                    // routingInstrumentation: Sentry.vueRouterInstrumentation(router),
                    tracingOrigins: ["mintpad.tnwebsolutions.nl", "mplp.tnwebsolutions.nl", /^\//],
                }),
            ],
            // Set tracesSampleRate to 1.0 to capture 100%
            // of transactions for performance monitoring.
            // We recommend adjusting this value in production
            tracesSampleRate: 0,
        });
    }
}