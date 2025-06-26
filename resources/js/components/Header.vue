<!-- resources/js/components/Header.vue -->
<template>
    <header class="header">
        <div class="header-inner">
            <div class="logo">
                <RouterLink to="/">Lash Log</RouterLink>
            </div>
            <nav class="nav">
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
        </div>
    </header>
</template>

<script setup>
import { useRoute } from "vue-router";
import "@/../css/components/Header.css";
const route = useRoute();

function isActive(path) {
    return route.path.startsWith(path);
}
</script>
