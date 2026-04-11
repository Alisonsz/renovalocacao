<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { MessageCircle, ChevronLeft, ChevronRight, ArrowLeft, Package, Star, CheckCircle, Menu, X } from 'lucide-vue-next';

type Product = {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    short_description: string | null;
    availability: string;
    price_per_day: number | null;
    meta_title: string | null;
    meta_description: string | null;
    meta_keywords: string | null;
    category: { id: number; name: string } | null;
    images: Array<{ id: number; url: string; alt_text: string | null; is_primary: boolean }>;
};

type RelatedProduct = {
    id: number;
    name: string;
    slug: string;
    short_description: string | null;
    availability: string;
    price_per_day: number | null;
    main_image_url: string | null;
};

const props = defineProps<{
    product: Product;
    relatedProducts: RelatedProduct[];
    settings: Record<string, string>;
}>();

const currentImageIndex = ref(0);
const scrolled = ref(false);
const mobileMenuOpen = ref(false);

const currentImage = computed(() => props.product.images[currentImageIndex.value] ?? null);

const whatsappUrl = computed(() => {
    const num = props.settings.whatsapp_number ?? '55';
    const msg = encodeURIComponent(`Olá! Tenho interesse em mais informações sobre: ${props.product.name}`);
    return `https://wa.me/${num}?text=${msg}`;
});

const metaTitle = computed(() => props.product.meta_title ?? props.product.name);
const metaDesc = computed(() => props.product.meta_description ?? props.product.short_description ?? '');

function prevImage() {
    if (props.product.images.length === 0) return;
    currentImageIndex.value = (currentImageIndex.value - 1 + props.product.images.length) % props.product.images.length;
}

function nextImage() {
    if (props.product.images.length === 0) return;
    currentImageIndex.value = (currentImageIndex.value + 1) % props.product.images.length;
}

function onScroll() { scrolled.value = window.scrollY > 50; }
onMounted(() => window.addEventListener('scroll', onScroll));
onUnmounted(() => window.removeEventListener('scroll', onScroll));

const availabilityInfo: Record<string, { label: string; color: string }> = {
    available:   { label: 'Disponível para Locação', color: 'text-green-700 bg-green-100' },
    rented:      { label: 'Atualmente Locado',        color: 'text-blue-700 bg-blue-100' },
    maintenance: { label: 'Em Manutenção',             color: 'text-orange-700 bg-orange-100' },
};
</script>

