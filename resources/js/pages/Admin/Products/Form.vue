<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import TiptapEditor from '@/components/admin/TiptapEditor.vue';
import { ref, computed } from 'vue';
import { Trash2, Upload, Star, ArrowLeft } from 'lucide-vue-next';

const props = defineProps<{
    categories: Array<{ id: number; name: string }>;
    product?: {
        id: number;
        name: string;
        slug: string;
        category_id: number;
        short_description: string | null;
        description: string | null;
        availability: string;
        price_per_day: number | null;
        is_active: boolean;
        order: number;
        meta_title: string | null;
        meta_description: string | null;
        meta_keywords: string | null;
        gm_brand: string | null;
        gm_gtin: string | null;
        gm_mpn: string | null;
        gm_condition: string;
        gm_google_product_category: string | null;
        gm_price: number | null;
        gm_currency: string;
        images: Array<{ id: number; url: string; alt_text: string | null; is_primary: boolean }>;
    };
}>();

const isEditing = computed(() => !!props.product);

const form = useForm({
    name:                    props.product?.name ?? '',
    category_id:             props.product?.category_id ?? '',
    short_description:       props.product?.short_description ?? '',
    description:             props.product?.description ?? '',
    availability:            props.product?.availability ?? 'available',
    price_per_day:           props.product?.price_per_day ?? '',
    is_active:               props.product?.is_active ?? true,
    order:                   props.product?.order ?? 0,
    meta_title:              props.product?.meta_title ?? '',
    meta_description:        props.product?.meta_description ?? '',
    meta_keywords:           props.product?.meta_keywords ?? '',
    gm_brand:                props.product?.gm_brand ?? '',
    gm_gtin:                 props.product?.gm_gtin ?? '',
    gm_mpn:                  props.product?.gm_mpn ?? '',
    gm_condition:            props.product?.gm_condition ?? 'new',
    gm_google_product_category: props.product?.gm_google_product_category ?? '',
    gm_price:                props.product?.gm_price ?? '',
    gm_currency:             props.product?.gm_currency ?? 'BRL',
    images:                  [] as File[],
    delete_image_ids:        [] as number[],
});

const imagePreviews = ref<Array<{ file: File; url: string }>>([]);

function handleImageUpload(event: Event) {
    const input = event.target as HTMLInputElement;
    if (!input.files) return;
    Array.from(input.files).forEach(file => {
        imagePreviews.value.push({ file, url: URL.createObjectURL(file) });
        form.images.push(file);
    });
    input.value = '';
}

function removeNewImage(index: number) {
    URL.revokeObjectURL(imagePreviews.value[index].url);
    imagePreviews.value.splice(index, 1);
    form.images.splice(index, 1);
}

function markDeleteImage(id: number) {
    form.delete_image_ids.push(id);
}

function unmarkDeleteImage(id: number) {
    const idx = form.delete_image_ids.indexOf(id);
    if (idx !== -1) form.delete_image_ids.splice(idx, 1);
}

function isMarkedForDelete(id: number) {
    return form.delete_image_ids.includes(id);
}

function submit() {
    if (isEditing.value) {
        form.post(route('admin.products.update', props.product!.id), {
            forceFormData: true,
            _method: 'PUT',
        } as any);
    } else {
        form.post(route('admin.products.store'), { forceFormData: true });
    }
}

const activeTab = ref<'basic' | 'seo' | 'merchant'>('basic');
</script>

