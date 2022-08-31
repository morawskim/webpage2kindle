module.exports = {
    ignoreFiles: [
        'web-ext-artifacts/',
        '.gitignore',
        '.web-extension-id',
    ],
    sourceDir: "./dist/",
    artifactsDir: "./dist/web-ext-artifacts",
    sign: {
        channel: "unlisted",
        id: "{d8ade355-3d9f-444f-abf8-95f6e43837c3}",
    }
};
