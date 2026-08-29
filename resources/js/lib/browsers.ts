const browserIcons: Record<string, string> = {
    brave: 'chromium.svg',
    chrome: 'chrome.svg',
    'chrome mobile': 'chrome.svg',
    chromium: 'chromium.svg',
    curl: 'curl.svg',
    duckduckgo: 'duckduckgo.svg',
    ecosia: 'ecosia.png',
    edge: 'edge.svg',
    firefox: 'firefox.svg',
    'huawei browser': 'huawei.png',
    'mobile safari': 'safari.png',
    opera: 'opera.svg',
    'opera gx': 'opera.svg',
    'opera mini': 'opera.svg',
    'qq browser': 'qq.png',
    safari: 'safari.png',
    'samsung internet': 'samsung-internet.svg',
    'uc browser': 'uc.svg',
    vivaldi: 'vivaldi.svg',
    yandex: 'yandex.png',
    'yandex browser': 'yandex.png',
};

export const browserIconUrl = (name: string): string =>
    `/images/browsers/${browserIcons[name.trim().toLowerCase()] ?? 'fallback.svg'}`;
