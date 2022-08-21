const path = require('path');
const CopyPlugin = require('copy-webpack-plugin');
const webpack = require('webpack');

module.exports = {
    mode: "production",
    entry: {
        background: path.resolve(__dirname, "background.ts"),
        contentScript: path.resolve(__dirname, "contentScript.ts"),
    },
    output: {
        path: path.join(__dirname, "dist"),
        filename: "[name].js",
        clean: true,
    },
    resolve: {
        extensions: [".ts", ".js"],
    },
    module: {
        rules: [
            {
                test: /\.tsx?$/,
                loader: "ts-loader",
                exclude: /node_modules/,
            },
        ],
    },
    plugins: [
        new CopyPlugin({
            patterns: [
                {from: "icons/", to: "icons/", },
                {from: "manifest.json", to: ".", },
            ]
        }),
        new webpack.EnvironmentPlugin({
            SYMFONY_ENDPOINT_URL: 'http://127.0.0.1:4200/web-extension',
        }),
    ],
};
