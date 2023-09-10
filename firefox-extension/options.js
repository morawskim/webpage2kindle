const saveOptions = () => {
    const url = document.getElementById('url').value;
    browser.storage.sync.set({ url });
};

const restoreOptions = async () => {
    const data = await browser.storage.sync.get({ url: process.env.SYMFONY_ENDPOINT_URL });
    document.getElementById('url').value = data.url;
};

document.addEventListener('DOMContentLoaded', restoreOptions);
document.getElementById('save').addEventListener('click', saveOptions);
