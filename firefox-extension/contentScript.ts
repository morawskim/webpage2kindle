import browser from "webextension-polyfill";
import webExtensionManifest from './manifest.json';

declare global {
    interface Window {
        hasRun: boolean;
    }
}

interface ConfigItem {
    selector: string
}


class Configuration {
    constructor(private data: {[key: string]: ConfigItem}) {
    }

    getSelectorForDomain(domain: string) {
        if (this.hasSelectorForDomain(domain)) {
            return this.data[domain].selector || '';
        }

        return '';
    }

    hasSelectorForDomain(domain: string) {
        return this.data.hasOwnProperty(domain);
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

function getConfiguration(configUrl: string): Promise<Configuration> {
    return fetch(configUrl)
        .then(response => {
            return response.json();
        }).then(data => {
            return new Configuration(data);
        })
        .catch()
}

function toDataURL(image: HTMLImageElement): string {
    const canvas = document.createElement('canvas');

    canvas.width = image.naturalWidth;
    canvas.height = image.naturalHeight;

    canvas.getContext('2d')?.drawImage(image, 0, 0);

    return canvas.toDataURL();
}

function convertImagesToDataUrl(nodes: NodeListOf<HTMLImageElement>) {
    let promises: Promise<void>[] = [];

    nodes.forEach(el => {
        if (el.complete) {
            el.src = toDataURL(el);
        } else {
            promises.push(new Promise((resolve) => {
                el.onload = function () {
                    el.src = toDataURL(el);
                    resolve();
                }
            }))
        }
    });

    return Promise.all(promises);
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
    console.log(`Init webpage2kindle web browser extension (version: ${webExtensionManifest.version})`);
    document.body.appendChild(createLoaderElement());

    // at the moment pushtokindle not support img tags with dataurl
    // convertImagesToDataUrl(document.querySelectorAll('img'))
    Promise.resolve()
        .then(async function () {
            const record =  await browser.storage.sync.get({
                url: process.env.SYMFONY_ENDPOINT_URL!,
                configUrl: '',
            });

            return Promise.resolve<[string, string]>([record["url"] as string, record["configUrl"] as string]);
        })
        .then(async function ([url, configUrl]) {
            console.log(`Url to webservice "${url}"`);
            console.log(`Url to JSON config document "${configUrl}"`);
            let body = document.body.outerHTML!;

            if (configUrl) {
                const domain = location.hostname;
                const configuration = await getConfiguration(configUrl);
                if (configuration.hasSelectorForDomain(domain)) {
                    console.log(`Use selector from JSON config for domain ${domain}`);
                    body = document.querySelector(configuration.getSelectorForDomain(domain))?.outerHTML || body;
                }
            }

            fetch(url, {
                body: new URLSearchParams({
                    "body": body,
                    "url": window.location.toString(),
                    "title": document.title,
                }),
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json',
                    'X-Extension-Version': webExtensionManifest.version,
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
                    console.error('Error', error, process.env.SYMFONY_ENDPOINT_URL);
                    browser.runtime.sendMessage({
                        success: false,
                        title: document.title,
                    });
                });
        });
})();
