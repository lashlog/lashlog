import axios from "axios";
import router from "@/router"; // routerを使う場合（Vue Routerを設定している前提）
import { useShopStore } from "@/stores/shop";

axios.defaults.baseURL = "http://localhost:8000";
axios.defaults.withCredentials = true;
axios.defaults.headers.common["Accept"] = "application/json";

// 🚨 共通のエラーハンドリング（401対応）
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            const shopStore = useShopStore(); // ✅ ストア取得
            shopStore.shop = null;
            // セッション切れ → ログイン画面へ
            router.push("/shop/login");
        }
        return Promise.reject(error);
    }
);
export default axios;
