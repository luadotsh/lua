import type { AxiosInstance } from 'axios';
import type { Auth } from '@/types/auth';

declare global {
    interface Window {
        axios: AxiosInstance;
    }
    const axios: AxiosInstance;
}

declare module 'vite/client' {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME: string;
        [key: string]: string | boolean | undefined;
    }

    interface ImportMeta {
        readonly env: ImportMetaEnv;
        readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
    }
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            flash: { banner?: string; bannerStyle?: string };
            sidebarOpen: boolean;
            env: string;
            locale: string;
            [key: string]: unknown;
        };
    }
}
