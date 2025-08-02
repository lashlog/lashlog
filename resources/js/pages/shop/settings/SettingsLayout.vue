<script setup>
import { useRoute } from "vue-router";

const route = useRoute();

const navItems = [
    { to: "/shop/settings/shops", label: "店舗情報" },
    { to: "/shop/settings/staffs", label: "スタッフ管理" },
    { to: "/shop/settings/schedules", label: "営業日設定" },
    { to: "/shop/settings/menus", label: "メニュー管理" },
    { to: "/shop/settings/reservation-sources", label: "予約元管理" },
    { to: "/shop/settings/discount-rules", label: "割引設定" },
];

const isActive = (path) => {
    return route.path.startsWith(path);
};
</script>

<template>
    <div class="flex min-h-screen">
        <aside class="w-64 bg-greige-100 border-r border-greige-500 p-6">
            <h2 class="text-xl font-bold mb-6">設定メニュー</h2>
            <ul class="space-y-4">
                <li v-for="item in navItems" :key="item.to">
                    <RouterLink :to="item.to">
                        <span
                            :class="[
                                isActive(item.to)
                                    ? 'text-primary-500 font-semibold'
                                    : 'text-gray-700',
                                'hover:underline',
                            ]"
                        >
                            {{ item.label }}
                        </span>
                    </RouterLink>
                </li>
            </ul>
        </aside>
        <main class="w-3/4 p-6">
            <router-view />
        </main>
    </div>
</template>
