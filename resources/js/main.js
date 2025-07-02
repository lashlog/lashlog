// resources/js/main.js
import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import { createPinia } from "pinia";
const app = createApp(App); // ← まず createApp
const pinia = createPinia(); // ← pinia 作成
app.use(pinia); // ← 先に pinia を app に登録
app.use(router); // ← ルーター登録
app.mount("#app");
