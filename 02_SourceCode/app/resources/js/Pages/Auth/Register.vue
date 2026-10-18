<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import FormField from '@/Components/FormField.vue';
import { useT } from '@/Composables/useT';
import { useLocaleRoute } from '@/Composables/useLocaleRoute';

const { t } = useT();
const lr = useLocaleRoute();
const form = useForm({ name: '', email: '', phone: '', password: '', password_confirmation: '' });

const submit = () => form.post(lr('/dang-ky'), { onFinish: () => form.reset('password', 'password_confirmation') });
</script>

<template>
    <Head :title="t('auth.register_title')" />
    <div class="card p-6 md:p-8">
        <h1 class="text-2xl font-bold">{{ t('auth.register_title') }}</h1>
        <p class="mt-1 text-sm text-muted">{{ t('auth.register_intro') }}</p>
        <form class="mt-6 space-y-4" novalidate @submit.prevent="submit">
            <FormField v-model="form.name" :label="t('common.name')" required autocomplete="name" :error="form.errors.name" />
            <FormField v-model="form.email" :label="t('common.email')" type="email" required autocomplete="email" :error="form.errors.email" />
            <FormField v-model="form.phone" :label="`${t('common.phone')} (${t('common.optional')})`" type="tel" autocomplete="tel" :error="form.errors.phone" />
            <FormField v-model="form.password" :label="t('auth.password')" type="password" required autocomplete="new-password" :hint="t('auth.password_hint')" :error="form.errors.password" />
            <FormField v-model="form.password_confirmation" :label="t('auth.password_confirmation')" type="password" required autocomplete="new-password" :error="form.errors.password_confirmation" />
            <button type="submit" class="btn btn-primary w-full" :disabled="form.processing">
                {{ form.processing ? t('common.loading') : t('auth.create_account') }}
            </button>
        </form>
        <p class="mt-6 text-center text-sm text-muted">
            {{ t('auth.have_account') }}
            <Link :href="lr('/dang-nhap')" class="link font-semibold">{{ t('nav.login') }}</Link>
        </p>
    </div>
</template>
