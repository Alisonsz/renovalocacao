<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { computed } from 'vue';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps<{
    category?: {
        id: number;
        name: string;
        description: string | null;
        order: number;
        is_active: boolean;
    };
}>();

const isEditing = computed(() => !!props.category);

const form = useForm({
    name:        props.category?.name ?? '',
    description: props.category?.description ?? '',
    order:       props.category?.order ?? 0,
    is_active:   props.category?.is_active ?? true,
    image:       null as File | null,
});

function submit() {
    if (isEditing.value) {
        form.put(route('admin.categories.update', props.category!.id), {
            forceFormData: true,
        });
    } else {
        form.post(route('admin.categories.store'), { forceFormData: true });
    }
}
</script>

<template>
    <AdminLayout>
        <Head :title="isEditing ? 'Editar Categoria' : 'Nova Categoria'" />

        <template #header>
            <div class="flex items-center gap-3 w-full">
                <Link
                    :href="route('admin.categories.index')"
                    class="p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors"
                >
                    <ArrowLeft :size="18" />
                </Link>
                <h1 class="text-lg font-semibold text-slate-900">
                    {{ isEditing ? 'Editar Categoria' : 'Nova Categoria' }}
                </h1>
            </div>
        </template>

        <form @submit.prevent="submit" class="max-w-xl">
            <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nome *</label>
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ex: Depilação a Laser"
                    />
                    <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Descrição</label>
                    <textarea
                        v-model="form.description"
                        rows="3"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                        placeholder="Descrição da categoria (opcional)"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Imagem da Categoria</label>
                    <input
                        type="file"
                        accept="image/*"
                        class="w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        @change="form.image = ($event.target as HTMLInputElement).files?.[0] ?? null"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Ordem</label>
                    <input
                        v-model="form.order"
                        type="number"
                        min="0"
                        class="w-32 border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <div>
                    <label class="flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
                        <input v-model="form.is_active" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
                        Categoria ativa
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-5">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white font-medium px-6 py-2.5 rounded-lg text-sm transition-colors"
                >
                    {{ form.processing ? 'Salvando...' : (isEditing ? 'Atualizar' : 'Criar Categoria') }}
                </button>
                <Link
                    :href="route('admin.categories.index')"
                    class="text-slate-600 hover:text-slate-900 font-medium px-4 py-2.5 rounded-lg text-sm transition-colors hover:bg-slate-100"
                >
                    Cancelar
                </Link>
            </div>
        </form>
    </AdminLayout>
</template>
