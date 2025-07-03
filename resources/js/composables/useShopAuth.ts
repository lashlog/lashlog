import { ref } from "vue";
import axios from "axios";
import type { Router } from "vue-router";
import { useShopStore } from "@/stores/shop";

export function useShopAuth(router?: Router) {
    const shopStore = useShopStore();

    const login = async (email: string, password: string) => {
        await axios.get("/sanctum/csrf-cookie", { withCredentials: true });
        await axios.post(
            "api/shop/login",
            { email, password },
            { withCredentials: true }
        );
        const res = await axios.get("api/shop/me", { withCredentials: true });
        shopStore.setShop(res.data);
    };

    const logout = async () => {
        await axios.post("api/shop/logout", {}, { withCredentials: true });
        shopStore.clearShop();
        if (router) {
            router.push("/login");
        }
    };
    const fetchShop = async () => {
        try {
            const res = await axios.get("api/shop/me", {
                withCredentials: true,
            });
            console.log("Fetched shop:", res.data);
            shopStore.setShop(res.data);
        } catch (e: any) {
            shopStore.clearShop();
            if (e.response?.status === 401) {
                if (router) {
                    router.push("/login");
                }
            }
        }
    };
    return {
        shop: shopStore.shop,
        login,
        logout,
        fetchShop, // 👈 これを返す
    };
}
