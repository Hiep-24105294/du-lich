<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useT } from '@/Composables/useT';

// Ba mức diễn giải: 1 phổ thông, 2 chuyên sâu, 3 học thuật
const props = defineProps({
    current: { type: Number, default: 1 },
    available: { type: Array, default: () => [1] },
});
const page = usePage();
const { t } = useT();

const basePath = computed(() => (page.url || '').split('?')[0]);
const levels = [1, 2, 3];
const isAvailable = (lvl) => props.available.map(Number).includes(lvl);
</script>

<template>
    <nav :aria-label="t('depth.label')">
        <ul class="flex flex-wrap gap-2">
            <li v-for="lvl in levels" :key="lvl">
                <Link
                    v-if="isAvailable(lvl)"
                    :href="`${basePath}?depth=${lvl}`"
                    class="tab inline-flex flex-col items-start leading-tight"
                    :aria-current="current === lvl ? 'page' : undefined"
                    preserve-scroll
                >
                    <span>{{ t(`depth.${lvl}`) }}</span>
                    <span class="text-[11px] font-normal opacity-80">{{ t(`depth.${lvl}_hint`) }}</span>
                </Link>
                <span
                    v-else
                    class="tab inline-flex flex-col items-start leading-tight"
                    aria-disabled="true"
                    :title="t('depth.unavailable')"
                >
                    <span>{{ t(`depth.${lvl}`) }}</span>
                    <span class="text-[11px] font-normal opacity-80">{{ t('depth.unavailable') }}</span>
                </span>
            </li>
        </ul>
    </nav>
</template>
