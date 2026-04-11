<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Package, Tag, Calendar, Star, TrendingUp, Clock } from 'lucide-vue-next';

defineProps<{
    stats: {
        products: number;
        categories: number;
        bookings: number;
        pending: number;
        testimonials: number;
    };
    recentBookings: Array<{
        id: number;
        customer_name: string;
        product_name: string;
        start_date: string;
        end_date: string | null;
        status: string;
        status_label: string;
        created_at: string;
    }>;
}>();

const statusColors: Record<string, string> = {
    pending:   'bg-yellow-100 text-yellow-800',
    contacted: 'bg-blue-100 text-blue-800',
    confirmed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
};
</script>

<template>
    <AdminLayout>
        <Head title="Dashboard" />

        <template #header>
            <div>
                <h1 class="text-lg font-semibold text-slate-900">Dashboard</h1>
                <p class="text-sm text-slate-500">Visão geral do sistema</p>
            </div>
        </template>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-slate-500 text-sm font-medium">Produtos</span>
                    <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center">
                        <Package :size="18" class="text-blue-600" />
                    </div>
                </div>
                <p class="text-3xl font-bold text-slate-900">{{ stats.products }}</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-slate-500 text-sm font-medium">Categorias</span>
                    <div class="w-9 h-9 bg-purple-50 rounded-lg flex items-center justify-center">
                        <Tag :size="18" class="text-purple-600" />
                    </div>
                </div>
                <p class="text-3xl font-bold text-slate-900">{{ stats.categories }}</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-slate-500 text-sm font-medium">Reservas</span>
                    <div class="w-9 h-9 bg-green-50 rounded-lg flex items-center justify-center">
                        <Calendar :size="18" class="text-green-600" />
                    </div>
                </div>
                <p class="text-3xl font-bold text-slate-900">{{ stats.bookings }}</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-slate-500 text-sm font-medium">Pendentes</span>
                    <div class="w-9 h-9 bg-yellow-50 rounded-lg flex items-center justify-center">
                        <Clock :size="18" class="text-yellow-600" />
                    </div>
                </div>
                <p class="text-3xl font-bold text-slate-900">{{ stats.pending }}</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-slate-500 text-sm font-medium">Depoimentos</span>
                    <div class="w-9 h-9 bg-amber-50 rounded-lg flex items-center justify-center">
                        <Star :size="18" class="text-amber-600" />
                    </div>
                </div>
                <p class="text-3xl font-bold text-slate-900">{{ stats.testimonials }}</p>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="bg-white rounded-xl border border-slate-200">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h2 class="font-semibold text-slate-900 flex items-center gap-2">
                    <TrendingUp :size="18" class="text-blue-600" />
                    Reservas Recentes
                </h2>
                <a :href="route('admin.bookings.index')" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    Ver todas →
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/50">
                            <th class="text-left px-6 py-3 text-slate-500 font-medium">Cliente</th>
                            <th class="text-left px-6 py-3 text-slate-500 font-medium hidden md:table-cell">Produto</th>
                            <th class="text-left px-6 py-3 text-slate-500 font-medium hidden lg:table-cell">Data</th>
                            <th class="text-left px-6 py-3 text-slate-500 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="booking in recentBookings"
                            :key="booking.id"
                            class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors"
                        >
                            <td class="px-6 py-4">
                                <p class="font-medium text-slate-900">{{ booking.customer_name }}</p>
                                <p class="text-slate-400 text-xs">{{ booking.created_at }}</p>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell text-slate-600">
                                {{ booking.product_name }}
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell text-slate-600">
                                {{ booking.start_date }}
                                <span v-if="booking.end_date"> → {{ booking.end_date }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', statusColors[booking.status]]"
                                >
                                    {{ booking.status_label }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="!recentBookings.length">
                            <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                                Nenhuma reserva ainda
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
