import * as Sentry from "@sentry/vue";
import { BrowserTracing } from "@sentry/tracing";

export function initSentry(app) {
    if (process.env.MIX_SENTRY_LARAVEL_DSN) {
        Sentry.init({
            app,
            dsn: process.env.MIX_SENTRY_LARAVEL_DSN,
            integrations: [
                new BrowserTracing({
                    // routingInstrumentation: Sentry.vueRouterInstrumentation(router),
                    tracingOrigins: ["app.mintpad.co", "on.mintpad.co", "beta.mintpad.co", /^\//],
                }),
            ],
            // Set tracesSampleRate to 1.0 to capture 100%
            // of transactions for performance monitoring.
            // We recommend adjusting this value in production
            tracesSampleRate: 0,
        });
    }
}

export function resportError(error) {
    if (process.env.MIX_SENTRY_LARAVEL_DSN) {
        Sentry.captureException(error)
    }
}
