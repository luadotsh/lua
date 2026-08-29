const osIcons: Record<string, string> = {
    android: 'android.png',
    'chrome os': 'chrome_os.png',
    fedora: 'fedora.png',
    'fire os': 'fire_os.png',
    freebsd: 'freebsd.png',
    'gnu/linux': 'gnu_linux.png',
    harmonyos: 'harmony_os.png',
    ios: 'ios.png',
    ipados: 'ipad_os.png',
    kaios: 'kai_os.png',
    linux: 'gnu_linux.png',
    'mac os': 'mac.png',
    macos: 'mac.png',
    playstation: 'playstation.png',
    tizen: 'tizen.png',
    ubuntu: 'ubuntu.png',
    windows: 'windows.png',
};

export const osIconUrl = (name: string): string =>
    `/images/os/${osIcons[name.trim().toLowerCase()] ?? 'fallback.svg'}`;
