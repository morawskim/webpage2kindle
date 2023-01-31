# About

Send body content of webpage to external service for improve readability.

## Build

Install all required dependencies - `npm ci` (node 18 and npm are required)

Change version of extension in the `manifest.json` file.
Execute `SYMFONY_ENDPOINT_URL=https://your-symfony-app.example.com/web-extension npm run build` to build extension. 
The zip with extension will be generated in `dist/web-ext-artifacts`
Execute `npm run sign` to sign and publish extension.

## Test

Install all required dependencies - `npm ci` (node 18 and npm are required)

Execute `npm run build` to build extension.
Execute `npm run start:firefox` to test web extension with local API instance (127.0.0.1:4200)
