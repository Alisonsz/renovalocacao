<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { ChevronDown, Phone, MessageCircle, Star, ChevronRight, Menu, X, ArrowRight, Package, CheckCircle, MapPin, Mail, Instagram, Facebook, Shield, Calendar, Users, Heart, Headphones, Award } from 'lucide-vue-next';

type Product = {
    id: number;
    name: string;
    slug: string;
    short_description: string | null;
    availability: string;
    price_per_day: number | null;
    category: { id: number; name: string } | null;
    main_image_url: string | null;
    images: Array<{ id: number; url: string; alt_text: string | null; is_primary: boolean }>;
};

type Testimonial = {
    id: number;
    customer_name: string;
    customer_company: string | null;
    customer_city: string | null;
    testimonial: string;
    rating: number;
};

const props = defineProps<{
    products: Product[];
    categories: Array<{ id: number; name: string; products_count: number }>;
    testimonials: Testimonial[];
    settings: Record<string, string>;
}>();

const mobileMenuOpen = ref(false);
const scrolled = ref(false);
const activeCategory = ref<number | null>(null);
const testimonialsRef = ref<HTMLElement | null>(null);
const productsRef = ref<HTMLElement | null>(null);

const filteredProducts = computed(() =>
    activeCategory.value
        ? props.products.filter(p => p.category?.id === activeCategory.value)
        : props.products
);

const whatsappUrl = computed(() => {
    const num = props.settings.whatsapp_number ?? '55';
    const msg = encodeURIComponent(props.settings.whatsapp_message ?? 'Olá! Tenho interesse em locação de equipamentos.');
    return `https://wa.me/${num}?text=${msg}`;
});

function scrollTo(id: string) {
    mobileMenuOpen.value = false;
    const el = document.getElementById(id);
    if (el) el.scrollIntoView({ behavior: 'smooth' });
}

function onScroll() {
    scrolled.value = window.scrollY > 50;
}

onMounted(() => window.addEventListener('scroll', onScroll));
onUnmounted(() => window.removeEventListener('scroll', onScroll));

const availabilityInfo: Record<string, { label: string; color: string }> = {
    available:   { label: 'Disponível',   color: 'bg-green-100 text-green-700' },
    rented:      { label: 'Locado',       color: 'bg-teal-100 text-teal-700' },
    maintenance: { label: 'Manutenção',   color: 'bg-orange-100 text-orange-700' },
};
</script>

