import {removeCssInlineAndExternalLinks} from "../helpers";

test('test remove inline and external CSS', () => {
    const body = `
<!doctype html>
<html data-static-assets="https://a0.awsstatic.com" class="aws-lng-en_US" data-aws-assets="https://a0.awsstatic.com" data-js-version="1.0.682" data-css-version="1.0.538" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Announcing Amazon EKS Capabilities for workload orchestration and cloud resource management | AWS News Blog</title>
    <script src="https://a0.awsstatic.com/libra/1.0.599/csp/csp-report.js"></script>
    <meta name="robots" content="max-image-preview:large">
    <link rel="dns-prefetch" href="//a0.awsstatic.com">
    <link rel="icon" type="image/ico" href="https://a0.awsstatic.com/main/images/site/fav/favicon.ico">
    <link rel="shortcut icon" type="image/ico" href="https://a0.awsstatic.com/main/images/site/fav/favicon.ico">
    <link rel="apple-touch-icon" sizes="57x57" href="https://a0.awsstatic.com/main/images/site/touch-icon-iphone-114-smile.png">
    <link rel="apple-touch-icon" sizes="72x72" href="https://a0.awsstatic.com/main/images/site/touch-icon-ipad-144-smile.png">
    <link rel="apple-touch-icon" sizes="114x114" href="https://a0.awsstatic.com/main/images/site/touch-icon-iphone-114-smile.png">
    <link rel="apple-touch-icon" sizes="144x144" href="https://a0.awsstatic.com/main/images/site/touch-icon-ipad-144-smile.png">
    <link rel="home" type="application/rss+xml" title="RSS 2.0 Feed" href="https://aws.amazon.com/blogs/aws/feed/">
    <link rel="stylesheet" href="https://a0.awsstatic.com/libra-css/css/1.0.509/style-awsm-base.css">
    <link rel="stylesheet" href="https://a0.awsstatic.com/awsm-core/0.1.4/bundle/head.css">
    <link rel="stylesheet" href="https://a0.awsstatic.com/eb-csr/2.0.14/orchestrate.css">
    <meta name="robots" content="index, follow">
    <script type="application/json" id="aws-page-settings">
        {"blogPageTitle":"Announcing Amazon EKS Capabilities for workload orchestration and cloud resource management","currentLanguage":"en-US","supportedLanguages":["ar","de","en","es","fr","id","it","jp","ko","pt","ru","th","tr","vi","cn","tw"],"currentStage":"Prod","libraCSSPath":"https://a0.awsstatic.com/libra-css/css/1.0.509","requireBaseUrl":"https://a0.awsstatic.com","requirePackages":[{"name":"libra","location":"libra/1.0.599"}],"requirePaths":{"aws-blog":"https://a0.awsstatic.com/aws-blog/1.0.89/js","directories":"https://a0.awsstatic.com/libra/1.0.599/directories","librastandardlib":"https://a0.awsstatic.com/libra/1.0.599/librastandardlib","scripts":"https://a0.awsstatic.com/libra/1.0.599/v1-polyfills/scripts"}}  </script>
    <script type="esms-options">{"noLoadEventRetriggers": true, "nonce":"EEhzVFwut9gPBTzBJvlz+P7TsnJ88nFXPWPmGwf2+H8="}</script>
    <script type="importmap" data-head-renderable="true">{"imports":{"react":"https://a0.awsstatic.com/eb-csr/2.0.14/react/react.js","react/jsx-runtime":"https://a0.awsstatic.com/eb-csr/2.0.14/react/jsx-runtime.js","react-dom":"https://a0.awsstatic.com/eb-csr/2.0.14/react/react-dom.js","react-dom/server":"https://a0.awsstatic.com/eb-csr/2.0.14/react/server-browser.js","react-dom-server-browser":"https://a0.awsstatic.com/eb-csr/2.0.14/react/react-dom-server-browser.js","sanitize-html":"https://a0.awsstatic.com/eb-csr/2.0.14/sanitize-html/index.js","video.js":"https://a0.awsstatic.com/eb-csr/2.0.14/videojs/video.js","videojs-event-tracking":"https://a0.awsstatic.com/eb-csr/2.0.14/videojs/videojs-event-tracking.js","videojs-hotkeys":"https://a0.awsstatic.com/eb-csr/2.0.14/videojs/videojs-hotkeys.js","@amzn/awsmcc":"https://a0.awsstatic.com/awsmcc/1.0.0/bundle/index.js"}}</script>
    <style id="global-styles-inline-css" type="text/css">
        body{--wp--preset--color--black: #000000;--wp--preset--color--cyan-bluish-gray: #abb8c3;--wp--preset--color--white: #ffffff;--wp--preset--color--pale-pink: #f78da7;--wp--preset--color--vivid-red: #cf2e2e;--wp--preset--color--luminous-vivid-orange: #ff6900;--wp--preset--color--luminous-vivid-amber: #fcb900;--wp--preset--color--light-green-cyan: #7bdcb5;--wp--preset--color--vivid-green-cyan: #00d084;--wp--preset--color--pale-cyan-blue: #8ed1fc;--wp--preset--color--vivid-cyan-blue: #0693e3;--wp--preset--color--vivid-purple: #9b51e0;--wp--preset--gradient--vivid-cyan-blue-to-vivid-purple: linear-gradient(135deg,rgba(6,147,227,1) 0%,rgb(155,81,224) 100%);--wp--preset--gradient--light-green-cyan-to-vivid-green-cyan: linear-gradient(135deg,rgb(122,220,180) 0%,rgb(0,208,130) 100%);--wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange: linear-gradient(135deg,rgba(252,185,0,1) 0%,rgba(255,105,0,1) 100%);--wp--preset--gradient--luminous-vivid-orange-to-vivid-red: linear-gradient(135deg,rgba(255,105,0,1) 0%,rgb(207,46,46) 100%);--wp--preset--gradient--very-light-gray-to-cyan-bluish-gray: linear-gradient(135deg,rgb(238,238,238) 0%,rgb(169,184,195) 100%);--wp--preset--gradient--cool-to-warm-spectrum: linear-gradient(135deg,rgb(74,234,220) 0%,rgb(151,120,209) 20%,rgb(207,42,186) 40%,rgb(238,44,130) 60%,rgb(251,105,98) 80%,rgb(254,248,76) 100%);--wp--preset--gradient--blush-light-purple: linear-gradient(135deg,rgb(255,206,236) 0%,rgb(152,150,240) 100%);--wp--preset--gradient--blush-bordeaux: linear-gradient(135deg,rgb(254,205,165) 0%,rgb(254,45,45) 50%,rgb(107,0,62) 100%);--wp--preset--gradient--luminous-dusk: linear-gradient(135deg,rgb(255,203,112) 0%,rgb(199,81,192) 50%,rgb(65,88,208) 100%);--wp--preset--gradient--pale-ocean: linear-gradient(135deg,rgb(255,245,203) 0%,rgb(182,227,212) 50%,rgb(51,167,181) 100%);--wp--preset--gradient--electric-grass: linear-gradient(135deg,rgb(202,248,128) 0%,rgb(113,206,126) 100%);--wp--preset--gradient--midnight: linear-gradient(135deg,rgb(2,3,129) 0%,rgb(40,116,252) 100%);--wp--preset--duotone--dark-grayscale: url('#wp-duotone-dark-grayscale');--wp--preset--duotone--grayscale: url('#wp-duotone-grayscale');--wp--preset--duotone--purple-yellow: url('#wp-duotone-purple-yellow');--wp--preset--duotone--blue-red: url('#wp-duotone-blue-red');--wp--preset--duotone--midnight: url('#wp-duotone-midnight');--wp--preset--duotone--magenta-yellow: url('#wp-duotone-magenta-yellow');--wp--preset--duotone--purple-green: url('#wp-duotone-purple-green');--wp--preset--duotone--blue-orange: url('#wp-duotone-blue-orange');--wp--preset--font-size--small: 13px;--wp--preset--font-size--medium: 20px;--wp--preset--font-size--large: 36px;--wp--preset--font-size--x-large: 42px;}</style>
                </div>
            </div>
        </div>
    </div>
</header>
<div id="aws-page-end"></div>
<script src="https://a0.awsstatic.com/aws-blog/1.0.89/js/vendor/prism.js" data-default-language="markup"></script>
FOOOOO
</body>
</html>
`
    const expected = `
<!doctype html>
<html data-static-assets="https://a0.awsstatic.com" class="aws-lng-en_US" data-aws-assets="https://a0.awsstatic.com" data-js-version="1.0.682" data-css-version="1.0.538" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Announcing Amazon EKS Capabilities for workload orchestration and cloud resource management | AWS News Blog</title>
    <script src="https://a0.awsstatic.com/libra/1.0.599/csp/csp-report.js"></script>
    <meta name="robots" content="max-image-preview:large">
    <link rel="dns-prefetch" href="//a0.awsstatic.com">
    <link rel="icon" type="image/ico" href="https://a0.awsstatic.com/main/images/site/fav/favicon.ico">
    <link rel="shortcut icon" type="image/ico" href="https://a0.awsstatic.com/main/images/site/fav/favicon.ico">
    <link rel="apple-touch-icon" sizes="57x57" href="https://a0.awsstatic.com/main/images/site/touch-icon-iphone-114-smile.png">
    <link rel="apple-touch-icon" sizes="72x72" href="https://a0.awsstatic.com/main/images/site/touch-icon-ipad-144-smile.png">
    <link rel="apple-touch-icon" sizes="114x114" href="https://a0.awsstatic.com/main/images/site/touch-icon-iphone-114-smile.png">
    <link rel="apple-touch-icon" sizes="144x144" href="https://a0.awsstatic.com/main/images/site/touch-icon-ipad-144-smile.png">
    <link rel="home" type="application/rss+xml" title="RSS 2.0 Feed" href="https://aws.amazon.com/blogs/aws/feed/">
    
    
    
    <meta name="robots" content="index, follow">
    <script type="application/json" id="aws-page-settings">
        {"blogPageTitle":"Announcing Amazon EKS Capabilities for workload orchestration and cloud resource management","currentLanguage":"en-US","supportedLanguages":["ar","de","en","es","fr","id","it","jp","ko","pt","ru","th","tr","vi","cn","tw"],"currentStage":"Prod","libraCSSPath":"https://a0.awsstatic.com/libra-css/css/1.0.509","requireBaseUrl":"https://a0.awsstatic.com","requirePackages":[{"name":"libra","location":"libra/1.0.599"}],"requirePaths":{"aws-blog":"https://a0.awsstatic.com/aws-blog/1.0.89/js","directories":"https://a0.awsstatic.com/libra/1.0.599/directories","librastandardlib":"https://a0.awsstatic.com/libra/1.0.599/librastandardlib","scripts":"https://a0.awsstatic.com/libra/1.0.599/v1-polyfills/scripts"}}  </script>
    <script type="esms-options">{"noLoadEventRetriggers": true, "nonce":"EEhzVFwut9gPBTzBJvlz+P7TsnJ88nFXPWPmGwf2+H8="}</script>
    <script type="importmap" data-head-renderable="true">{"imports":{"react":"https://a0.awsstatic.com/eb-csr/2.0.14/react/react.js","react/jsx-runtime":"https://a0.awsstatic.com/eb-csr/2.0.14/react/jsx-runtime.js","react-dom":"https://a0.awsstatic.com/eb-csr/2.0.14/react/react-dom.js","react-dom/server":"https://a0.awsstatic.com/eb-csr/2.0.14/react/server-browser.js","react-dom-server-browser":"https://a0.awsstatic.com/eb-csr/2.0.14/react/react-dom-server-browser.js","sanitize-html":"https://a0.awsstatic.com/eb-csr/2.0.14/sanitize-html/index.js","video.js":"https://a0.awsstatic.com/eb-csr/2.0.14/videojs/video.js","videojs-event-tracking":"https://a0.awsstatic.com/eb-csr/2.0.14/videojs/videojs-event-tracking.js","videojs-hotkeys":"https://a0.awsstatic.com/eb-csr/2.0.14/videojs/videojs-hotkeys.js","@amzn/awsmcc":"https://a0.awsstatic.com/awsmcc/1.0.0/bundle/index.js"}}</script>
    
                </div>
            </div>
        </div>
    </div>
</header>
<div id="aws-page-end"></div>
<script src="https://a0.awsstatic.com/aws-blog/1.0.89/js/vendor/prism.js" data-default-language="markup"></script>
FOOOOO
</body>
</html>
`;
    expect(removeCssInlineAndExternalLinks(body)).toEqual(expected);
});
