<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Plus, Pencil, Trash2, Star } from 'lucide-vue-next';

defineProps<{
    testimonials: Array<{
        id: number;
        customer_name: string;
        customer_company: string | null;
        customer_city: string | null;
        testimonial: string;
        rating: number;
        is_active: boolean;
        order: number;
    }>;
}>();

function destroy(id: number) {
    if (confirm('Excluir este depoimento?')) {
        router.delete(route('admin.testimonials.destroy', id));
    }
}
</script>

<template>
    <AdminLayout>
        <Head title="Depoimentos" />

        <template #header>
            <div class="flex items-center justify-between w-full">
                <div>
                    <h1 class="text-lg font-semibold text-slate-900">Depoimentos</h1>
                    <p class="text-sm text-slate-500">{{ testimonials.length }} depoimento(s)</p>
                </div>
                <Link
                    :href="route('admin.testimonials.create')"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors"
                >
                    <Plus :size="16" />
                    Novo Depoimento
                </Link>
            </div>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
                v-for="t in testimonials"
                :key="t.id"
                :class="['bg-white rounded-xl border p-5 transition-all', t.is_active ? 'border-slate-200' : 'border-slate-100 opacity-60']"
            >
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="font-semibold text-slate-900">{{ t.customer_name }}</p>
                        <p v-if="t.customer_company" class="text-xs text-slate-500">{{ t.customer_company }}</p>
                        <p v-if="t.customer_city" class="text-xs text-slate-400">{{ t.customer_city }}</p>
                    </div>
                    <div class="flex gap-0.5">
                        <Star
                            v-for="i in 5"
                            :key="i"
                            :size="13"
                            :class="i <= t.rating ? 'text-yellow-400 fill-yellow-400' : 'text-slate-200'"
                        />
                    </div>
                </div>
                <p class="text-sm text-slate-600 line-clamp-3 mb-4">"{{ t.testimonial }}"</p>
                <div class="flex items-center justify-between">
                    <span :class="['text-xs font-medium', t.is_active ? 'text-green-600' : 'text-slate-400']">
                        {{ t.is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                    <div class="flex gap-2">
                        <Link
                            :href="route('admin.testimonials.edit', t.id)"
                            class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                        >
                            <Pencil :size="14" />
                        </Link>
                        <button
                            class="p-1.5 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                            @click="destroy(t.id)"
                        >
                            <Trash2 :size="14" />
                        </button>
                    </div>
                </div>
            </div>

            <div
                v-if="!testimonials.length"
                class="md:col-span-2 lg:col-span-3 bg-white rounded-xl border border-slate-200 p-12 text-center text-slate-400"
            >
                Nenhum depoimento cadastrado.
                <Link :href="route('admin.testimonials.create')" class="text-blue-600 hover:underline ml-1">
                    Adicionar agora →
                </Link>
            </div>
        </div>
    </AdminLayout>
</template>
