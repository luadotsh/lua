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

    diffForHumans(date) {
        let localDate = dayjs
            .utc(date)
            .format("YYYY-MM-DD HH:mm:ss");
        return dayjs().to(dayjs(localDate));
    }
};
