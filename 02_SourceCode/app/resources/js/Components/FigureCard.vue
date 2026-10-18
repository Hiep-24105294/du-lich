<script setup>
import { Link } from '@inertiajs/vue3';
import CoverImage from '@/Components/CoverImage.vue';
import { useT } from '@/Composables/useT';
import { useFormat } from '@/Composables/useFormat';

defineProps({
    figure: { type: Object, required: true },
});
const { te } = useT();
const { yearRange } = useFormat();
</script>

<template>
    <article class="card card-hover flex h-full flex-col overflow-hidden text-center">
        <Link :href="figure.url" tabindex="-1" aria-hidden="true" class="block p-4 pb-0">
            <CoverImage :src="figure.portrait" :alt="figure.title" icon="user" ratio="aspect-square" class="mx-auto w-28 rounded-full ring-4 ring-accent-soft" />
        </Link>
        <div class="flex flex-1 flex-col items-center gap-1 p-4">
            <h3 class="text-base font-bold">
                <Link :href="figure.url" class="hover:text-primary">{{ figure.title }}</Link>
            </h3>
            <p class="text-xs font-semibold text-accent-strong">{{ yearRange(figure.birth_year, figure.death_year) }}</p>
            <span class="badge">{{ te('figure_type', figure.figure_type) }}</span>
            <p class="mt-1 line-clamp-3 text-sm text-muted">{{ figure.summary }}</p>
        </div>
    </article>
</template>
