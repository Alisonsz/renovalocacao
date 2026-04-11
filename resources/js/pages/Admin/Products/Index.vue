<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Plus, Pencil, Trash2, Eye, EyeOff } from 'lucide-vue-next';

defineProps<{
    products: Array<{
        id: number;
        name: string;
        slug: string;
        availability: string;
        is_active: boolean;
        category: string | null;
        main_image: string | null;
        order: number;
    }>;
}>();

const availabilityLabel: Record<string, string> = {
    available:   'Disponível',
    rented:      'Locado',
    maintenance: 'Manutenção',
};
const availabilityColors: Record<string, string> = {
    available:   'bg-green-100 text-green-800',
    rented:      'bg-blue-100 text-blue-800',
    maintenance: 'bg-orange-100 text-orange-800',
};

function destroy(id: number) {
    if (confirm('Tem certeza que deseja excluir este produto?')) {
        router.delete(route('admin.products.destroy', id));
    }
}
</script>

<template>
    <AdminLayout>
        <Head title="Produtos" />

        <template #header>
            <div class="flex items-center justify-between w-full">
                <div>
                    <h1 class="text-lg font-semibold text-slate-900">Produtos</h1>
                    <p class="text-sm text-slate-500">{{ products.length }} produto(s) cadastrado(s)</p>
                </div>
                <Link
                    :href="route('admin.products.create')"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors"
                >
                    <Plus :size="16" />
                    Novo Produto
                </Link>
            </div>
        </template>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/50">
                            <th class="text-left px-6 py-3 text-slate-500 font-medium">Produto</th>
                            <th class="text-left px-6 py-3 text-slate-500 font-medium hidden md:table-cell">Categoria</th>
                            <th class="text-left px-6 py-3 text-slate-500 font-medium">Situação</th>
                            <th class="text-left px-6 py-3 text-slate-500 font-medium hidden lg:table-cell">Status</th>
                            <th class="text-right px-6 py-3 text-slate-500 font-medium">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="product in products"
                            :key="product.id"
                            class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img
                                        v-if="product.main_image"
                                        :src="product.main_image"
                                        :alt="product.name"
                                        class="w-10 h-10 rounded-lg object-cover border border-slate-200"
                                    />
                                    <div
                                        v-else
                                        class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 text-xs"
                                    >
                                        N/A
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900">{{ product.name }}</p>
                                        <p class="text-slate-400 text-xs">{{ product.slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell text-slate-600">
                                {{ product.category ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', availabilityColors[product.availability]]"
                                >
                                    {{ availabilityLabel[product.availability] ?? product.availability }}
                                </span>
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                <span
                                    :class="['inline-flex items-center gap-1 text-xs font-medium', product.is_active ? 'text-green-600' : 'text-slate-400']"
                                >
                                    <component :is="product.is_active ? Eye : EyeOff" :size="14" />
                                    {{ product.is_active ? 'Visível' : 'Oculto' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <Link
                                        :href="route('admin.products.edit', product.id)"
                                        class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                    >
                                        <Pencil :size="15" />
                                    </Link>
                                    <button
                                        class="p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        @click="destroy(product.id)"
                                    >
                                        <Trash2 :size="15" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!products.length">
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                Nenhum produto cadastrado ainda.
                                <Link :href="route('admin.products.create')" class="text-blue-600 hover:underline ml-1">
                                    Criar primeiro produto →
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
