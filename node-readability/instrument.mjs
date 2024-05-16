import * as Sentry from "@sentry/node";
import * as dotenv from 'dotenv';

dotenv.config({ path: "/app/.env" });
console.log(process.env.SENTRY_DSN)
Sentry.init({
    dsn: process.env.SENTRY_DSN,
    tracesSampleRate: 1.0,
});
