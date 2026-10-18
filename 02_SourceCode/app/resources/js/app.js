import axios from 'axios';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { i18nPlugin } from '@/Composables/useT';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';

// axios tự gửi X-XSRF-TOKEN từ cookie; header này giúp Laravel nhận diện AJAX
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });

// Layout mặc định theo thư mục trang; trang có thể tự khai báo `layout` để ghi đè
function layoutFor(name) {
    if (name.startsWith('Admin/')) return AdminLayout;
    if (name.startsWith('Auth/')) return AuthLayout;
    return PublicLayout;
}

createInertiaApp({
    title: (title) => (title ? `${title} – DiSanSo` : 'DiSanSo'),
    resolve: (name) => {
        const page = pages[`./Pages/${name}.vue`];
        if (!page) {
            throw new Error(`Inertia page not found: ${name}`);
        }
        if (page.default.layout === undefined) {
            page.default.layout = layoutFor(name);
        }
        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18nPlugin)
            .mount(el);
    },
    progress: {
        color: '#B7791F',
        showSpinner: false,
    },
});
