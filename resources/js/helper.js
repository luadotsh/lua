import { usePage } from "@inertiajs/vue3";

export default {
    kFormatter(num) {
        return Math.abs(num) > 999
            ? Math.sign(num) * (Math.abs(num) / 1000).toFixed(1) + "k"
            : Math.sign(num) * Math.abs(num);
    },

    lowerCase(value) {
        if (!value) return "";
        return value.toLowerCase();
    },

    copyToClipboard(url, message = null) {
        const clipboardData =
            event.clipboardData ||
            window.clipboardData ||
            event.originalEvent?.clipboardData ||
            navigator.clipboard;

        clipboardData.writeText(url);

        // Show flash message
        if(message) {
            usePage().props.flash.bannerStyle = "success";
            usePage().props.flash.banner = message;
        }
    },

    getYouTubeVideoIdFromUrl(url) {
        const regExp =
            /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
        const match = url.match(regExp);
        return match && match[2].length === 11 ? match[2] : undefined;
    },
};
