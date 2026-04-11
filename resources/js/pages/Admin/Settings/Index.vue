<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ChevronDown, ChevronRight } from 'lucide-vue-next';
import { ref } from 'vue';

type Setting = { id: number; key: string; value: string | null; type: string; label: string; group: string };

const props = defineProps<{
    settings: Record<string, Setting[]>;
}>();

const groupLabels: Record<string, string> = {
    general:  'Geral',
    contact:  'Contato',
    email:    'E-mail',
    seo:      'SEO',
    merchant: 'Google Merchant',
};

// Build a flat settingsMap { key: value } for the form
const settingsMap: Record<string, string> = {};
Object.values(props.settings).flat().forEach(s => {
    settingsMap[s.key] = s.value ?? '';
});

const form = useForm({ settings: settingsMap });

const openGroups = ref<Record<string, boolean>>({
    general: true,
    contact: true,
    email: true,
    seo: false,
    merchant: false,
});

function submit() {
    form.post(route('admin.settings.update'));
}
</script>

<template>
    <AdminLayout>
        <Head title="Configurações" />

        <template #header>
            <div>
                <h1 class="text-lg font-semibold text-slate-900">Configurações</h1>
                <p class="text-sm text-slate-500">Gerencie as configurações do site</p>
            </div>
        </template>

        <form @submit.prevent="submit" class="max-w-3xl space-y-4">
            <div
                v-for="(items, group) in settings"
                :key="group"
                class="bg-white rounded-xl border border-slate-200 overflow-hidden"
            >
                <button
                    type="button"
                    class="w-full flex items-center justify-between px-6 py-4 hover:bg-slate-50 transition-colors"
                    @click="openGroups[group] = !openGroups[group]"
                >
                    <span class="font-semibold text-slate-900">{{ groupLabels[group] ?? group }}</span>
                    <component :is="openGroups[group] ? ChevronDown : ChevronRight" :size="18" class="text-slate-400" />
                </button>

                <div v-show="openGroups[group]" class="px-6 pb-6 space-y-4 border-t border-slate-100">
                    <div v-for="setting in items" :key="setting.key" class="pt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ setting.label }}</label>

                        <textarea
                            v-if="setting.type === 'textarea'"
                            v-model="form.settings[setting.key]"
                            rows="3"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                        />
                        <input
                            v-else
                            v-model="form.settings[setting.key]"
                            :type="setting.type === 'email' ? 'email' : setting.type === 'url' ? 'url' : setting.type === 'phone' ? 'tel' : 'text'"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white font-medium px-6 py-2.5 rounded-lg text-sm transition-colors"
                >
                    {{ form.processing ? 'Salvando...' : 'Salvar Configurações' }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>