<template>
    <Head>
        <title>{{ settings.seo_title ?? settings.company_name ?? 'Renova Locação' }}</title>
        <meta name="description" :content="settings.seo_description ?? ''" />
        <meta name="keywords" :content="settings.seo_keywords ?? ''" />
        <meta property="og:title" :content="settings.seo_title ?? settings.company_name ?? ''" />
        <meta property="og:description" :content="settings.seo_description ?? ''" />
    </Head>

    <div class="min-h-screen bg-white font-sans text-slate-900 antialiased">

        <!-- ===== NAVBAR ===== -->
        <header
            :class="[
                'fixed top-0 inset-x-0 z-50 transition-all duration-300',
                scrolled ? 'bg-white/95 backdrop-blur-sm shadow-sm py-3' : 'bg-transparent py-5'
            ]"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <!-- Logo -->
                    <button @click="scrollTo('hero')" class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-[#0F5D57] to-[#0D9488] rounded-xl flex items-center justify-center shadow">
                            <span class="text-white font-bold text-sm tracking-wide">RL</span>
                        </div>
                        <span :class="['font-bold text-xl tracking-tight', scrolled ? 'text-[#0F172A]' : 'text-white drop-shadow']" style="font-family: 'Playfair Display', serif;">
                            Renova Locação
                        </span>
                    </button>

                    <!-- Desktop Nav -->
                    <nav class="hidden md:flex items-center gap-8">
                        <button
                            v-for="item in [
                                { label: 'Início', id: 'hero' },
                                { label: 'Serviços', id: 'products' },
                                { label: 'Sobre Nós', id: 'about' },
                                { label: 'Depoimentos', id: 'testimonials' },
                                { label: 'Contato', id: 'contact' },
                            ]"
                            :key="item.id"
                            :class="['text-sm font-medium transition-colors', scrolled ? 'text-slate-600 hover:text-[#0F766E]' : 'text-white/90 hover:text-white']"
                            @click="scrollTo(item.id)"
                        >
                            {{ item.label }}
                        </button>
                        <a
                            :href="whatsappUrl"
                            target="_blank"
                            class="inline-flex items-center gap-2 bg-[#0F5D57] hover:bg-[#0D9488] text-white text-sm font-semibold px-5 py-2.5 rounded-full transition-all shadow-lg hover:shadow-teal-500/30 hover:-translate-y-0.5"
                        >
                            <MessageCircle :size="16" />
                            Fale Conosco
                        </a>
                    </nav>

                    <!-- Mobile Menu Button -->
                    <button
                        :class="['md:hidden p-2 rounded-lg transition-colors', scrolled ? 'text-slate-700 hover:bg-slate-100' : 'text-white hover:bg-white/10']"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                    >
                        <component :is="mobileMenuOpen ? X : Menu" :size="24" />
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div v-if="mobileMenuOpen" class="md:hidden bg-white border-t border-slate-100 shadow-xl">
                <div class="px-4 py-4 space-y-1">
                    <button
                        v-for="item in [
                            { label: 'Início', id: 'hero' },
                            { label: 'Serviços', id: 'products' },
                            { label: 'Sobre Nós', id: 'about' },
                            { label: 'Depoimentos', id: 'testimonials' },
                            { label: 'Contato', id: 'contact' },
                        ]"
                        :key="item.id"
                        class="w-full text-left px-4 py-3 text-sm font-medium text-slate-700 hover:text-[#0F766E] hover:bg-slate-50 rounded-lg transition-colors"
                        @click="scrollTo(item.id)"
                    >
                        {{ item.label }}
                    </button>
                    <a
                        :href="whatsappUrl"
                        target="_blank"
                        class="flex items-center gap-2 bg-[#0F5D57] text-white text-sm font-semibold px-4 py-3 rounded-full mt-3 justify-center transition-colors hover:bg-[#0D9488]"
                    >
                        <MessageCircle :size="16" />
                        Fale Conosco no WhatsApp
                    </a>
                </div>
            </div>
        </header>

        <!-- ===== HERO SECTION ===== -->
        <section id="hero" class="relative min-h-screen flex items-center overflow-hidden">
            <!-- Base dark background -->
            <div class="absolute inset-0 bg-[#071F1C]" />
            <!-- Gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#0A2721] via-[#0F5D57]/70 to-[#071F1C]" />
            <!-- Glowing blobs -->
            <div class="absolute -top-32 right-0 w-[700px] h-[700px] bg-[#0D9488]/20 rounded-full blur-[130px]" />
            <div class="absolute bottom-0 -left-32 w-[500px] h-[500px] bg-[#0F5D57]/25 rounded-full blur-[100px]" />
            <!-- Subtle grid -->
            <div class="absolute inset-0 opacity-[0.035]" style="background-image: linear-gradient(rgba(255,255,255,0.6) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.6) 1px, transparent 1px); background-size: 64px 64px;" />
            <!-- Decorative rings -->
            <div class="absolute top-1/2 right-0 -translate-y-1/2 translate-x-1/3 w-[700px] h-[700px] rounded-full border border-white/[0.04]" />
            <div class="absolute top-1/2 right-0 -translate-y-1/2 translate-x-1/3 w-[500px] h-[500px] rounded-full border border-white/[0.06]" />

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-24 w-full">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">

                    <!-- LEFT: Main content -->
                    <div>
                        <!-- Badge -->
                        <div class="inline-flex items-center gap-2 bg-[#0D9488]/20 backdrop-blur-sm text-[#5EEAD4] text-xs font-semibold px-4 py-2 rounded-full border border-[#0D9488]/40 mb-8 uppercase tracking-widest">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#2DD4BF] animate-pulse" />
                            Locação de Equipamentos de Estética
                        </div>

                        <h1 class="text-5xl sm:text-6xl lg:text-[62px] font-bold leading-[1.1] mb-6" style="font-family: 'Playfair Display', serif;">
                            <span class="text-white">{{ settings.hero_title ?? 'Equipe seu negócio.' }}</span>
                            <span class="block mt-1 text-transparent bg-clip-text bg-gradient-to-r from-[#2DD4BF] to-[#5EEAD4]">
                                Sem complicação.
                            </span>
                        </h1>

                        <p class="text-lg text-white/65 max-w-lg mb-10 leading-relaxed">
                            {{ settings.hero_subtitle ?? 'Locamos equipamentos de estética premium com transparência total, suporte humano e condições flexíveis pensadas para o crescimento do seu negócio.' }}
                        </p>

                        <!-- CTAs -->
                        <div class="flex flex-wrap gap-4 mb-14">
                            <button
                                class="inline-flex items-center gap-2 bg-[#0D9488] hover:bg-[#0F766E] text-white font-semibold px-8 py-4 rounded-full text-base transition-all shadow-2xl shadow-[#0D9488]/30 hover:-translate-y-0.5 hover:shadow-[#0D9488]/50"
                                @click="scrollTo('products')"
                            >
                                Ver Equipamentos
                                <ArrowRight :size="18" />
                            </button>
                            <a
                                :href="whatsappUrl"
                                target="_blank"
                                class="inline-flex items-center gap-2 border border-white/20 hover:border-white/40 bg-white/5 hover:bg-white/10 text-white font-semibold px-8 py-4 rounded-full text-base transition-all"
                            >
                                <MessageCircle :size="18" />
                                Fale pelo WhatsApp
                            </a>
                        </div>

                        <!-- Trust strip -->
                        <div class="flex flex-wrap items-center gap-8 pt-8 border-t border-white/10">
                            <div v-for="stat in [
                                { value: '100%', label: 'Satisfação garantida' },
                                { value: '24h', label: 'Suporte disponível' },
                                { value: 'Zero', label: 'Burocracia' },
                            ]" :key="stat.label">
                                <p class="text-2xl font-bold text-white" style="font-family: 'Playfair Display', serif;">{{ stat.value }}</p>
                                <p class="text-teal-300/50 text-xs uppercase tracking-wider mt-0.5">{{ stat.label }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: Advantage cards (desktop) -->
                    <div class="hidden lg:block relative">
                        <!-- Center glow -->
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="w-72 h-72 bg-[#0D9488]/15 rounded-full blur-3xl" />
                        </div>

                        <div class="grid grid-cols-2 gap-4 relative">
                            <div
                                v-for="(adv, i) in [
                                    { icon: Shield,     title: 'Transparência',     desc: 'Contratos claros, sem surpresas e sem letras miúdas.' },
                                    { icon: Calendar,   title: 'Agenda Flexível',   desc: 'Locação por dia, semana ou mês, no seu ritmo.' },
                                    { icon: Users,      title: 'Time Capacitado',   desc: 'Profissionais treinados prontos para te apoiar.' },
                                    { icon: Heart,      title: 'Equipe Humana',     desc: 'Atendimento próximo, com quem realmente se importa.' },
                                    { icon: Headphones, title: 'Suporte 24h',       desc: 'Sempre disponível quando você mais precisar.' },
                                    { icon: Award,      title: 'Qualidade Premium', desc: 'Equipamentos modernos revisados regularmente.' },
                                ]"
                                :key="adv.title"
                                :class="[
                                    'bg-white/[0.05] hover:bg-white/[0.09] border border-white/10 hover:border-[#0D9488]/60 rounded-2xl p-5 transition-all duration-300 cursor-default group',
                                    i % 2 !== 0 ? 'translate-y-8' : ''
                                ]"
                            >
                                <div class="w-10 h-10 rounded-xl bg-[#0D9488]/20 border border-[#0D9488]/30 flex items-center justify-center mb-3 group-hover:bg-[#0D9488]/35 transition-colors">
                                    <component :is="adv.icon" :size="19" class="text-[#2DD4BF]" />
                                </div>
                                <h3 class="text-white font-semibold text-sm mb-1.5">{{ adv.title }}</h3>
                                <p class="text-white/45 text-xs leading-relaxed">{{ adv.desc }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile advantages strip -->
                <div class="lg:hidden mt-10 grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <div
                        v-for="adv in [
                            { icon: Shield,     title: 'Transparência' },
                            { icon: Calendar,   title: 'Agenda Flexível' },
                            { icon: Users,      title: 'Time Capacitado' },
                            { icon: Heart,      title: 'Equipe Humana' },
                            { icon: Headphones, title: 'Suporte 24h' },
                            { icon: Award,      title: 'Qualidade Premium' },
                        ]"
                        :key="adv.title"
                        class="flex items-center gap-3 bg-white/[0.05] border border-white/10 rounded-xl px-4 py-3"
                    >
                        <component :is="adv.icon" :size="16" class="text-[#2DD4BF] flex-shrink-0" />
                        <span class="text-white/75 text-xs font-medium">{{ adv.title }}</span>
                    </div>
                </div>
            </div>

            <!-- Scroll indicator -->
            <button
                class="absolute bottom-8 left-1/2 -translate-x-1/2 text-white/30 hover:text-white/60 transition-colors animate-bounce"
                @click="scrollTo('products')"
            >
                <ChevronDown :size="32" />
            </button>
        </section>

        <!-- ===== PRODUCTS SECTION ===== -->
        <section id="products" class="py-24 bg-white" ref="productsRef">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center mb-16">
                    <span class="text-[#0D9488] text-sm font-semibold uppercase tracking-widest">Nosso Catálogo</span>
                    <h2 class="text-4xl lg:text-5xl font-bold text-[#0F172A] mt-3 mb-4" style="font-family: 'Playfair Display', serif;">
                        Equipamentos Disponíveis
                    </h2>
                    <p class="text-slate-500 max-w-xl mx-auto">
                        Máquinas de última geração para impulsionar o seu negócio de estética com qualidade e tecnologia.
                    </p>
                </div>

                <!-- Category Filters -->
                <div v-if="categories.length > 0" class="flex flex-wrap gap-2 justify-center mb-10">
                    <button
                        :class="[
                            'px-5 py-2 rounded-full text-sm font-medium transition-all',
                            activeCategory === null
                                ? 'bg-[#0F5D57] text-white shadow-lg shadow-teal-900/20'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                        ]"
                        @click="activeCategory = null"
                    >
                        Todos ({{ products.length }})
                    </button>
                    <button
                        v-for="cat in categories"
                        :key="cat.id"
                        :class="[
                            'px-5 py-2 rounded-full text-sm font-medium transition-all',
                            activeCategory === cat.id
                                ? 'bg-[#0F5D57] text-white shadow-lg shadow-teal-900/20'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                        ]"
                        @click="activeCategory = cat.id"
                    >
                        {{ cat.name }} ({{ cat.products_count }})
                    </button>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <article
                        v-for="product in filteredProducts"
                        :key="product.id"
                        class="group bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 overflow-hidden"
                    >
                        <!-- Image -->
                        <div class="relative overflow-hidden aspect-[4/3] bg-slate-50">
                            <img
                                v-if="product.main_image_url"
                                :src="product.main_image_url"
                                :alt="product.name"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                            <div
                                v-else
                                class="w-full h-full flex items-center justify-center"
                            >
                                <Package :size="48" class="text-slate-300" />
                            </div>
                            <!-- Availability Badge -->
                            <div class="absolute top-3 right-3">
                                <span :class="['text-xs font-semibold px-2.5 py-1 rounded-full', availabilityInfo[product.availability]?.color ?? 'bg-slate-100 text-slate-700']">
                                    {{ availabilityInfo[product.availability]?.label ?? product.availability }}
                                </span>
                            </div>
                            <!-- Category -->
                            <div v-if="product.category" class="absolute top-3 left-3">
                                <span class="bg-white/90 text-slate-700 text-xs font-medium px-2.5 py-1 rounded-full backdrop-blur-sm">
                                    {{ product.category.name }}
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <h3 class="font-bold text-lg text-[#0F172A] mb-2 line-clamp-2 group-hover:text-[#0F766E] transition-colors" style="font-family: 'Playfair Display', serif;">
                                {{ product.name }}
                            </h3>
                            <p v-if="product.short_description" class="text-slate-500 text-sm line-clamp-2 mb-4">
                                {{ product.short_description }}
                            </p>

                            <div v-if="product.price_per_day" class="flex items-baseline gap-1 mb-5">
                                <span class="text-xs text-slate-400">A partir de</span>
                                <span class="text-xl font-bold text-[#0F5D57]">
                                    R$ {{ Number(product.price_per_day).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}
                                </span>
                                <span class="text-xs text-slate-400">/dia</span>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <Link
                                    v-if="product.availability === 'available'"
                                    :href="route('booking.create', product.slug)"
                                    class="flex-1 bg-[#0F5D57] hover:bg-[#0D9488] text-white text-sm font-semibold py-2.5 px-4 rounded-xl text-center transition-colors"
                                >
                                    Reservar
                                </Link>
                                <span
                                    v-else
                                    class="flex-1 bg-slate-100 text-slate-400 text-sm font-semibold py-2.5 px-4 rounded-xl text-center cursor-not-allowed"
                                >
                                    Indisponível
                                </span>
                                <Link
                                    :href="route('products.show', product.slug)"
                                    class="flex-1 border border-slate-200 hover:border-[#0F766E] hover:text-[#0F766E] text-slate-600 text-sm font-semibold py-2.5 px-4 rounded-xl text-center transition-colors"
                                >
                                    Detalhes
                                </Link>
                            </div>
                        </div>
                    </article>

                    <!-- Empty State -->
                    <div v-if="filteredProducts.length === 0" class="col-span-full text-center py-20 text-slate-400">
                        <Package :size="48" class="mx-auto mb-4 opacity-30" />
                        <p class="text-lg">Nenhum produto encontrado</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== ABOUT SECTION ===== -->
        <section id="about" class="py-24 bg-[#F0FAFA]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <!-- Text -->
                    <div>
                        <span class="text-[#0D9488] text-sm font-semibold uppercase tracking-widest">Sobre Nós</span>
                        <h2 class="text-4xl lg:text-5xl font-bold text-[#0F172A] mt-3 mb-6 leading-tight" style="font-family: 'Playfair Display', serif;">
                            {{ settings.company_name ?? 'Renova Locação' }}
                        </h2>
                        <p class="text-slate-600 text-lg leading-relaxed mb-8">
                            {{ settings.company_about ?? 'Somos especialistas em locação de equipamentos de estética de alta performance.' }}
                        </p>
                        <ul class="space-y-4">
                            <li
                                v-for="item in [
                                    'Equipamentos de última geração',
                                    'Suporte técnico especializado',
                                    'Contratos flexíveis e personalizados',
                                    'Entrega e instalação inclusos',
                                ]"
                                :key="item"
                                class="flex items-center gap-3"
                            >
                                <CheckCircle :size="20" class="text-[#0D9488] flex-shrink-0" />
                                <span class="text-slate-700">{{ item }}</span>
                            </li>
                        </ul>
                        <a
                            :href="whatsappUrl"
                            target="_blank"
                            class="inline-flex items-center gap-2 bg-[#0F5D57] hover:bg-[#0D9488] text-white font-semibold px-8 py-4 rounded-full mt-10 transition-all hover:shadow-lg hover:shadow-teal-900/30 hover:-translate-y-0.5"
                        >
                            <MessageCircle :size="18" />
                            Solicitar Informações
                        </a>
                    </div>

                    <!-- Visual -->
                    <div class="relative">
                        <div class="bg-gradient-to-br from-[#0F5D57] to-[#0D9488] rounded-3xl p-10 text-white shadow-2xl">
                            <div class="grid grid-cols-2 gap-6">
                                <div
                                    v-for="stat in [
                                        { value: '100%', label: 'Satisfação garantida' },
                                        { value: '3+', label: 'Equipamentos premium' },
                                        { value: '24h', label: 'Suporte dedicado' },
                                        { value: 'Flex', label: 'Planos sob medida' },
                                    ]"
                                    :key="stat.label"
                                    class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-center"
                                >
                                    <p class="text-4xl font-bold mb-2" style="font-family: 'Playfair Display', serif;">{{ stat.value }}</p>
                                    <p class="text-teal-200 text-sm">{{ stat.label }}</p>
                                </div>
                            </div>
                        </div>
                        <!-- Decorative element -->
                        <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-[#2DD4BF]/20 rounded-2xl -z-10" />
                        <div class="absolute -top-4 -left-4 w-24 h-24 bg-[#0F5D57]/10 rounded-2xl -z-10" />
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== TESTIMONIALS SECTION ===== -->
        <section id="testimonials" class="py-24 bg-white" ref="testimonialsRef">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <span class="text-[#0D9488] text-sm font-semibold uppercase tracking-widest">Depoimentos</span>
                    <h2 class="text-4xl lg:text-5xl font-bold text-[#0F172A] mt-3 mb-4" style="font-family: 'Playfair Display', serif;">
                        O que dizem nossos clientes
                    </h2>
                    <p class="text-slate-500 max-w-xl mx-auto">
                        A satisfação dos nossos parceiros é a nossa maior conquista.
                    </p>
                </div>

                <div v-if="testimonials.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div
                        v-for="testimonial in testimonials"
                        :key="testimonial.id"
                        class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg transition-all p-8 flex flex-col"
                    >
                        <!-- Stars -->
                        <div class="flex gap-1 mb-5">
                            <Star
                                v-for="i in 5"
                                :key="i"
                                :size="16"
                                :class="i <= testimonial.rating ? 'text-yellow-400 fill-yellow-400' : 'text-slate-200 fill-slate-200'"
                            />
                        </div>
                        <!-- Quote -->
                        <p class="text-slate-600 text-base leading-relaxed flex-1 mb-6">
                            "{{ testimonial.testimonial }}"
                        </p>
                        <!-- Customer -->
                        <div class="flex items-center gap-3 pt-5 border-t border-slate-100">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#0F5D57] to-[#0D9488] flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                {{ testimonial.customer_name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <p class="font-semibold text-[#0F172A] text-sm">{{ testimonial.customer_name }}</p>
                                <p class="text-slate-400 text-xs">
                                    {{ [testimonial.customer_company, testimonial.customer_city].filter(Boolean).join(' · ') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-16 text-slate-400">
                    <Star :size="40" class="mx-auto mb-4 opacity-30" />
                    <p>Depoimentos em breve</p>
                </div>
            </div>
        </section>

        <!-- ===== CONTACT SECTION ===== -->
        <section id="contact" class="py-24 bg-gradient-to-br from-[#0A2721] via-[#0F5D57] to-[#0D9488]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14">
                    <span class="text-teal-300 text-sm font-semibold uppercase tracking-widest">Entre em Contato</span>
                    <h2 class="text-4xl lg:text-5xl font-bold text-white mt-3 mb-4" style="font-family: 'Playfair Display', serif;">
                        Pronto para começar?
                    </h2>
                    <p class="text-teal-100/80 max-w-xl mx-auto">
                        Fale conosco e saiba como podemos ajudar o seu negócio a crescer com os melhores equipamentos.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    <a
                        v-if="settings.whatsapp_number"
                        :href="whatsappUrl"
                        target="_blank"
                        class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-7 text-center hover:bg-white/20 transition-all group"
                    >
                        <MessageCircle :size="32" class="text-[#2DD4BF] mx-auto mb-4 group-hover:scale-110 transition-transform" />
                        <p class="text-white font-semibold mb-1">WhatsApp</p>
                        <p class="text-teal-200 text-sm">Fale conosco agora</p>
                    </a>
                    <a
                        v-if="settings.contact_email"
                        :href="'mailto:' + settings.contact_email"
                        class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-7 text-center hover:bg-white/20 transition-all group"
                    >
                        <Mail :size="32" class="text-[#2DD4BF] mx-auto mb-4 group-hover:scale-110 transition-transform" />
                        <p class="text-white font-semibold mb-1">E-mail</p>
                        <p class="text-teal-200 text-sm">{{ settings.contact_email }}</p>
                    </a>
                    <div
                        v-if="settings.contact_phone"
                        class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-7 text-center"
                    >
                        <Phone :size="32" class="text-[#2DD4BF] mx-auto mb-4" />
                        <p class="text-white font-semibold mb-1">Telefone</p>
                        <p class="text-teal-200 text-sm">{{ settings.contact_phone }}</p>
                    </div>
                </div>

                <div class="text-center">
                    <a
                        :href="whatsappUrl"
                        target="_blank"
                        class="inline-flex items-center gap-3 bg-white text-[#0F5D57] font-bold px-10 py-5 rounded-full text-lg transition-all hover:bg-teal-50 shadow-2xl hover:shadow-teal-900/40 hover:-translate-y-1"
                    >
                        <MessageCircle :size="22" />
                        Solicitar Orçamento pelo WhatsApp
                    </a>
                </div>
            </div>
        </section>

        <!-- ===== FOOTER ===== -->
        <footer class="bg-[#071F1C] text-slate-400 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gradient-to-br from-[#0F5D57] to-[#0D9488] rounded-xl flex items-center justify-center">
                            <span class="text-white font-bold text-xs">RL</span>
                        </div>
                        <div>
                            <p class="text-white font-semibold text-sm" style="font-family: 'Playfair Display', serif;">Renova Locação</p>
                            <p class="text-slate-500 text-xs">{{ settings.company_tagline ?? 'Equipamentos de Estética para Locação' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <a v-if="settings.instagram_url" :href="settings.instagram_url" target="_blank" class="hover:text-white transition-colors">
                            <Instagram :size="20" />
                        </a>
                        <a v-if="settings.facebook_url" :href="settings.facebook_url" target="_blank" class="hover:text-white transition-colors">
                            <Facebook :size="20" />
                        </a>
                    </div>
                    <p class="text-xs text-slate-500 text-center">
                        © {{ new Date().getFullYear() }} {{ settings.company_name ?? 'Renova Locação' }}. Todos os direitos reservados.
                    </p>
                </div>
            </div>
        </footer>

        <!-- ===== FLOATING WHATSAPP BUTTON ===== -->
        <a
            :href="whatsappUrl"
            target="_blank"
            class="fixed bottom-6 right-6 z-50 flex items-center gap-2 bg-[#25D366] hover:bg-[#20B858] text-white font-semibold px-5 py-3.5 rounded-full shadow-2xl hover:shadow-green-500/40 transition-all hover:-translate-y-1"
            aria-label="Falar pelo WhatsApp"
        >
            <MessageCircle :size="22" />
            <span class="text-sm hidden sm:inline">WhatsApp</span>
        </a>

    </div>
</template>
