<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLogo from '@/Components/AppLogo.vue';
import FlashToast from '@/Components/FlashToast.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import LocaleSwitch from '@/Components/LocaleSwitch.vue';
import { useT } from '@/Composables/useT';
import { useLocaleRoute } from '@/Composables/useLocaleRoute';

const page = usePage();
const { t } = useT();
const lr = useLocaleRoute();
const app = computed(() => page.props.app || {});
</script>

<template>
    <div class="flex min-h-screen flex-col bg-bg">
        <header class="container-page flex h-16 items-center justify-between">
            <Link :href="lr('/')" class="rounded-lg" :aria-label="t('nav.home')"><AppLogo /></Link>
            <div class="flex items-center gap-1">
                <LocaleSwitch />
                <ThemeToggle />
            </div>
        </header>
        <FlashToast />
        <main id="main" class="flex flex-1 items-start justify-center px-4 py-8 md:items-center">
            <div class="w-full max-w-md">
                <slot />
            </div>
        </main>
        <footer class="py-6 text-center text-xs text-muted">
            {{ app.course || 'Lập trình ứng dụng web trong du lịch 2' }} · {{ app.courseCode || 'CSE703073' }} · {{ app.team || 'DiSanSo' }}<br />
            {{ app.notice || t('footer.academic_notice') }}
        </footer>
    </div>
</template>
