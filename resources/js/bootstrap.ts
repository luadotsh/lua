import axios from 'axios';

/**
 * Browser wiring: axios on the window and the Echo connection. Neither exists
 * on the server, and both throw there — the SSR renderer imports this module
 * exactly like the browser does.
 */
if (typeof window !== 'undefined') {
    window.axios = axios;
    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    // Dynamic so the WebSocket client is never even loaded server-side.
    import('./echo');
}
