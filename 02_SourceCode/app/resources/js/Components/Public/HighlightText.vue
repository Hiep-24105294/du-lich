<script setup>
import { computed } from 'vue';

// Tô sáng các từ khớp mà không dùng v-html (an toàn XSS)
const props = defineProps({
    text: { type: String, default: '' },
    terms: { type: Array, default: () => [] },
});

const escapeRe = (s) => s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

const parts = computed(() => {
    const words = props.terms.filter((w) => w && w.length > 1).map(escapeRe);
    if (!props.text || !words.length) return [{ text: props.text || '', hit: false }];
    const splitter = new RegExp(`(${words.join('|')})`, 'giu');
    const matcher = new RegExp(`^(?:${words.join('|')})$`, 'iu');
    return props.text.split(splitter).filter(Boolean).map((chunk) => ({ text: chunk, hit: matcher.test(chunk) }));
});
</script>

<template>
    <span><template v-for="(p, i) in parts" :key="i"><mark v-if="p.hit" class="rounded bg-accent-soft px-0.5 text-text">{{ p.text }}</mark><template v-else>{{ p.text }}</template></template></span>
</template>
