import dayjs from "@/dayjs";

type DateInput = string | number | Date | null | undefined;

export default {
    getUserTimezone() {
        return Intl.DateTimeFormat().resolvedOptions().timeZone;
    },

    formatDate(date: DateInput) {
        return dayjs(date).format("MMM D, YYYY");
    },

    formatDateTime(date: DateInput) {
        return dayjs.utc(date).format("MMM D, YYYY h:mm A");
    },

    formatDateTimeForApi(date: DateInput) {
        return dayjs.utc(date).tz("utc").format("YYYY-MM-DD HH:mm:ss");
    },

    diffForHumans(date: DateInput) {
        // Convert UTC date to local timezone
        const localDate = dayjs.utc(date).local();

        // Return the human-readable difference
        return dayjs().to(localDate);
    },
};
