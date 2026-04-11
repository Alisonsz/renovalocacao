<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { computed } from 'vue';
import { ArrowLeft, Star } from 'lucide-vue-next';

const props = defineProps<{
    testimonial?: {
        id: number;
        customer_name: string;
        customer_company: string | null;
        customer_city: string | null;
        testimonial: string;
        rating: number;
        is_active: boolean;
        order: number;
    };
}>();

const isEditing = computed(() => !!props.testimonial);

const form = useForm({
    customer_name:    props.testimonial?.customer_name ?? '',
    customer_company: props.testimonial?.customer_company ?? '',
    customer_city:    props.testimonial?.customer_city ?? '',
    testimonial:      props.testimonial?.testimonial ?? '',
    rating:           props.testimonial?.rating ?? 5,
    is_active:        props.testimonial?.is_active ?? true,
    order:            props.testimonial?.order ?? 0,
    customer_photo:   null as File | null,
});

function submit() {
    if (isEditing.value) {
        form.post(route('admin.testimonials.update', props.testimonial!.id), {
            forceFormData: true,
            _method: 'PUT',
        } as any);
    } else {
        form.post(route('admin.testimonials.store'), { forceFormData: true });
    }
}
</script>

<template>
    <AdminLayout>
        <Head :title="isEditing ? 'Editar Depoimento' : 'Novo Depoimento'" />

        <template #header>
            <div class="flex items-center gap-3 w-full">
                <Link :href="route('admin.testimonials.index')" class="p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
                    <ArrowLeft :size="18" />
                </Link>
                <h1 class="text-lg font-semibold text-slate-900">{{ isEditing ? 'Editar Depoimento' : 'Novo Depoimento' }}</h1>
            </div>
        </template>

        <form @submit.prevent="submit" class="max-w-xl">
            <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nome do Cliente *</label>
                        <input v-model="form.customer_name" type="text" required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Empresa</label>
                        <input v-model="form.customer_company" type="text"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Cidade</label>
                        <input v-model="form.customer_city" type="text"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Avaliação (estrelas)</label>
                        <div class="flex gap-2 mt-2">
                            <button
                                v-for="i in 5"
                                :key="i"
                                type="button"
                                @click="form.rating = i"
                            >
                                <Star
                                    :size="24"
                                    :class="i <= form.rating ? 'text-yellow-400 fill-yellow-400' : 'text-slate-300 hover:text-yellow-300'"
                                />
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Depoimento *</label>
                    <textarea v-model="form.testimonial" required rows="4"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                        placeholder="O depoimento do cliente..." />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Foto do Cliente (opcional)</label>
                    <input type="file" accept="image/*"
                        class="w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        @change="form.customer_photo = ($event.target as HTMLInputElement).files?.[0] ?? null" />
                </div>

                <div class="flex items-center gap-5">
                    <label class="flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
                        <input v-model="form.is_active" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
                        Depoimento ativo
                    </label>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Ordem</label>
                        <input v-model="form.order" type="number" min="0" class="w-20 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-5">
                <button type="submit" :disabled="form.processing"
                    class="bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white font-medium px-6 py-2.5 rounded-lg text-sm transition-colors">
                    {{ form.processing ? 'Salvando...' : (isEditing ? 'Atualizar' : 'Criar Depoimento') }}
                </button>
                <Link :href="route('admin.testimonials.index')" class="text-slate-600 hover:text-slate-900 font-medium px-4 py-2.5 rounded-lg text-sm hover:bg-slate-100">
                    Cancelar
                </Link>
            </div>
        </form>
    </AdminLayout>
</template>
