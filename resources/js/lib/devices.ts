const deviceIcons: Record<string, string> = {
    desktop: 'desktop.png',
    laptop: 'desktop.png',
    mobile: 'mobile.png',
    tablet: 'tablet.png',
};

export const deviceIconUrl = (name: string): string | null => {
    const icon = deviceIcons[name.trim().toLowerCase()];

    return icon ? `/images/devices/${icon}` : null;
};
