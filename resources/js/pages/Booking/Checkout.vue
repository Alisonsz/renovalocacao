<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { MessageCircle, ArrowLeft, Package, CheckCircle, AlertCircle, Calendar, User, Mail, Phone, Building2, MapPin, FileText } from 'lucide-vue-next';

type Product = {
    id: number;
    name: string;
    slug: string;
    short_description: string | null;
    price_per_day: number | null;
    category: { name: string } | null;
    images: Array<{ url: string; alt_text: string | null }>;
};

const props = defineProps<{
    product: Product;
    settings: Record<string, string>;
    blockedRanges: Array<{
        start_date: string;
        end_date: string;
    }>;
}>();

const route = (name: string, params?: unknown) =>
    (globalThis as unknown as { route: (routeName: string, routeParams?: unknown) => string }).route(name, params);

const scrolled = ref(false);
function onScroll() { scrolled.value = window.scrollY > 50; }
onMounted(() => window.addEventListener('scroll', onScroll));
onUnmounted(() => window.removeEventListener('scroll', onScroll));

const mainImage = computed(() => props.product.images.find(i => true) ?? null);

const whatsappUrl = computed(() => {
    const num = props.settings.whatsapp_number ?? '55';
    const msg = encodeURIComponent(`Olá! Gostaria de solicitar uma reserva para: ${props.product.name}`);
    return `https://wa.me/${num}?text=${msg}`;
});

const form = useForm({
    product_id: props.product.id,
    customer_name: '',
    customer_email: '',
    customer_phone: '',
    customer_company: '',
    customer_zip_code: '',
    customer_city: '',
    customer_street: '',
    customer_number: '',
    customer_reference: '',
    start_date: '',
    end_date: '',
    message: '',
});

function overlaps(startA: string, endA: string, startB: string, endB: string): boolean {
    return startA <= endB && endA >= startB;
}

const selectedDateConflict = computed(() => {
    if (!form.start_date || !form.end_date) {
        return false;
    }

    return props.blockedRanges.some((range) => overlaps(
        form.start_date,
        form.end_date,
        range.start_date,
        range.end_date,
    ));
});

const conflictRangeLabel = computed(() => {
    if (!form.start_date || !form.end_date) {
        return null;
    }

    const conflict = props.blockedRanges.find((range) => overlaps(
        form.start_date,
        form.end_date,
        range.start_date,
        range.end_date,
    ));

    if (!conflict) {
        return null;
    }

    return `${formatDate(conflict.start_date)} até ${formatDate(conflict.end_date)}`;
});

function formatDate(date: string): string {
    return new Date(`${date}T00:00:00`).toLocaleDateString('pt-BR');
}

function submit() {
    if (selectedDateConflict.value) {
        form.setError('start_date', 'Este período conflita com uma reserva já confirmada. Escolha outra data.');
        return;
    }

    form.post(route('booking.store'));
}

// Phone mask
function maskPhone(e: Event) {
    const target = e.target as HTMLInputElement;
    let v = target.value.replace(/\D/g, '');
    v = v.slice(0, 11);
    if (v.length <= 11) {
        v = v.replace(/^(\d{2})(\d)/g,'($1) $2');
        v = v.replace(/(\d)(\d{4})$/,'$1-$2');
    }
    form.customer_phone = v;
}

function maskZipCode(e: Event) {
    const target = e.target as HTMLInputElement;
    let value = target.value.replace(/\D/g, '').slice(0, 8);

    if (value.length > 5) {
        value = `${value.slice(0, 5)}-${value.slice(5)}`;
    }

    form.customer_zip_code = value;
}

function normalizeAddressNumber(e: Event) {
    const target = e.target as HTMLInputElement;
    form.customer_number = target.value.replace(/[^0-9A-Za-z\-\/ ]/g, '').slice(0, 10);
}
</script>