<template>
    <Head>
        <title>{{ metaTitle }}</title>
        <meta name="description" :content="metaDesc" />
        <meta v-if="product.meta_keywords" name="keywords" :content="product.meta_keywords" />
        <meta property="og:title" :content="metaTitle" />
        <meta property="og:description" :content="metaDesc" />
        <meta v-if="currentImage" property="og:image" :content="currentImage.url" />
    </Head>

    <div class="min-h-screen bg-white font-sans antialiased">
        <!-- Navbar (same style as Home) -->
        <header :class="['fixed top-0 inset-x-0 z-50 transition-all duration-300', scrolled ? 'bg-white/95 backdrop-blur-sm shadow-sm py-3' : 'bg-white shadow-sm py-4']">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <Link :href="route('home')" class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gradient-to-br from-[#1E3A8A] to-[#3B82F6] rounded-xl flex items-center justify-center shadow">
                        <span class="text-white font-bold text-xs">RL</span>
                    </div>
                    <span class="font-bold text-lg text-[#0F172A]" style="font-family: 'Playfair Display', serif;">
                        {{ settings.company_name ?? 'Renova Locação' }}
                    </span>
                </Link>
                <div class="flex items-center gap-4">
                    <Link :href="route('home')" class="flex items-center gap-2 text-sm text-slate-600 hover:text-[#1E3A8A] transition-colors">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Link>
                    <a
                        :href="whatsappUrl"
                        target="_blank"
                        class="hidden md:inline-flex items-center gap-2 bg-[#1E3A8A] hover:bg-[#1D4ED8] text-white text-sm font-semibold px-5 py-2.5 rounded-full transition-all"
                    >
                        <MessageCircle :size="16" />
                        WhatsApp
                    </a>
                </div>
            </div>
        </header>

        <!-- Breadcrumb -->
        <div class="pt-20 bg-slate-50 border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <nav class="flex items-center gap-2 text-xs text-slate-400">
                    <Link :href="route('home')" class="hover:text-[#1E3A8A] transition-colors">Início</Link>
                    <span>/</span>
                    <span v-if="product.category" class="hover:text-[#1E3A8A] cursor-pointer">{{ product.category.name }}</span>
                    <span v-if="product.category">/</span>
                    <span class="text-slate-700 font-medium">{{ product.name }}</span>
                </nav>
            </div>
        </div>

        <!-- Product Main -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 mb-16">
                <!-- Image Gallery -->
                <div>
                    <div class="relative bg-slate-50 rounded-2xl overflow-hidden aspect-[4/3] shadow-sm mb-4">
                        <img
                            v-if="currentImage"
                            :src="currentImage.url"
                            :alt="currentImage.alt_text ?? product.name"
                            class="w-full h-full object-contain p-4"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <Package :size="64" class="text-slate-300" />
                        </div>
                        <!-- Navigation buttons -->
                        <template v-if="product.images.length > 1">
                            <button
                                class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-slate-50 transition-colors"
                                @click="prevImage"
                            >
                                <ChevronLeft :size="18" class="text-slate-700" />
                            </button>
                            <button
                                class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-slate-50 transition-colors"
                                @click="nextImage"
                            >
                                <ChevronRight :size="18" class="text-slate-700" />
                            </button>
                        </template>
                        <!-- Counter -->
                        <div v-if="product.images.length > 1" class="absolute bottom-3 right-3 bg-black/50 text-white text-xs px-2 py-1 rounded-full">
                            {{ currentImageIndex + 1 }} / {{ product.images.length }}
                        </div>
                    </div>

                    <!-- Thumbnail strip -->
                    <div v-if="product.images.length > 1" class="grid grid-cols-5 gap-2">
                        <button
                            v-for="(img, index) in product.images"
                            :key="img.id"
                            :class="[
                                'aspect-square rounded-xl overflow-hidden border-2 transition-all',
                                currentImageIndex === index ? 'border-[#1E3A8A] shadow-md' : 'border-transparent hover:border-slate-300'
                            ]"
                            @click="currentImageIndex = index"
                        >
                            <img :src="img.url" :alt="img.alt_text ?? ''" class="w-full h-full object-cover" />
                        </button>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="flex flex-col">
                    <div v-if="product.category" class="mb-3">
                        <span class="bg-blue-50 text-[#1E3A8A] text-xs font-semibold px-3 py-1 rounded-full">
                            {{ product.category.name }}
                        </span>
                    </div>

                    <h1 class="text-3xl lg:text-4xl font-bold text-[#0F172A] mb-4 leading-tight" style="font-family: 'Playfair Display', serif;">
                        {{ product.name }}
                    </h1>

                    <!-- Availability -->
                    <div class="flex items-center gap-2 mb-5">
                        <span :class="['inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium', availabilityInfo[product.availability]?.color ?? 'bg-slate-100 text-slate-700']">
                            <span :class="['w-2 h-2 rounded-full', product.availability === 'available' ? 'bg-green-500 animate-pulse' : product.availability === 'rented' ? 'bg-blue-500' : 'bg-orange-500']" />
                            {{ availabilityInfo[product.availability]?.label ?? product.availability }}
                        </span>
                    </div>

                    <!-- Short description -->
                    <p v-if="product.short_description" class="text-slate-600 text-lg leading-relaxed mb-6">
                        {{ product.short_description }}
                    </p>

                    <!-- Price -->
                    <div v-if="product.price_per_day" class="bg-[#F0F7FF] rounded-2xl p-5 mb-7">
                        <p class="text-xs text-slate-500 mb-1">Locação a partir de</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-bold text-[#1E3A8A]" style="font-family: 'Playfair Display', serif;">
                                R$ {{ Number(product.price_per_day).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}
                            </span>
                            <span class="text-slate-500 text-sm">/dia</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-2">*Valor pode variar conforme período de locação</p>
                    </div>

                    <!-- Benefits -->
                    <ul class="space-y-2.5 mb-8">
                        <li v-for="item in ['Entrega e instalação inclusos', 'Suporte técnico especializado', 'Contrato flexível', 'Treinamento de operação']" :key="item" class="flex items-center gap-2 text-sm text-slate-600">
                            <CheckCircle :size="16" class="text-[#3B82F6] flex-shrink-0" />
                            {{ item }}
                        </li>
                    </ul>

                    <!-- CTAs -->
                    <div class="flex flex-col sm:flex-row gap-3 mt-auto">
                        <Link
                            v-if="product.availability === 'available'"
                            :href="route('booking.create', product.slug)"
                            class="flex-1 bg-[#1E3A8A] hover:bg-[#1D4ED8] text-white font-bold py-4 px-6 rounded-2xl text-center transition-all hover:shadow-lg hover:shadow-blue-900/30 hover:-translate-y-0.5"
                        >
                            Solicitar Reserva
                        </Link>
                        <span v-else class="flex-1 bg-slate-100 text-slate-400 font-bold py-4 px-6 rounded-2xl text-center cursor-not-allowed">
                            Indisponível no Momento
                        </span>
                        <a
                            :href="whatsappUrl"
                            target="_blank"
                            class="flex-1 flex items-center justify-center gap-2 border-2 border-[#1E3A8A] text-[#1E3A8A] hover:bg-[#1E3A8A] hover:text-white font-bold py-4 px-6 rounded-2xl transition-all"
                        >
                            <MessageCircle :size="18" />
                            Mais Informações
                        </a>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div v-if="product.description" class="max-w-4xl mb-16">
                <h2 class="text-2xl font-bold text-[#0F172A] mb-6" style="font-family: 'Playfair Display', serif;">
                    Descrição do Equipamento
                </h2>
                <div
                    class="prose prose-slate max-w-none text-slate-600 leading-relaxed"
                    v-html="product.description"
                />
            </div>

            <!-- Related Products -->
            <div v-if="relatedProducts.length > 0">
                <h2 class="text-2xl font-bold text-[#0F172A] mb-8" style="font-family: 'Playfair Display', serif;">
                    Outros Equipamentos
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <article
                        v-for="rp in relatedProducts"
                        :key="rp.id"
                        class="group bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all overflow-hidden"
                    >
                        <div class="relative aspect-[4/3] bg-slate-50 overflow-hidden">
                            <img
                                v-if="rp.main_image_url"
                                :src="rp.main_image_url"
                                :alt="rp.name"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <Package :size="36" class="text-slate-300" />
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-[#0F172A] text-sm mb-1 line-clamp-1">{{ rp.name }}</h3>
                            <div v-if="rp.price_per_day" class="text-[#1E3A8A] font-bold text-sm mb-3">
                                R$ {{ Number(rp.price_per_day).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}/dia
                            </div>
                            <Link :href="route('products.show', rp.slug)" class="block w-full text-center bg-slate-100 hover:bg-[#1E3A8A] hover:text-white text-slate-700 text-xs font-semibold py-2 rounded-xl transition-colors">
                                Ver Detalhes
                            </Link>
                        </div>
                    </article>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-[#0F172A] text-slate-400 py-8 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-sm" style="font-family: 'Playfair Display', serif;">
                        <span class="text-white font-semibold">{{ settings.company_name ?? 'Renova Locação' }}</span>
                    </p>
                    <p class="text-xs text-slate-500">
                        © {{ new Date().getFullYear() }} Todos os direitos reservados.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
