<script setup>
import { useT } from '@/Composables/useT';
import { useFormat } from '@/Composables/useFormat';
import DepthTabs from '@/Components/DepthTabs.vue';

// Khối diễn giải dùng chung cho di sản, hiện vật, nhân vật (body_html đã lọc ở backend)
defineProps({
    interpretation: { type: Object, default: null },
    depth: { type: Number, default: 1 },
    availableDepths: { type: Array, default: () => [1] },
});
const { t } = useT();
const { date } = useFormat();
</script>

<template>
    <section :aria-label="t('sites.interpretation')">
        <DepthTabs :current="depth" :available="availableDepths" class="mb-6" />
        <template v-if="interpretation">
            <h2 v-if="interpretation.title" class="mb-3 text-2xl font-bold">{{ interpretation.title }}</h2>
            <p v-if="interpretation.summary" class="mb-5 text-lg text-muted">{{ interpretation.summary }}</p>
            <div class="prose-heritage" v-html="interpretation.body_html"></div>
            <div v-if="interpretation.sources?.length" class="mt-8 rounded-xl bg-surface-2 p-4">
                <h3 class="mb-2 font-body text-sm font-bold uppercase tracking-wider text-muted">{{ t('common.sources') }}</h3>
                <ol class="list-decimal space-y-1 pl-5 text-sm">
                    <li v-for="(src, i) in interpretation.sources" :key="i" class="break-words">{{ src }}</li>
                </ol>
            </div>
            <p class="mt-4 text-xs text-muted">
                <span v-if="interpretation.version">{{ t('common.version') }} {{ interpretation.version }}</span>
                <span v-if="interpretation.updated_at"> · {{ t('common.updated_at') }} {{ date(interpretation.updated_at) }}</span>
            </p>
        </template>
        <p v-else class="text-muted">{{ t('sites.no_interpretation') }}</p>
    </section>
</template>
