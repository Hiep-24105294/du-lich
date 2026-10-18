import axios from 'axios';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { i18nPlugin } from '@/Composables/useT';

// axios tự gửi X-XSRF-TOKEN từ cookie; header này giúp Laravel nhận diện AJAX
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Nạp trang theo nhu cầu: mỗi trang là một chunk riêng, bundle đầu vào chỉ còn khung ứng dụng
const pages = import.meta.glob('./Pages/**/*.vue');

// Layout mặc định theo thư mục trang (cũng nạp động); trang có thể tự khai báo `layout` để ghi đè
function layoutFor(name) {
    if (name.startsWith('Admin/')) return import('@/Layouts/AdminLayout.vue');
    if (name.startsWith('Auth/')) return import('@/Layouts/AuthLayout.vue');
    return import('@/Layouts/PublicLayout.vue');
}

createInertiaApp({
    title: (title) => (title ? `${title} – DiSanSo` : 'DiSanSo'),
    resolve: async (name) => {
        const loader = pages[`./Pages/${name}.vue`];
        if (!loader) {
            throw new Error(`Inertia page not found: ${name}`);
        }
        const [page, layout] = await Promise.all([loader(), layoutFor(name)]);
        if (page.default.layout === undefined) {
            page.default.layout = layout.default;
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