<template>
    <AdminLayout>
        <Head :title="isEditing ? 'Editar Produto' : 'Novo Produto'" />

        <template #header>
            <div class="flex items-center gap-3 w-full">
                <Link
                    :href="route('admin.products.index')"
                    class="p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors"
                >
                    <ArrowLeft :size="18" />
                </Link>
                <div>
                    <h1 class="text-lg font-semibold text-slate-900">
                        {{ isEditing ? 'Editar Produto' : 'Novo Produto' }}
                    </h1>
                </div>
            </div>
        </template>

        <form @submit.prevent="submit" class="max-w-4xl space-y-6">
            <!-- Tabs -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="flex border-b border-slate-200">
                    <button
                        v-for="tab in [
                            { key: 'basic', label: 'Informações' },
                            { key: 'seo', label: 'SEO' },
                            { key: 'merchant', label: 'Google Merchant' },
                        ]"
                        :key="tab.key"
                        type="button"
                        :class="[
                            'px-6 py-3 text-sm font-medium transition-colors border-b-2 -mb-px',
                            activeTab === tab.key
                                ? 'text-blue-600 border-blue-600'
                                : 'text-slate-500 border-transparent hover:text-slate-700'
                        ]"
                        @click="activeTab = tab.key as any"
                    >
                        {{ tab.label }}
                    </button>
                </div>

                <!-- Basic Tab -->
                <div v-show="activeTab === 'basic'" class="p-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nome do Produto *</label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Ex: Máquina de Depilação a Laser Diodo"
                            />
                            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Categoria *</label>
                            <select
                                v-model="form.category_id"
                                required
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="">Selecione uma categoria</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Disponibilidade</label>
                            <select
                                v-model="form.availability"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="available">Disponível</option>
                                <option value="rented">Locado</option>
                                <option value="maintenance">Em Manutenção</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Valor/Dia (R$)</label>
                            <input
                                v-model="form.price_per_day"
                                type="number"
                                step="0.01"
                                min="0"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="0.00"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Ordem de exibição</label>
                            <input
                                v-model="form.order"
                                type="number"
                                min="0"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Descrição Curta</label>
                            <input
                                v-model="form.short_description"
                                type="text"
                                maxlength="500"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Resumo em uma linha para listagem"
                            />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Descrição Completa</label>
                            <TiptapEditor v-model="form.description" placeholder="Descreva o produto em detalhes..." />
                        </div>

                        <div class="md:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
                                <input v-model="form.is_active" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
                                Produto ativo (visível no site)
                            </label>
                        </div>
                    </div>

                    <!-- Images -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-3">Fotos do Produto</label>

                        <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-3">
                            <!-- Existing images -->
                            <div
                                v-for="img in product?.images ?? []"
                                :key="img.id"
                                :class="['relative rounded-lg overflow-hidden border-2 aspect-square group', isMarkedForDelete(img.id) ? 'border-red-400 opacity-50' : 'border-slate-200']"
                            >
                                <img :src="img.url" :alt="img.alt_text ?? ''" class="w-full h-full object-cover" />
                                <div v-if="img.is_primary" class="absolute top-1 left-1">
                                    <Star :size="14" class="text-yellow-400 fill-yellow-400" />
                                </div>
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <button
                                        type="button"
                                        class="p-1.5 rounded-full bg-white/20 text-white hover:bg-red-500 transition-colors"
                                        @click="isMarkedForDelete(img.id) ? unmarkDeleteImage(img.id) : markDeleteImage(img.id)"
                                    >
                                        <Trash2 :size="14" />
                                    </button>
                                </div>
                            </div>

                            <!-- New image previews -->
                            <div
                                v-for="(preview, index) in imagePreviews"
                                :key="'new-' + index"
                                class="relative rounded-lg overflow-hidden border-2 border-blue-300 aspect-square group"
                            >
                                <img :src="preview.url" alt="Nova imagem" class="w-full h-full object-cover" />
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <button
                                        type="button"
                                        class="p-1.5 rounded-full bg-red-500 text-white transition-colors"
                                        @click="removeNewImage(index)"
                                    >
                                        <Trash2 :size="14" />
                                    </button>
                                </div>
                            </div>

                            <!-- Upload Button -->
                            <label class="border-2 border-dashed border-slate-300 rounded-lg aspect-square flex flex-col items-center justify-center gap-1 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors">
                                <Upload :size="20" class="text-slate-400" />
                                <span class="text-xs text-slate-400">Adicionar</span>
                                <input type="file" multiple accept="image/*" class="hidden" @change="handleImageUpload" />
                            </label>
                        </div>
                        <p class="text-xs text-slate-400 mt-2">A primeira imagem será a principal. Máximo 5MB por foto.</p>
                    </div>
                </div>

                <!-- SEO Tab -->
                <div v-show="activeTab === 'seo'" class="p-6 space-y-5">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-700 mb-4">
                        Configure os metadados para melhorar o posicionamento nos mecanismos de busca.
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Meta Title</label>
                        <input v-model="form.meta_title" type="text" maxlength="255"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :placeholder="form.name || 'Título para o Google'" />
                        <p class="text-xs text-slate-400 mt-1">{{ form.meta_title?.length ?? 0 }}/60 caracteres recomendados</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Meta Description</label>
                        <textarea v-model="form.meta_description" rows="3" maxlength="500"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                            placeholder="Descrição para aparecer nos resultados do Google..." />
                        <p class="text-xs text-slate-400 mt-1">{{ form.meta_description?.length ?? 0 }}/160 caracteres recomendados</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Keywords</label>
                        <input v-model="form.meta_keywords" type="text"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="palavra-chave1, palavra-chave2, ..." />
                    </div>
                </div>

                <!-- Google Merchant Tab -->
                <div v-show="activeTab === 'merchant'" class="p-6 space-y-5">
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-700 mb-4">
                        Configure os dados para o Google Merchant Center. O feed XML está disponível em
                        <code class="bg-amber-100 px-1 rounded">/google-merchant.xml</code>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Marca / Brand</label>
                            <input v-model="form.gm_brand" type="text"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Ex: Lumedical" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">GTIN</label>
                            <input v-model="form.gm_gtin" type="text"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Código de barras global" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">MPN</label>
                            <input v-model="form.gm_mpn" type="text"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Part Number do fabricante" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Condição</label>
                            <select v-model="form.gm_condition"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="new">Novo</option>
                                <option value="refurbished">Recondicionado</option>
                                <option value="used">Usado</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Preço no Merchant (R$)</label>
                            <input v-model="form.gm_price" type="number" step="0.01" min="0"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="0.00" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Moeda</label>
                            <input v-model="form.gm_currency" type="text" maxlength="3"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="BRL" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Categoria Google Product</label>
                            <input v-model="form.gm_google_product_category" type="text"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Ex: Health & Beauty > Personal Care > Hair Removal" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white font-medium px-6 py-2.5 rounded-lg text-sm transition-colors"
                >
                    {{ form.processing ? 'Salvando...' : (isEditing ? 'Atualizar Produto' : 'Criar Produto') }}
                </button>
                <Link
                    :href="route('admin.products.index')"
                    class="text-slate-600 hover:text-slate-900 font-medium px-4 py-2.5 rounded-lg text-sm transition-colors hover:bg-slate-100"
                >
                    Cancelar
                </Link>
            </div>
        </form>
    </AdminLayout>
</template>
