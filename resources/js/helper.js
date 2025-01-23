import { usePage } from "@inertiajs/vue3";
import colorLib from "@kurkle/color";

export default {
    kFormatter(num) {
        const absNum = Math.abs(num);
        const sign = Math.sign(num);

        if (absNum >= 1000000000) {
            return sign * (absNum / 1000000000).toFixed(1) + "B";
        }
        if (absNum >= 1000000) {
            return sign * (absNum / 1000000).toFixed(1) + "M";
        }
        if (absNum >= 1000) {
            return sign * (absNum / 1000).toFixed(1) + "k";
        }
        return sign * absNum;
    },

    transparentize(value, opacity) {
        var alpha = opacity === undefined ? 0.5 : 1 - opacity;
        return colorLib(value).alpha(alpha).rgbString();
    },

    lowerCase(value) {
        if (!value) return "";
        return value.toLowerCase();
    },

    calcTotal(data) {
        if (Object.keys(data).length === 0) return;

        return data.reduce((a, b) => {
            return a + b.y;
        }, 0);
    },

    calcPercentage(total, value) {
        if (total === 0) return 0;

        return ((value / total) * 100).toFixed(0);
    },

    copyToClipboard(url, message = null) {
        const clipboardData =
            event.clipboardData ||
            window.clipboardData ||
            event.originalEvent?.clipboardData ||
            navigator.clipboard;

        clipboardData.writeText(url);

        // Show flash message
        if (message) {
            usePage().props.flash.bannerStyle = "success";
            usePage().props.flash.banner = message;
        }
    },

    /**
     * Function to get the favicon URL of a given website or return a fallback image if it fails.
     *
     * @param {string} url - The website URL.
     * @param {string} fallbackUrl - The URL of the fallback image if the favicon can't be loaded.
     * @returns {Promise<string>} - A promise that resolves to the favicon URL or the fallback image URL.
     */
    async favicon(url) {
        const faviconUrl = `https://t1.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=https://${url}&size=128`;

        try {
            // Check if the favicon URL is valid by trying to fetch it
            const response = await fetch(faviconUrl);
            if (!response.ok) {
                throw new Error("Favicon not found");
            }

            // If the favicon loads successfully, return the favicon URL
            return faviconUrl;
        } catch (error) {
            // If there's an error (e.g., favicon not found), return the fallback image URL
            return "/images/websites/favicon.png";
        }
    },

    getYouTubeVideoIdFromUrl(url) {
        const regExp =
            /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
        const match = url.match(regExp);
        return match && match[2].length === 11 ? match[2] : undefined;
    },
};
