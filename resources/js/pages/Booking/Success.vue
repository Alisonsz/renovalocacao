<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { MessageCircle, CheckCircle2, Home, ArrowRight } from 'lucide-vue-next';

const props = defineProps<{
    settings: Record<string, string>;
    bookingReference: string | null;
}>();

const route = (name: string, params?: unknown) =>
    (globalThis as unknown as { route: (routeName: string, routeParams?: unknown) => string }).route(name, params);

const whatsappUrl = (() => {
    const num = props.settings.whatsapp_number ?? '55';
    const referenceLabel = props.bookingReference ? ` Número da reserva: #${props.bookingReference}.` : '';
    const msg = encodeURIComponent(`Olá! Acabei de enviar uma solicitação de reserva pelo site.${referenceLabel}`);
    return `https://wa.me/${num}?text=${msg}`;
})();
</script>

<template>
    <Head>
        <title>Reserva Solicitada – {{ settings.company_name ?? 'Renova Locação' }}</title>
        <meta name="robots" content="noindex" />
    </Head>

    <div class="min-h-screen bg-gradient-to-br from-[#071F1C] via-[#0F5D57] to-[#0D9488] flex flex-col font-sans antialiased">
        <!-- Minimal header -->
        <header class="py-5 px-6">
            <Link :href="route('home')" class="inline-flex items-center gap-3">
                <div class="w-9 h-9 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-xs">RL</span>
                </div>
                <span class="font-bold text-white" style="font-family: 'Playfair Display', serif;">
                    {{ settings.company_name ?? 'Renova Locação' }}
                </span>
            </Link>
        </header>

        <!-- Main content -->
        <main class="flex-1 flex items-center justify-center px-4 py-16">
            <div class="max-w-lg w-full text-center">
                <!-- Success icon with pulse ring -->
                <div class="relative inline-block mb-8">
                    <div class="absolute inset-0 bg-white/30 rounded-full animate-ping" />
                    <div class="relative w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-2xl">
                        <CheckCircle2 :size="48" class="text-[#0F5D57]" />
                    </div>
                </div>

                <h1 class="text-4xl lg:text-5xl font-bold text-white mb-4 leading-tight" style="font-family: 'Playfair Display', serif;">
                    Reserva Solicitada!
                </h1>
                <p class="text-teal-100 text-lg mb-2">
                    Sua solicitação foi recebida com sucesso.
                </p>
                <p class="text-teal-100/80 text-base mb-10 max-w-md mx-auto leading-relaxed">
                    Nossa equipe entrará em contato em até <strong class="text-white">24 horas</strong> para confirmar a disponibilidade e discutir os detalhes da locação.
                </p>

                <div v-if="bookingReference" class="mb-8 bg-white/15 border border-white/30 backdrop-blur rounded-2xl p-4 max-w-sm mx-auto">
                    <p class="text-teal-200 text-xs uppercase tracking-wider">Número da reserva</p>
                    <p class="text-2xl font-bold text-white mt-1">#{{ bookingReference }}</p>
                    <p class="text-teal-100 text-xs mt-1">Informe esse número ao falar com nossa equipe.</p>
                </div>

                <!-- Info cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-10">
                    <div v-for="item in [
                        { label: 'Prazo de resposta', value: 'Até 24 horas' },
                        { label: 'Confirmação via', value: 'E-mail ou Telefone' },
                        { label: 'Suporte', value: 'Via WhatsApp' },
                    ]" :key="item.label" class="bg-white/10 backdrop-blur rounded-xl p-4 border border-white/10">
                        <p class="text-teal-200 text-xs mb-1">{{ item.label }}</p>
                        <p class="text-white font-semibold text-sm">{{ item.value }}</p>
                    </div>
                </div>

                <!-- CTAs -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <Link
                        :href="route('home')"
                        class="inline-flex items-center justify-center gap-2 bg-white text-[#0F5D57] hover:bg-teal-50 font-bold py-4 px-8 rounded-2xl transition-all hover:shadow-xl hover:-translate-y-0.5"
                    >
                        <Home :size="18" />
                        Voltar ao Início
                    </Link>
                    <a
                        :href="whatsappUrl"
                        target="_blank"
                        class="inline-flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-8 rounded-2xl transition-all hover:shadow-xl hover:-translate-y-0.5"
                    >
                        <MessageCircle :size="18" />
                        Falar pelo WhatsApp
                    </a>
                </div>
            </div>
        </main>

        <footer class="py-5 text-center text-teal-200/80 text-xs">
            © {{ new Date().getFullYear() }} {{ settings.company_name ?? 'Renova Locação' }}. Todos os direitos reservados.
        </footer>
    </div>
</template>
