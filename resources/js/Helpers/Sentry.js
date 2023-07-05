import * as Sentry from "@sentry/vue";

export function reportError(error) {
    if (import.meta.env.VITE_SENTRY_LARAVEL_DSN) {
        Sentry.captureException(error)
    }
}
