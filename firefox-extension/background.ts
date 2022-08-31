import browser from "webextension-polyfill";

interface ResultSuccess {
    success: true;
    pushToKindleUrl: string;
    title: string;
}

interface ResultFailed {
    success: false;
}

function isSupportedProtocol(urlString: string) {
    const supportedProtocols = ["https:", "http:"];
    const url = document.createElement('a');
    url.href = urlString;

    return supportedProtocols.indexOf(url.protocol) !== -1;
}

function notify(message: ResultSuccess|ResultFailed) {
    if (message.success) {
        browser.notifications.create({
            type: 'basic',
            iconUrl: 'icons/icon-48.png',
            title: `Link is ready (${message.title})`,
            message: message.pushToKindleUrl,
            priority: 0,
        });
        browser.tabs.create({
            url: message.pushToKindleUrl,
        });
    } else {
        browser.notifications.create({
            type: 'basic',
            iconUrl: 'icons/icon-48.png',
            title: 'Error during creating url to pushtokindle',
            message: 'Check console',
            priority: 0,
        });
    }
}

browser.runtime.onMessage.addListener(notify);
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
