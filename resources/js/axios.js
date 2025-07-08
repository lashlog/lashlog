import axios from "axios";

import router from "@/router"; // routerを使う場合（Vue Routerを設定している前提）

axios.defaults.baseURL = "http://localhost:8000";
axios.defaults.withCredentials = true;
axios.defaults.headers.common["Accept"] = "application/json";

// 🚨 共通のエラーハンドリング（401対応）
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            // セッション切れ → ログイン画面へ
            router.push("/login");
        }
        return Promise.reject(error);
    }
);
export default axios;
