import browser from "webextension-polyfill";

declare global {
    interface Window {
        hasRun: boolean;
    }
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

    const body = document.body.outerHTML;
    // todo mmo remove hardcoded value when webpack will be added
    fetch('http://127.0.0.1:4200/web-extension', {
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
            browser.runtime.sendMessage({success: true, pushToKindleUrl: data.pushToKindleUrl});
        })
        .catch((error) => {
            console.error('Error', error);
            browser.runtime.sendMessage({success: false});
        });
})();
