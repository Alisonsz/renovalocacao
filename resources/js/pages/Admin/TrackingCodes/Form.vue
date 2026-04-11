<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { computed } from 'vue';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps<{
    trackingCode?: {
        id: number;
        name: string;
        code: string;
        position: string;
        is_active: boolean;
        pages: string[] | null;
    };
}>();

const isEditing = computed(() => !!props.trackingCode);

const form = useForm({
    name:      props.trackingCode?.name ?? '',
    code:      props.trackingCode?.code ?? '',
    position:  props.trackingCode?.position ?? 'head',
    is_active: props.trackingCode?.is_active ?? true,
    pages:     props.trackingCode?.pages ?? [],
});

const availablePages = [
    { value: 'home',     label: 'Home' },
    { value: 'product',  label: 'Produto' },
    { value: 'booking',  label: 'Reserva (Checkout)' },
    { value: 'success',  label: 'Confirmação de Reserva' },
];

function togglePage(page: string) {
    const idx = form.pages.indexOf(page);
    if (idx === -1) form.pages.push(page);
    else form.pages.splice(idx, 1);
}

function submit() {
    if (isEditing.value) {
        form.put(route('admin.tracking-codes.update', props.trackingCode!.id));
    } else {
        form.post(route('admin.tracking-codes.store'));
    }
}
</script>

<template>
    <AdminLayout>
        <Head :title="isEditing ? 'Editar Código' : 'Novo Código de Rastreamento'" />

        <template #header>
            <div class="flex items-center gap-3 w-full">
                <Link :href="route('admin.tracking-codes.index')" class="p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
                    <ArrowLeft :size="18" />
                </Link>
                <h1 class="text-lg font-semibold text-slate-900">
                    {{ isEditing ? 'Editar Código' : 'Novo Código de Rastreamento' }}
                </h1>
            </div>
        </template>

        <form @submit.prevent="submit" class="max-w-2xl">
            <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nome *</label>
                    <input v-model="form.name" type="text" required
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ex: Google Analytics GA4, Facebook Pixel..." />
                    <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Código *</label>
                    <textarea v-model="form.code" required rows="8"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y bg-slate-50"
                        placeholder="Cole aqui o código do script (incluindo as tags <script>)..." />
                    <p v-if="form.errors.code" class="text-red-500 text-xs mt-1">{{ form.errors.code }}</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Posição de Injeção</label>
                        <select v-model="form.position" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="head">Dentro do &lt;head&gt;</option>
                            <option value="body_start">Início do &lt;body&gt;</option>
                            <option value="body_end">Final do &lt;body&gt;</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer mt-3">
                            <input v-model="form.is_active" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
                            Código ativo
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Páginas (deixe em branco para todas)</label>
                    <div class="flex flex-wrap gap-2">
                        <label
                            v-for="pg in availablePages"
                            :key="pg.value"
                            :class="[
                                'flex items-center gap-2 px-3 py-1.5 rounded-full border text-sm cursor-pointer transition-colors',
                                form.pages.includes(pg.value)
                                    ? 'bg-blue-600 border-blue-600 text-white'
                                    : 'border-slate-300 text-slate-600 hover:border-blue-300'
                            ]"
                        >
                            <input type="checkbox" class="hidden" :checked="form.pages.includes(pg.value)" @change="togglePage(pg.value)" />
                            {{ pg.label }}
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-5">
                <button type="submit" :disabled="form.processing"
                    class="bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white font-medium px-6 py-2.5 rounded-lg text-sm transition-colors">
                    {{ form.processing ? 'Salvando...' : (isEditing ? 'Atualizar' : 'Criar Código') }}
                </button>
                <Link :href="route('admin.tracking-codes.index')" class="text-slate-600 hover:text-slate-900 font-medium px-4 py-2.5 rounded-lg text-sm hover:bg-slate-100">
                    Cancelar
                </Link>
            </div>
        </form>
    </AdminLayout>
</template>
