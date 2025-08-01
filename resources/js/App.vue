<!-- resources/js/App.vue -->
<template>
    <div>
        <Header />
        <main>
            <router-view />
        </main>
    </div>
</template>

<script setup>
import { useRouter } from "vue-router";
import { watch } from "vue";
import { useShopStore } from "@/stores/shop";
import { useStaffStore } from "@/stores/staff";
import Header from "./components/Header.vue";

const router = useRouter();
const shopStore = useShopStore();
const staffStore = useStaffStore();

// 👇 状態を常時監視する
watch(
    () => shopStore.shop,
    (newVal) => {
        const path = router.currentRoute.value.path;
        if (!newVal && path.startsWith('/shop')) {
            router.push('/shop/login');
        }
    },
    { immediate: true }
);
watch(
    () => staffStore.staff,
    (val) => {
        if (!val && router.currentRoute.value.path.startsWith('/staff') && router.currentRoute.value.path !== '/staff/login') {
            router.push('/staff/login');
        }
    },
    { immediate: true }
);
</script>
