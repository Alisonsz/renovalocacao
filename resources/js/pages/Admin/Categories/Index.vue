<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Plus, Pencil, Trash2 } from 'lucide-vue-next';

defineProps<{
    categories: Array<{
        id: number;
        name: string;
        slug: string;
        is_active: boolean;
        order: number;
        products_count: number;
    }>;
}>();

function destroy(id: number) {
    if (confirm('Excluir esta categoria? Os produtos associados perderão a referência.')) {
        router.delete(route('admin.categories.destroy', id));
    }
}
</script>

<template>
    <AdminLayout>
        <Head title="Categorias" />

        <template #header>
            <div class="flex items-center justify-between w-full">
                <div>
                    <h1 class="text-lg font-semibold text-slate-900">Categorias</h1>
                    <p class="text-sm text-slate-500">{{ categories.length }} categoria(s)</p>
                </div>
                <Link
                    :href="route('admin.categories.create')"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors"
                >
                    <Plus :size="16" />
                    Nova Categoria
                </Link>
            </div>
        </template>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50">
                        <th class="text-left px-6 py-3 text-slate-500 font-medium">Nome</th>
                        <th class="text-left px-6 py-3 text-slate-500 font-medium hidden md:table-cell">Slug</th>
                        <th class="text-left px-6 py-3 text-slate-500 font-medium">Produtos</th>
                        <th class="text-left px-6 py-3 text-slate-500 font-medium hidden lg:table-cell">Status</th>
                        <th class="text-right px-6 py-3 text-slate-500 font-medium">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="cat in categories"
                        :key="cat.id"
                        class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors"
                    >
                        <td class="px-6 py-4 font-medium text-slate-900">{{ cat.name }}</td>
                        <td class="px-6 py-4 hidden md:table-cell text-slate-500 font-mono text-xs">{{ cat.slug }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                {{ cat.products_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell">
                            <span :class="['text-xs font-medium', cat.is_active ? 'text-green-600' : 'text-slate-400']">
                                {{ cat.is_active ? 'Ativa' : 'Inativa' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <Link
                                    :href="route('admin.categories.edit', cat.id)"
                                    class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                >
                                    <Pencil :size="15" />
                                </Link>
                                <button
                                    class="p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    @click="destroy(cat.id)"
                                >
                                    <Trash2 :size="15" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!categories.length">
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            Nenhuma categoria cadastrada.
                            <Link :href="route('admin.categories.create')" class="text-blue-600 hover:underline ml-1">
                                Criar agora →
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
