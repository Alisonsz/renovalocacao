<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    LayoutDashboard, Package, Tag, Users, Star, Settings,
    Code2, Menu, X, ChevronRight, LogOut, Calendar
} from 'lucide-vue-next';

const page = usePage();
const sidebarOpen = ref(false);

const navItems = [
    { label: 'Dashboard',    icon: LayoutDashboard, route: 'admin.dashboard' },
    { label: 'Produtos',     icon: Package,         route: 'admin.products.index' },
    { label: 'Categorias',   icon: Tag,             route: 'admin.categories.index' },
    { label: 'Reservas',     icon: Calendar,        route: 'admin.bookings.index' },
    { label: 'Depoimentos',  icon: Star,            route: 'admin.testimonials.index' },
    { label: 'Pixels/Codes', icon: Code2,           route: 'admin.tracking-codes.index' },
    { label: 'Configurações',icon: Settings,        route: 'admin.settings.index' },
];

const currentRoute = computed(() => page.props.routeName as string ?? '');

function isActive(route: string): boolean {
    return currentRoute.value?.startsWith(route.replace('.index', '').replace('.create', '').replace('.edit', ''));
}
</script>

<template>
    <div class="admin-panel flex h-screen bg-slate-50 font-sans overflow-hidden">
        <!-- Sidebar Overlay (mobile) -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 bg-black/50 z-20 lg:hidden"
            @click="sidebarOpen = false"
        />

        <!-- Sidebar -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-30 w-64 bg-[#0F172A] flex flex-col transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:z-auto',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full'
            ]"
        >
            <!-- Logo -->
            <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
                <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-sm">RL</span>
                </div>
                <div>
                    <p class="text-white font-semibold text-sm leading-tight">Renova Locação</p>
                    <p class="text-slate-400 text-xs">Painel Administrativo</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 overflow-y-auto">
                <ul class="space-y-1">
                    <li v-for="item in navItems" :key="item.route">
                        <Link
                            :href="route(item.route)"
                            :class="[
                                'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group',
                                isActive(item.route)
                                    ? 'bg-blue-600 text-white'
                                    : 'text-slate-300 hover:bg-white/10 hover:text-white'
                            ]"
                            @click="sidebarOpen = false"
                        >
                            <component :is="item.icon" :size="18" />
                            <span>{{ item.label }}</span>
                            <ChevronRight :size="14" class="ml-auto opacity-0 group-hover:opacity-100 transition-opacity" />
                        </Link>
                    </li>
                </ul>
            </nav>

            <!-- Footer -->
            <div class="px-3 py-4 border-t border-white/10">
                <Link
                    :href="route('home')"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 hover:bg-white/10 hover:text-white transition-all"
                    target="_blank"
                >
                    <ChevronRight :size="18" />
                    <span>Ver Site</span>
                </Link>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 hover:bg-red-500/20 hover:text-red-300 transition-all mt-1"
                >
                    <LogOut :size="18" />
                    <span>Sair</span>
                </Link>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white border-b border-slate-200 px-4 lg:px-6 py-4 flex items-center gap-4 flex-shrink-0">
                <button
                    class="lg:hidden p-2 rounded-lg hover:bg-slate-100 transition-colors"
                    @click="sidebarOpen = !sidebarOpen"
                >
                    <component :is="sidebarOpen ? X : Menu" :size="20" />
                </button>
                <slot name="header" />
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                <!-- Flash messages -->
                <div
                    v-if="($page.props.flash as any)?.success"
                    class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2"
                >
                    <span>✓</span>
                    {{ ($page.props.flash as any).success }}
                </div>
                <div
                    v-if="($page.props.flash as any)?.error"
                    class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2"
                >
                    <span>✗</span>
                    {{ ($page.props.flash as any).error }}
                </div>
                <slot />
            </main>
        </div>
    </div>
</template>
