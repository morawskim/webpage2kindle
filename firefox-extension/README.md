# About

Send body content of webpage to external service for improve readability.

⚠️ **WARNING** ⚠️
At the moment due to [bug in web-ext](https://github.com/mozilla/web-ext/issues/2546) you need to edit a file `node_modules/web-ext/lib/program.js` 
and manually set `type` for `channel`:

```diff
--- firefox-extension/node_modules/web-ext/lib/program.js       2022-12-03 12:46:42.989376501 +0100
+++ ./program.js        2022-12-03 12:46:27.336686774 +0100
@@ -466,7 +466,8 @@
       type: 'number'
     },
     channel: {
-      describe: 'The channel for which to sign the addon. Either ' + "'listed' or 'unlisted'"
+      describe: 'The channel for which to sign the addon. Either ' + "'listed' or 'unlisted'",
+      type: 'string'
     },
     'amo-metadata': {
       describe: 'Path to a JSON file containing an object with metadata ' + 'to be passed to the API. ' + 'See https://addons-server.readthedocs.io' + '/en/latest/topics/api/addons.html for details. ' + 'Only used with `use-submission-api`',
@@ -661,4 +662,4 @@
     ...runOptions
   });
 }
```

Otherwise, you will receive:
> WebExtError: Option: channel was defined without a type.

## Build

Install all required dependencies - `npm ci` (node 18 and npm are required)

Execute `SYMFONY_ENDPOINT_URL=https://your-symfony-app.example.com/web-extension npm run build` to build extension. 
The zip with extension will be generated in `dist/web-ext-artifacts`

## Test

Install all required dependencies - `npm ci` (node 18 and npm are required)

Execute `npm run build` to build extension.
Execute `npm run start:firefox` to test web extension with local API instance (127.0.0.1:4200)
