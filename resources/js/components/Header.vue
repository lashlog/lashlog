<!-- resources/js/components/Header.vue -->
<template>
    <header class="header">
        <div class="header-inner">
            <div class="logo">
                <RouterLink to="/">Lash Log</RouterLink>
            </div>
            <div class="flex items-center space-x-6">
                <nav class="flex space-x-4" v-if="shop">
                    <RouterLink
                        to="/calendar"
                        class="hover:underline"
                        :class="{
                            'font-bold underline text-yellow-300':
                                isActive('/calendar'),
                        }"
                        >📅 カレンダー</RouterLink
                    >

                    <RouterLink
                        to="/shops"
                        class="hover:underline"
                        :class="{
                            'font-bold underline text-yellow-300':
                                isActive('/shops'),
                        }"
                        >設定</RouterLink
                    >

                    <RouterLink
                        to="/staffs"
                        class="hover:underline"
                        :class="{
                            'font-bold underline text-yellow-300':
                                isActive('/staffs'),
                        }"
                        >👩‍💼 スタッフ</RouterLink
                    >

                    <RouterLink
                        to="/menus"
                        class="hover:underline"
                        :class="{
                            'font-bold underline text-yellow-300':
                                isActive('/menus'),
                        }"
                        >📋 メニュー</RouterLink
                    >

                    <RouterLink
                        to="/customers"
                        class="hover:underline"
                        :class="{
                            'font-bold underline text-yellow-300':
                                isActive('/customers'),
                        }"
                        >👤 顧客</RouterLink
                    >
                </nav>
                <div v-if="shop" class="relative">
                    <button
                        @click="toggleDropdown"
                        class="font-bold text-gray-700"
                    >
                        {{ shop.name }} ⏷
                    </button>
                    <div
                        v-if="dropdownOpen"
                        class="absolute right-0 mt-2 w-40 bg-white border rounded shadow z-10"
                    >
                        <button
                            @click="handleLogout"
                            class="block w-full text-left px-4 py-2 hover:bg-gray-100"
                        >
                            🚪 ログアウト
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import "@/../css/components/Header.css";
import { useShopAuth } from "@/composables/useShopAuth";

const route = useRoute();
const { shop, logout, fetchShop } = useShopAuth();
const dropdownOpen = ref(false);

function isActive(path) {
    return route.path.startsWith(path);
}
onMounted(() => {
    fetchShop(); // これが重要
});

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
};

const handleLogout = async () => {
    await logout();
};
</script>
