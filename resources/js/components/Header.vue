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
                        to="/shop/calendar"
                        class="hover:underline"
                        :class="{
                            'font-bold underline text-primary-300':
                                isActive('/shop/calendar'),
                        }"
                        >📅 カレンダー</RouterLink
                    >

                    <RouterLink
                        to="/shop/settings/shops"
                        class="hover:underline"
                        :class="{
                            'font-bold underline text-primary-300': isActive(
                                '/shop/settings/shops'
                            ),
                        }"
                        >設定</RouterLink
                    >

                    <RouterLink
                        to="/shop/customers"
                        class="hover:underline"
                        :class="{
                            'font-bold underline text-primary-300':
                                isActive('/shop/customers'),
                        }"
                        >👤 顧客</RouterLink
                    >
                </nav>
                <div v-if="shop" class="relative">
                    <button
                        @click="toggleDropdown"
                        class="font-bold text-white border border-greige-300 hover:cursor-pointer hover:text-primary-300 bg-primary-500 px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-primary-300"
                    >
                        {{ shop.name }} ⏷
                    </button>
                    <div
                        v-if="dropdownOpen"
                        class="absolute right-0 mt-2 w-40 bg-greige-200 border rounded shadow z-10"
                    >
                        <button
                            @click="handleLogout"
                            class="block w-full text-left px-4 py-2 hover:bg-primary-100 hover:cursor-pointer"
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
import { ref, computed } from "vue";
import { useRoute } from "vue-router";
import "@/../css/components/Header.css";
import { useShopAuth } from "@/composables/useShopAuth";
import { useShopStore } from "@/stores/shop";
const route = useRoute();
const { logout, fetchShop } = useShopAuth();
const dropdownOpen = ref(false);
const shopStore = useShopStore();
const shop = computed(() => shopStore.shop);

function isActive(path) {
    return route.path.startsWith(path);
}
// onMounted(() => {
//     fetchShop(); // これが重要
//     console.log("Header mounted, shop:", shop);
// });

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
};

const handleLogout = async () => {
    await logout();
};
</script>
