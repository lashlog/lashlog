// resources/js/main.js
import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import { createPinia } from "pinia";
import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.min.css";
const app = createApp(App); // ← まず createApp
const pinia = createPinia(); // ← pinia 作成
app.component("Multiselect", Multiselect);
app.use(pinia); // ← 先に pinia を app に登録
app.use(router); // ← ルーター登録
app.mount("#app");
