import * as Sentry from "@sentry/vue";

export function resportError(error) {
    if (import.meta.env.VITE_SENTRY_LARAVEL_DSN) {
        Sentry.captureException(error)
    }
}
