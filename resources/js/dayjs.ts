// dayjs
import dayjs from 'dayjs';
import calendar from 'dayjs/plugin/calendar';
import duration from 'dayjs/plugin/duration';
import relativeTime from 'dayjs/plugin/relativeTime';
import timezone from 'dayjs/plugin/timezone';
import updateLocale from 'dayjs/plugin/updateLocale';
import utc from 'dayjs/plugin/utc';
dayjs.extend(utc);
dayjs.extend(timezone);
dayjs.extend(calendar);
dayjs.extend(relativeTime);
dayjs.extend(duration);
dayjs.extend(updateLocale);

import 'dayjs/locale/en';
import 'dayjs/locale/es';
import 'dayjs/locale/pt';
import 'dayjs/locale/pt-br';

export default dayjs;
