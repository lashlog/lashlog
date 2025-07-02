import { ref } from "vue";
import axios from "axios";
import type { Router } from "vue-router";
const shop = ref<any>(null);

export function useShopAuth(router?: Router) {
    const login = async (email: string, password: string) => {
        await axios.get("/sanctum/csrf-cookie", { withCredentials: true });
        await axios.post(
            "/shop/login",
            { email, password },
            { withCredentials: true }
        );
        const res = await axios.get("/shop/me", { withCredentials: true });
        shop.value = res.data;
    };

    const logout = async () => {
        await axios.post("/shop/logout", {}, { withCredentials: true });
        shop.value = null;
    };
    const fetchShop = async () => {
        try {
            const res = await axios.get("/shop/me", {
                withCredentials: true,
            });
            shop.value = res.data;
        } catch (e: any) {
            shop.value = null;

            if (e.response?.status === 401) {
                // 未ログイン状態ならログインページに遷移
                // if (router) {
                //     router.push("/login");
                // }
            }
        }
    };
    return {
        shop,
        login,
        logout,
        fetchShop, // 👈 これを返す
    };
}
