const saveOptions = () => {
    const url = document.getElementById('url').value;
    const configUrl = document.getElementById('config').value;
    browser.storage.sync.set({ url, configUrl });
};

const restoreOptions = async () => {
    const data = await browser.storage.sync.get({
        url: process.env.SYMFONY_ENDPOINT_URL,
        configUrl: '',
    });
    document.getElementById('url').value = data.url;
    document.getElementById('config').value = data.configUrl;
};

document.addEventListener('DOMContentLoaded', restoreOptions);
document.getElementById('save').addEventListener('click', saveOptions);