<template>
    <Head>
        <title>Reservar: {{ product.name }}</title>
        <meta name="description" :content="`Solicite a reserva do equipamento ${product.name} - ${settings.company_name ?? 'Renova Locação'}`" />
        <meta name="robots" content="noindex" />
    </Head>

    <div class="booking-page min-h-screen bg-[#F8FAFC] font-sans antialiased">
        <!-- Navbar -->
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
                <Link :href="route('products.show', product.slug)" class="flex items-center gap-2 text-sm text-slate-600 hover:text-[#1E3A8A] transition-colors">
                    <ArrowLeft :size="16" />
                    Voltar ao Produto
                </Link>
            </div>
        </header>

        <div class="pt-24 pb-16">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Page header -->
                <div class="text-center mb-10">
                    <span class="inline-block bg-blue-100 text-[#1E3A8A] text-sm font-semibold px-4 py-1.5 rounded-full mb-3">
                        Solicitação de Reserva
                    </span>
                    <h1 class="text-3xl lg:text-4xl font-bold text-[#0F172A]" style="font-family: 'Playfair Display', serif;">
                        Reserve seu Equipamento
                    </h1>
                    <p class="text-slate-500 mt-2.5 max-w-xl mx-auto">
                        Preencha o formulário abaixo e entraremos em contato em até 24 horas para confirmar a disponibilidade.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Form -->
                    <div class="lg:col-span-2">
                        <form class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8" @submit.prevent="submit">
                            <!-- Personal info -->
                            <h2 class="text-base font-semibold text-[#0F172A] flex items-center gap-2 mb-5">
                                <User :size="17" class="text-[#3B82F6]" />
                                Seus Dados
                            </h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nome completo <span class="text-red-500">*</span></label>
                                    <input
                                        v-model="form.customer_name"
                                        type="text"
                                        placeholder="Seu nome completo"
                                        :class="['w-full border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#3B82F6] transition', form.errors.customer_name ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-white']"
                                        required
                                    />
                                    <p v-if="form.errors.customer_name" class="text-red-500 text-xs mt-1">{{ form.errors.customer_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                        <Mail :size="13" class="inline mr-1 text-slate-400" />
                                        E-mail <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.customer_email"
                                        type="email"
                                        placeholder="seu@email.com"
                                        :class="['w-full border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#3B82F6] transition', form.errors.customer_email ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-white']"
                                        required
                                    />
                                    <p v-if="form.errors.customer_email" class="text-red-500 text-xs mt-1">{{ form.errors.customer_email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                        <Phone :size="13" class="inline mr-1 text-slate-400" />
                                        Telefone / WhatsApp <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.customer_phone"
                                        type="text"
                                        inputmode="tel"
                                        placeholder="(00) 00000-0000"
                                        maxlength="16"
                                        :class="['w-full border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#3B82F6] transition', form.errors.customer_phone ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-white']"
                                        required
                                        @input="maskPhone"
                                    />
                                    <p v-if="form.errors.customer_phone" class="text-red-500 text-xs mt-1">{{ form.errors.customer_phone }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                        <Building2 :size="13" class="inline mr-1 text-slate-400" />
                                        Empresa / Clínica <span class="text-slate-400 text-xs">(opcional)</span>
                                    </label>
                                    <input
                                        v-model="form.customer_company"
                                        type="text"
                                        placeholder="Nome da empresa"
                                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#3B82F6] transition bg-white"
                                    />
                                </div>
                            </div>

                            <h2 class="text-base font-semibold text-[#0F172A] flex items-center gap-2 mb-5">
                                <MapPin :size="17" class="text-[#3B82F6]" />
                                Endereço para Entrega/Retirada
                            </h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                        CEP <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.customer_zip_code"
                                        type="text"
                                        inputmode="numeric"
                                        maxlength="9"
                                        placeholder="00000-000"
                                        :class="['w-full border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#3B82F6] transition', form.errors.customer_zip_code ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-white']"
                                        required
                                        @input="maskZipCode"
                                    />
                                    <p v-if="form.errors.customer_zip_code" class="text-red-500 text-xs mt-1">{{ form.errors.customer_zip_code }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                        Cidade <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.customer_city"
                                        type="text"
                                        maxlength="255"
                                        placeholder="Sua cidade"
                                        :class="['w-full border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#3B82F6] transition', form.errors.customer_city ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-white']"
                                        required
                                    />
                                    <p v-if="form.errors.customer_city" class="text-red-500 text-xs mt-1">{{ form.errors.customer_city }}</p>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                        Rua <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.customer_street"
                                        type="text"
                                        maxlength="255"
                                        placeholder="Rua, avenida ou travessa"
                                        :class="['w-full border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#3B82F6] transition', form.errors.customer_street ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-white']"
                                        required
                                    />
                                    <p v-if="form.errors.customer_street" class="text-red-500 text-xs mt-1">{{ form.errors.customer_street }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                        Número <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.customer_number"
                                        type="text"
                                        maxlength="10"
                                        placeholder="123 ou S/N"
                                        :class="['w-full border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#3B82F6] transition', form.errors.customer_number ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-white']"
                                        required
                                        @input="normalizeAddressNumber"
                                    />
                                    <p v-if="form.errors.customer_number" class="text-red-500 text-xs mt-1">{{ form.errors.customer_number }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                        Ponto de referência <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.customer_reference"
                                        type="text"
                                        maxlength="255"
                                        placeholder="Ex.: próximo à praça central"
                                        :class="['w-full border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#3B82F6] transition', form.errors.customer_reference ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-white']"
                                        required
                                    />
                                    <p v-if="form.errors.customer_reference" class="text-red-500 text-xs mt-1">{{ form.errors.customer_reference }}</p>
                                </div>
                            </div>

                            <!-- Period -->
                            <h2 class="text-base font-semibold text-[#0F172A] flex items-center gap-2 mb-4">
                                <Calendar :size="17" class="text-[#3B82F6]" />
                                Período de Locação
                            </h2>
                            <p class="text-xs text-slate-400 mb-3">
                                Informe o período desejado — confirmaremos a disponibilidade real em até 24h.
                            </p>
                            <div v-if="blockedRanges.length" class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-3">
                                <p class="text-xs font-semibold text-amber-800 mb-1">Períodos já confirmados (indisponíveis):</p>
                                <p class="text-xs text-amber-700 leading-relaxed">
                                    <span v-for="(range, index) in blockedRanges" :key="`${range.start_date}-${range.end_date}`">
                                        {{ formatDate(range.start_date) }} até {{ formatDate(range.end_date) }}<span v-if="index < blockedRanges.length - 1">, </span>
                                    </span>
                                </p>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Data de início <span class="text-red-500">*</span></label>
                                    <input
                                        v-model="form.start_date"
                                        type="date"
                                        :min="new Date().toISOString().slice(0, 10)"
                                        :class="['w-full border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#3B82F6] transition', form.errors.start_date ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-white']"
                                        required
                                    />
                                    <p v-if="form.errors.start_date" class="text-red-500 text-xs mt-1">{{ form.errors.start_date }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Data de término <span class="text-red-500">*</span></label>
                                    <input
                                        v-model="form.end_date"
                                        type="date"
                                        :min="form.start_date || new Date().toISOString().slice(0, 10)"
                                        :class="['w-full border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#3B82F6] transition', form.errors.end_date ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-white']"
                                        required
                                    />
                                    <p v-if="form.errors.end_date" class="text-red-500 text-xs mt-1">{{ form.errors.end_date }}</p>
                                </div>
                            </div>
                            <div v-if="selectedDateConflict" class="mb-6 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                                Esse período está indisponível. Conflito com reserva confirmada em {{ conflictRangeLabel }}.
                            </div>

                            <!-- Message -->
                            <h2 class="text-base font-semibold text-[#0F172A] flex items-center gap-2 mb-4">
                                <FileText :size="17" class="text-[#3B82F6]" />
                                Mensagem <span class="text-slate-400 text-sm font-normal">(opcional)</span>
                            </h2>
                            <textarea
                                v-model="form.message"
                                rows="4"
                                maxlength="1000"
                                placeholder="Descreva seu interesse, local de uso, dúvidas técnicas..."
                                class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#3B82F6] transition bg-white resize-none mb-7"
                            ></textarea>

                            <!-- Error summary -->
                            <div v-if="Object.keys(form.errors).length > 0" class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5 flex items-start gap-2">
                                <AlertCircle :size="18" class="text-red-500 flex-shrink-0 mt-0.5" />
                                <div>
                                    <p class="text-red-700 text-sm font-medium">Por favor, corrija os campos acima.</p>
                                </div>
                            </div>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full bg-[#1E3A8A] hover:bg-[#1D4ED8] disabled:opacity-60 text-white font-bold py-4 rounded-2xl text-base transition-all hover:shadow-lg hover:shadow-blue-900/30 hover:-translate-y-0.5 disabled:translate-y-0"
                            >
                                <span v-if="form.processing">Enviando...</span>
                                <span v-else>Enviar Solicitação de Reserva</span>
                            </button>

                            <p class="text-xs text-slate-400 text-center mt-3">
                                Ao enviar você concorda em ser contactado pela nossa equipe.
                            </p>
                        </form>
                    </div>

                    <!-- Sidebar: product card + trust signals -->
                    <div class="space-y-5">
                        <!-- Product card -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                            <div class="aspect-[4/3] bg-slate-50">
                                <img
                                    v-if="mainImage"
                                    :src="mainImage.url"
                                    :alt="mainImage.alt_text ?? product.name"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <Package :size="40" class="text-slate-300" />
                                </div>
                            </div>
                            <div class="p-5">
                                <span v-if="product.category" class="text-[#3B82F6] text-xs font-semibold">{{ product.category.name }}</span>
                                <h3 class="font-bold text-[#0F172A] text-base mt-0.5 mb-2" style="font-family: 'Playfair Display', serif;">
                                    {{ product.name }}
                                </h3>
                                <p v-if="product.short_description" class="text-slate-500 text-xs leading-relaxed mb-3">
                                    {{ product.short_description }}
                                </p>
                                <div v-if="product.price_per_day" class="border-t border-slate-100 pt-3">
                                    <span class="text-xs text-slate-400">A partir de</span>
                                    <div class="text-2xl font-bold text-[#1E3A8A]" style="font-family: 'Playfair Display', serif;">
                                        R$ {{ Number(product.price_per_day).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}
                                        <span class="text-sm font-normal text-slate-400">/dia</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Trust signals -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 space-y-4">
                            <h4 class="text-sm font-semibold text-[#0F172A]">Por que nos escolher?</h4>
                            <ul class="space-y-3">
                                <li v-for="item in [
                                    { icon: CheckCircle, text: 'Resposta em até 24 horas' },
                                    { icon: CheckCircle, text: 'Equipamentos revisados e certificados' },
                                    { icon: CheckCircle, text: 'Suporte técnico incluso' },
                                    { icon: CheckCircle, text: 'Contratos flexíveis' },
                                ]" :key="item.text" class="flex items-center gap-2 text-sm text-slate-600">
                                    <component :is="item.icon" :size="15" class="text-[#3B82F6] flex-shrink-0" />
                                    {{ item.text }}
                                </li>
                            </ul>
                        </div>

                        <!-- WhatsApp CTA -->
                        <a
                            :href="whatsappUrl"
                            target="_blank"
                            class="flex items-center justify-center gap-2.5 w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3.5 rounded-2xl transition-colors text-sm"
                        >
                            <MessageCircle :size="18" />
                            Prefere pelo WhatsApp?
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
