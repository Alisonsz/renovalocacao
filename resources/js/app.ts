import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';
import { route } from '@/lib/route';

const appName = import.meta.env.VITE_APP_NAME || 'Renova Locação';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
                return null;
            // Public pages use no layout wrapper (self-contained)
            case name === 'Home':
            case name.startsWith('Products/'):
            case name.startsWith('Booking/'):
                return null;
            // Admin pages use no layout (AdminLayout is embedded per-page)
            case name.startsWith('Admin/'):
                return null;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];
            default:
                return AppLayout;
        }
    },
    progress: {
        color: '#3B82F6',
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin);
        app.config.globalProperties.route = route;
        app.config.globalProperties.$route = route;
        (window as unknown as Record<string, unknown>).route = route;
        app.mount(el);
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();
