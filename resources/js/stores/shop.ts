// resources/js/stores/shop.ts
import { defineStore } from "pinia";

type Shop = {
    id: number;
    name: string;
    // 他に必要なプロパティ
};
export const useShopStore = defineStore("shop", {
    state: () => ({
        shop: null,
    }),
    actions: {
        setShop(data: any) {
            this.shop = data;
        },
        clearShop() {
            this.shop = null;
        },
    },
});
