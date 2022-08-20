import express, { Express, Request, Response } from 'express';
import { Readability } from '@mozilla/readability';
import { JSDOM } from 'jsdom';

const app: Express = express();
const port = 3000;

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

app.listen(port, () => {
    console.log(`Microservice is listening on port ${port}`)
});
