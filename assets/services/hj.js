import Hotjar from '@hotjar/browser';

const siteId = 3572459;
const hotjarVersion = 6;

Hotjar.init(siteId, hotjarVersion, {
    debug: true
});