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
import Header from "./components/Header.vue";

const router = useRouter();
const shopStore = useShopStore();

// 👇 状態を常時監視する
watch(
    () => shopStore.shop,
    (newVal) => {
        if (!newVal && router.currentRoute.value.path !== "/login") {
            router.push("/login");
        }
    },
    { immediate: true }
);
</script>
