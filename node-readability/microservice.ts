import { Readability } from '@mozilla/readability';
import { JSDOM } from 'jsdom';
import express, { Express, Request, Response } from 'express';
import * as Sentry from "@sentry/node";

const port = 3000;
const app: Express = express();
app.use(express.urlencoded({ extended: true, limit: '5mb' }));

app.post('/process-webpage', (req: Request, res: Response) => {
    const {body, url, title} = req.body;
    const doc = new JSDOM(body, {
        url: url,
    });
    const reader = new Readability(doc.window.document);
    const article = reader.parse();
    const articleContent = (article && article.content) ? `<h1>${title}</h1>` + article.content : '';

    const responseObject = {
        'success': !!(article && article.content.length),
        'body': articleContent,
    };

    res.json(responseObject);
});

// The error handler must be before any other error middleware and after all controllers
Sentry.setupExpressErrorHandler(app);

app.listen(port, () => {
    console.log(`Microservice is listening on port ${port}`);
});
