import browser from "webextension-polyfill";

declare global {
    interface Window {
        hasRun: boolean;
    }
}

function createLoaderElement() {
    const loader = document.createElement('div');
    const style = loader.style;
    style.position = 'fixed';
    style.top = '0';
    style.right = '0';
    style.width = '100%';
    style.backgroundColor = '#222';
    style.color = '#EEE';
    style.textAlign = 'center';
    style.fontFamily = 'sans-serif';
    style.padding = '1.5em';
    style.zIndex = '9999999999';
    loader.textContent = 'Loading ...';

    return loader;
}

(function () {
    /**
     * Check and set a global guard variable.
     * If this content script is injected into the same page again,
     * it will do nothing next time.
     */
    if (window.hasRun) {
        return;
    }
    window.hasRun = true;
    console.log('Init webpage2kindle web browser extension');
    document.body.appendChild(createLoaderElement());

    const body = document.body.outerHTML;
    fetch(process.env.SYMFONY_ENDPOINT_URL!, {
        body: new URLSearchParams({
            "body": body,
            "url": window.location.toString(),
        }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        method: 'POST',
        mode: 'cors',
    })
        .then(response => response.json())
        .then(data => {
            console.log(data.pushToKindleUrl);
            browser.runtime.sendMessage({
                success: true,
                pushToKindleUrl: data.pushToKindleUrl,
                title: document.title,
                tabUrl: window.location.href,
            });
        })
        .catch((error) => {
            console.error('Error', error);
            browser.runtime.sendMessage({success: false});
        });
})();
