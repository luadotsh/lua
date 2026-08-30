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

    /**
     * The viewer's timezone, short form — "GMT-3", "PST". Derived from Intl
     * rather than dayjs's `z`, which needs the advancedFormat plugin.
     */
    getTimezoneAbbr(): string {
        const parts = new Intl.DateTimeFormat(undefined, {
            timeZoneName: "short",
        }).formatToParts(new Date());

        return parts.find((part) => part.type === "timeZoneName")?.value ?? "";
    },

    diffForHumans(date: DateInput) {
        // Convert UTC date to local timezone
        const localDate = dayjs.utc(date).local();

        // Return the human-readable difference
        return dayjs().to(localDate);
    },
};
