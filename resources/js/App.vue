<!-- resources/js/App.vue -->
<template>
    <div>
        <Header />
        <main class="p-6">
            <router-view />
        </main>
    </div>
</template>

<script setup>
import Header from "./components/Header.vue";
</script>
