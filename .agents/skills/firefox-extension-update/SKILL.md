---
name: firefox-extension-update
description: Bump the Firefox extension patch version in firefox-extension/manifest.json (semver) and update the matching download link in symfony-app/templates/homepage.html.twig. Use when releasing or updating the webpage2kindle Firefox extension.
license: MIT
compatibility: opencode
metadata:
  author: https://github.com/morawskim
  version: "0.1.0"
  domain: release
  triggers: Firefox, extension, manifest, semver, version bump
  role: specialist
  scope: implementation
  output-format: code
---

# Update firefox extension

Release workflow for the webpage2kindle Firefox extension.

## Core workflow

At file `firefox-extension/manifest.json` which is JSON file increase a verison of extension stored in key "version".
Increase only patch version, because this extension use semver.

Next in file `symfony-app/templates/homepage.html.twig` there will be a link to download firefox extension.
Please update the link to asset using the same version which you set in json manifest file.
