import { createRouter, createWebHistory } from "vue-router";

// 機能ごとのページを分かりやすくインポート
import CalendarView from "../pages/admin/Reservation/CalendarView.vue";
import ShopList from "../pages/admin/Shop/ShopList.vue";
import StaffList from "../pages/admin/Staff/StaffList.vue";
import ShopForm from "../pages/admin/Shop/ShopForm.vue";
import MenuList from "../pages/admin/Menu/MenuList.vue";
import CustomerList from "../pages/admin/Customer/CustomerList.vue";
import Login from "../pages/admin/Auth/Login.vue";
import { useShopAuth } from "../composables/useShopAuth";
import { useShopStore } from "@/stores/shop";
const routes = [
    { path: "/", redirect: "/login" },
    { path: "/login", component: Login },
    {
        path: "/calendar",
        component: CalendarView,
        meta: { requiresAuth: true },
    },
    { path: "/shops", component: ShopForm, meta: { requiresAuth: true } },
    { path: "/staffs", component: StaffList, meta: { requiresAuth: true } },
    { path: "/menus", component: MenuList, meta: { requiresAuth: true } },
    {
        path: "/customers",
        component: CustomerList,
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});
// ✅ ログインチェック用グローバルガード
router.beforeEach(async (to, from, next) => {
    const shopStore = useShopStore();
    const { fetchShop } = useShopAuth();
    console.log("ルート変更:", to.path);
    console.log("shopStore.shop:", shopStore.shop);
    if (to.meta.requiresAuth === true) {
        if (shopStore.shop === null) {
            try {
                await fetchShop();
            } catch (e) {
                console.warn("認証されていません。ログイン画面に遷移します");
            }
        }
        if (!shopStore.shop) {
            return next("/login");
        }
    }

    if (to.path === "/login" && shopStore.shop) {
        return next("/calendar");
    }

    next();
});
export default router;
