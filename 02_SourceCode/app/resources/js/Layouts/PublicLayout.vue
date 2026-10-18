<script setup>
import { ref, computed, watch } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import AppLogo from '@/Components/AppLogo.vue';
import Icon from '@/Components/Icon.vue';
import FlashToast from '@/Components/FlashToast.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import LocaleSwitch from '@/Components/LocaleSwitch.vue';
import AccountMenu from '@/Components/AccountMenu.vue';
import { useT } from '@/Composables/useT';
import { useLocaleRoute } from '@/Composables/useLocaleRoute';

const page = usePage();
const { t } = useT();
const lr = useLocaleRoute();
const mobileOpen = ref(false);

const nav = computed(() => [
    { key: 'sites', path: '/di-san' },
    { key: 'artifacts', path: '/hien-vat' },
    { key: 'figures', path: '/nhan-vat' },
    { key: 'timeline', path: '/dong-thoi-gian' },
    { key: 'map', path: '/ban-do' },
    { key: 'tours', path: '/tuyen-tham-quan' },
    { key: 'tags', path: '/the' },
    { key: 'search', path: '/tim-kiem' },
]);

const app = computed(() => page.props.app || {});
const currentPath = computed(() => (page.url || '').split('?')[0]);
const isActive = (path) => currentPath.value.startsWith(lr(path));

// Đóng menu di động sau mỗi lần điều hướng
router.on('navigate', () => (mobileOpen.value = false));
watch(mobileOpen, (v) => document.body.classList.toggle('overflow-hidden', v));
</script>

<template>
    <div class="flex min-h-screen flex-col">
        <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[1200] btn btn-primary">
            {{ t('common.skip_to_content') }}
        </a>

        <header class="sticky top-0 z-[1000] border-b border-border bg-bg/90 backdrop-blur supports-[backdrop-filter]:bg-bg/80">
            <div class="container-page flex h-16 items-center gap-3">
                <Link :href="lr('/')" class="shrink-0 rounded-lg" :aria-label="t('nav.home_logo')">
                    <AppLogo />
                </Link>

                <nav class="ml-4 hidden flex-1 items-center gap-1 xl:flex" :aria-label="t('nav.main')">
                    <Link
                        v-for="item in nav"
                        :key="item.key"
                        :href="lr(item.path)"
                        class="rounded-full px-3 py-1.5 text-sm font-medium transition-colors hover:bg-surface-2"
                        :class="isActive(item.path) ? 'bg-primary-soft text-primary' : ''"
                        :aria-current="isActive(item.path) ? 'page' : undefined"
                    >
                        {{ t(`nav.${item.key}`) }}
                    </Link>
                </nav>

                <div class="ml-auto flex items-center gap-1">
                    <Link :href="lr('/tim-kiem')" class="btn btn-ghost btn-icon xl:hidden" :aria-label="t('nav.search')">
                        <Icon name="search" />
                    </Link>
                    <LocaleSwitch />
                    <ThemeToggle />
                    <div class="hidden md:block"><AccountMenu /></div>
                    <button
                        type="button"
                        class="btn btn-ghost btn-icon xl:hidden"
                        :aria-expanded="mobileOpen"
                        aria-controls="mobile-nav"
                        :aria-label="mobileOpen ? t('common.close_menu') : t('common.open_menu')"
                        @click="mobileOpen = !mobileOpen"
                    >
                        <Icon :name="mobileOpen ? 'close' : 'menu'" />
                    </button>
                </div>
            </div>

            <div v-show="mobileOpen" id="mobile-nav" class="border-t border-border bg-bg xl:hidden">
                <nav class="container-page grid gap-1 py-3 sm:grid-cols-2" :aria-label="t('nav.main')">
                    <Link
                        v-for="item in nav"
                        :key="item.key"
                        :href="lr(item.path)"
                        class="rounded-lg px-3 py-2.5 font-medium hover:bg-surface-2"
                        :class="isActive(item.path) ? 'bg-primary-soft text-primary' : ''"
                        :aria-current="isActive(item.path) ? 'page' : undefined"
                    >
                        {{ t(`nav.${item.key}`) }}
                    </Link>
                    <Link :href="lr('/gioi-thieu')" class="rounded-lg px-3 py-2.5 font-medium hover:bg-surface-2">{{ t('nav.about') }}</Link>
                </nav>
                <div class="container-page border-t border-border py-3 md:hidden">
                    <AccountMenu />
                </div>
            </div>
        </header>

        <FlashToast />

        <main id="main" class="flex-1" tabindex="-1">
            <slot />
        </main>

        <footer class="mt-16 border-t border-border bg-surface">
            <div class="ornament"></div>
            <div class="container-page grid gap-8 py-10 md:grid-cols-3">
                <div>
                    <AppLogo />
                    <p class="mt-3 max-w-sm text-sm text-muted">{{ t('footer.tagline') }}</p>
                </div>
                <nav :aria-label="t('footer.explore')">
                    <h2 class="mb-3 font-body text-sm font-bold uppercase tracking-wider text-muted">{{ t('footer.explore') }}</h2>
                    <ul class="grid grid-cols-2 gap-2 text-sm">
                        <li v-for="item in nav" :key="item.key">
                            <Link :href="lr(item.path)" class="hover:text-primary hover:underline">{{ t(`nav.${item.key}`) }}</Link>
                        </li>
                        <li><Link :href="lr('/gioi-thieu')" class="hover:text-primary hover:underline">{{ t('nav.about') }}</Link></li>
                        <li><a href="/api/v1/openapi.json" class="hover:text-primary hover:underline">{{ t('footer.open_api') }}</a></li>
                    </ul>
                </nav>
                <div class="text-sm">
                    <h2 class="mb-3 font-body text-sm font-bold uppercase tracking-wider text-muted">{{ t('footer.academic') }}</h2>
                    <dl class="space-y-1">
                        <div><dt class="inline text-muted">{{ t('footer.course') }}:</dt> <dd class="inline font-medium">{{ app.course || 'Lập trình ứng dụng web trong du lịch 2' }}</dd></div>
                        <div><dt class="inline text-muted">{{ t('footer.course_code') }}:</dt> <dd class="inline font-medium">{{ app.courseCode || 'CSE703073' }}</dd></div>
                        <div><dt class="inline text-muted">{{ t('footer.team') }}:</dt> <dd class="inline font-medium">{{ app.team || 'DiSanSo' }}</dd></div>
                    </dl>
                    <p class="mt-3 rounded-lg bg-accent-soft px-3 py-2 text-xs font-semibold text-accent-strong">
                        {{ app.notice || t('footer.academic_notice') }}
                    </p>
                </div>
            </div>
            <div class="border-t border-border">
                <p class="container-page py-4 text-xs text-muted">
                    © 2026 {{ app.name || 'DiSanSo' }} · {{ t('footer.map_credit') }}
                </p>
            </div>
        </footer>
    </div>
</template>
