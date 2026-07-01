import puppeteer from 'puppeteer-core';
import path from 'node:path';

const screenshotPath = path.join(process.cwd(), 'screenshot.png');
let failed = false;
const browser = await puppeteer.connect({
    browserWSEndpoint: "ws://127.0.0.1:9222/session",
    protocol: 'webDriverBiDi',
});
const page = await browser.newPage();
await page.goto('https://pl.wikipedia.org/wiki/PHP');
await page.setViewport({width: 1080, height: 1024});

page.on('console', msg => {
    console.log(`[browser:${msg.type()}] ${msg.text()}`);
});

await page.evaluate(() => {
    window.postMessage({
        type: "webpage2kindle.createReadableVersion",
    }, "*");
});
try {
    await page.waitForFunction(
        () => window.location.href.startsWith('https://pushtokindle.fivefilters.org'),
        {
            timeout: 20_000,
            polling: 1000,
        }
    );
    console.log('Success');
} catch (err) {
    failed = true;
    console.error(`Something wrong. Check console.logs in output and screenshot ${screenshotPath}`, err);
} finally {
    await page.screenshot({
        path: screenshotPath,
        fullPage: true,
    });
    await browser.disconnect();
    // await browser.close();
    if (failed) {
        process.exit(1);
    }
}
