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
    }
};
