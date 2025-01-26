import dayjs from "@/dayjs";

export default {
    getUserTimezone() {
        return Intl.DateTimeFormat().resolvedOptions().timeZone;
    },

    formatDate(date) {
        return dayjs(date).format("MMM D, YYYY");
    },

    formatDateTime(date) {
        return dayjs.utc(date).format("MMM D, YYYY h:mm A");
    },

    formatDateTimeForApi(date) {
        return dayjs.utc(date).tz("utc").format("YYYY-MM-DD HH:mm:ss");
    },

    diffForHumans(date) {
        // Convert UTC date to local timezone
        const localDate = dayjs.utc(date).local();

        // Return the human-readable difference
        return dayjs().to(localDate);
    },
};
