import browser from "webextension-polyfill";

interface ResultSuccess {
    success: true;
    pushToKindleUrl: string;
    title: string;
    tabUrl: string;
}

interface ResultFailed {
    success: false;
    title: string;
}

function isSupportedProtocol(urlString: string) {
    const supportedProtocols = ["https:", "http:"];
    const url = document.createElement('a');
    url.href = urlString;

    return supportedProtocols.indexOf(url.protocol) !== -1;
}

async function notify(message: ResultSuccess|ResultFailed) {
    if (message.success) {
        const tabs = await browser.tabs.query({ url: message.tabUrl});
        if (tabs.length) {
            const tabId = tabs[0].id;

            browser.tabs.update(tabId, {
                url: message.pushToKindleUrl,
            }).catch((e) => {
                console.error(e);
                browser.notifications.create({
                    type: 'basic',
                    iconUrl: 'icons/icon-48.png',
                    title: 'Cannot update tab URL',
                    message: `Check also console.log. Error message: "${e.message}"`,
                    priority: 0,
                });
            });
        } else {
            browser.tabs.create({
                url: message.pushToKindleUrl,
            });
        }
    } else {
        browser.notifications.create({
            type: 'basic',
            iconUrl: 'icons/icon-48.png',
            title: `Error during creating url to pushtokindle for ${message.title}`,
            message: 'Check console',
            priority: 0,
        });
    }
}

browser.runtime.onMessage.addListener(notify as any);
browser.browserAction.onClicked.addListener((tab) => {
    if (!tab.url || !isSupportedProtocol(tab.url)) {
        console.info(`The url ("${tab.url}") of your active tab is not supported`);
        return;
    }

    console.info('The extension script will be registered');
    browser.tabs.executeScript({ file: '/contentScript.js' })
        .then(() => console.info('The script has been registered'))
        .catch((error: unknown) => console.error('Cannot register script', error));
});

