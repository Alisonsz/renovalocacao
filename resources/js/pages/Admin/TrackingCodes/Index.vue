<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Plus, Pencil, Trash2, Eye, EyeOff } from 'lucide-vue-next';

defineProps<{
    codes: Array<{
        id: number;
        name: string;
        position: string;
        is_active: boolean;
        pages: string[] | null;
        created_at: string;
    }>;
}>();

const positionLabels: Record<string, string> = {
    head:       '<head>',
    body_start: '<body> início',
    body_end:   '<body> fim',
};

function destroy(id: number) {
    if (confirm('Remover este código de rastreamento?')) {
        router.delete(route('admin.tracking-codes.destroy', id));
    }
}
</script>

<template>
    <AdminLayout>
        <Head title="Pixels & Scripts" />

        <template #header>
            <div class="flex items-center justify-between w-full">
                <div>
                    <h1 class="text-lg font-semibold text-slate-900">Pixels & Scripts</h1>
                    <p class="text-sm text-slate-500">Google Analytics, Facebook Pixel, etc.</p>
                </div>
                <Link
                    :href="route('admin.tracking-codes.create')"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors"
                >
                    <Plus :size="16" />
                    Adicionar Código
                </Link>
            </div>
        </template>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50">
                        <th class="text-left px-6 py-3 text-slate-500 font-medium">Nome</th>
                        <th class="text-left px-6 py-3 text-slate-500 font-medium hidden md:table-cell">Posição</th>
                        <th class="text-left px-6 py-3 text-slate-500 font-medium hidden lg:table-cell">Páginas</th>
                        <th class="text-left px-6 py-3 text-slate-500 font-medium">Status</th>
                        <th class="text-right px-6 py-3 text-slate-500 font-medium">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="code in codes"
                        :key="code.id"
                        class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors"
                    >
                        <td class="px-6 py-4 font-medium text-slate-900">{{ code.name }}</td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <code class="text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded">{{ positionLabels[code.position] ?? code.position }}</code>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell text-slate-500 text-xs">
                            {{ code.pages?.join(', ') || 'Todas as páginas' }}
                        </td>
                        <td class="px-6 py-4">
                            <span :class="['inline-flex items-center gap-1 text-xs font-medium', code.is_active ? 'text-green-600' : 'text-slate-400']">
                                <component :is="code.is_active ? Eye : EyeOff" :size="14" />
                                {{ code.is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <Link :href="route('admin.tracking-codes.edit', code.id)" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <Pencil :size="15" />
                                </Link>
                                <button class="p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" @click="destroy(code.id)">
                                    <Trash2 :size="15" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!codes.length">
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            Nenhum código cadastrado.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-4 bg-slate-50 rounded-xl border border-slate-200 p-4">
            <h3 class="text-sm font-semibold text-slate-700 mb-2">Como usar</h3>
            <p class="text-xs text-slate-500">
                Adicione aqui seus pixels do Google Analytics (gtag.js), Facebook Pixel, Google Tag Manager, ou qualquer outro script de rastreamento.
                Os códigos marcados como "Ativo" serão injetados automaticamente em todas as páginas do site.
            </p>
        </div>
    </AdminLayout>
</template>
