function isSupportedProtocol(urlString) {
    const supportedProtocols = ["https:", "http:"];
    const url = document.createElement('a');
    url.href = urlString;

    return supportedProtocols.indexOf(url.protocol) !== -1;
}

browser.browserAction.onClicked.addListener((tab) => {
    if (!isSupportedProtocol(tab.url)) {
        console.info(`The url ("${tab.url}") of your active tab is not supported`);
        return;
    }

    console.info('The extension script will be registered');
    browser.tabs.executeScript({ file: '/content_scripts/script.js' })
        .then(() => console.info('The script has been registered'))
        .catch((error) => console.error('Cannot register script', error));
});
