<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ref, computed } from 'vue';
import { ArrowLeft, ChevronDown, ChevronUp } from 'lucide-vue-next';

type Booking = {
    id: number;
    customer_name: string;
    customer_email: string;
    customer_phone: string;
    customer_city: string | null;
    product_name: string | null;
    product_id: number;
    start_date: string;
    end_date: string | null;
    message: string | null;
    status: string;
    status_label: string;
    admin_notes: string | null;
    created_at: string;
};

const props = defineProps<{ bookings: Booking[] }>();

const statusColors: Record<string, string> = {
    pending:   'bg-yellow-100 text-yellow-800',
    contacted: 'bg-blue-100 text-blue-800',
    confirmed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
};

const selectedBooking = ref<Booking | null>(null);

const editForm = useForm({
    status: '',
    admin_notes: '',
});

function openEdit(booking: Booking) {
    selectedBooking.value = booking;
    editForm.status = booking.status;
    editForm.admin_notes = booking.admin_notes ?? '';
}

function updateBooking() {
    if (!selectedBooking.value) return;
    editForm.put(route('admin.bookings.update', selectedBooking.value.id), {
        onSuccess: () => { selectedBooking.value = null; },
    });
}

const filterStatus = ref('all');
const filtered = computed(() =>
    filterStatus.value === 'all'
        ? props.bookings
        : props.bookings.filter(b => b.status === filterStatus.value)
);
</script>

<template>
    <AdminLayout>
        <Head title="Reservas" />

        <template #header>
            <div class="flex items-center justify-between w-full">
                <div>
                    <h1 class="text-lg font-semibold text-slate-900">Reservas</h1>
                    <p class="text-sm text-slate-500">{{ bookings.length }} solicitação(ões)</p>
                </div>
            </div>
        </template>

        <!-- Filter Tabs -->
        <div class="flex gap-2 mb-4 flex-wrap">
            <button
                v-for="{ key, label } in [
                    { key: 'all', label: 'Todas' },
                    { key: 'pending', label: 'Pendentes' },
                    { key: 'contacted', label: 'Contactados' },
                    { key: 'confirmed', label: 'Confirmados' },
                    { key: 'cancelled', label: 'Cancelados' },
                ]"
                :key="key"
                :class="[
                    'px-4 py-1.5 rounded-full text-sm font-medium transition-colors',
                    filterStatus === key
                        ? 'bg-blue-600 text-white'
                        : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'
                ]"
                @click="filterStatus = key"
            >
                {{ label }}
            </button>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/50">
                            <th class="text-left px-6 py-3 text-slate-500 font-medium">Cliente</th>
                            <th class="text-left px-6 py-3 text-slate-500 font-medium hidden md:table-cell">Produto</th>
                            <th class="text-left px-6 py-3 text-slate-500 font-medium hidden lg:table-cell">Período</th>
                            <th class="text-left px-6 py-3 text-slate-500 font-medium">Status</th>
                            <th class="text-right px-6 py-3 text-slate-500 font-medium">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="booking in filtered" :key="booking.id">
                            <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-slate-900">{{ booking.customer_name }}</p>
                                    <p class="text-slate-400 text-xs">{{ booking.customer_email }}</p>
                                    <p class="text-slate-400 text-xs">{{ booking.customer_phone }}</p>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell text-slate-600">{{ booking.product_name }}</td>
                                <td class="px-6 py-4 hidden lg:table-cell text-slate-600">
                                    {{ booking.start_date }}
                                    <span v-if="booking.end_date"> → {{ booking.end_date }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', statusColors[booking.status]]">
                                        {{ booking.status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button
                                        class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                                        @click="openEdit(booking)"
                                    >
                                        Atualizar
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="booking.message" class="border-b border-slate-50 bg-slate-50/30">
                                <td colspan="5" class="px-6 py-2">
                                    <span class="text-xs text-slate-500">Mensagem:</span>
                                    <span class="text-xs text-slate-700 ml-1">{{ booking.message }}</span>
                                </td>
                            </tr>
                        </template>
                        <tr v-if="!filtered.length">
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                Nenhuma reserva encontrada.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Modal -->
        <div
            v-if="selectedBooking"
            class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
            @click.self="selectedBooking = null"
        >
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Atualizar Reserva</h3>
                <p class="text-sm text-slate-500 mb-4">
                    <strong>{{ selectedBooking.customer_name }}</strong> — {{ selectedBooking.product_name }}
                </p>
                <form @submit.prevent="updateBooking" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
                        <select v-model="editForm.status" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="pending">Pendente</option>
                            <option value="contacted">Contactado</option>
                            <option value="confirmed">Confirmado</option>
                            <option value="cancelled">Cancelado</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Notas Internas</label>
                        <textarea v-model="editForm.admin_notes" rows="3" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none" placeholder="Observações internas..." />
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" :disabled="editForm.processing" class="flex-1 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white font-medium py-2.5 rounded-lg text-sm transition-colors">
                            Salvar
                        </button>
                        <button type="button" class="flex-1 border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium py-2.5 rounded-lg text-sm transition-colors" @click="selectedBooking = null">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
