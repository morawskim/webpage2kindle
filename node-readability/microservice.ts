import express, { Express, Request, Response } from 'express';
import { Readability } from '@mozilla/readability';
import { JSDOM } from 'jsdom';
import * as dotenv from 'dotenv';
import * as Sentry from "@sentry/node";
import * as Tracing from "@sentry/tracing";

dotenv.config({ path: "/app/.env" });
const app: Express = express();
const port = 3000;
Sentry.init({
    dsn: process.env.SENTRY_DSN,
    integrations: [
        // enable HTTP calls tracing
        new Sentry.Integrations.Http({ tracing: true }),
        // enable Express.js middleware tracing
        new Tracing.Integrations.Express({ app }),
    ],
    tracesSampleRate: 1.0,
});

app.use(Sentry.Handlers.requestHandler({transaction: "methodPath"}));
app.use(Sentry.Handlers.tracingHandler());
app.use(express.urlencoded({ extended: true, limit: '5mb' }));

app.post('/process-webpage', (req: Request, res: Response) => {
    const {body, url} = req.body;
    const doc = new JSDOM(body, {
        url: url
    });
    const reader = new Readability(doc.window.document);
    const article = reader.parse();

    const responseObject = {
        'success': !!(article && article.content.length),
        'body': article && article.content
    };

    res.setHeader('X-Foo', 'abc');
    res.json(responseObject);
});

// The error handler must be before any other error middleware and after all controllers
app.use(Sentry.Handlers.errorHandler());

app.listen(port, () => {
    console.log(`Microservice is listening on port ${port}`);
});
