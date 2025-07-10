import { createRouter, createWebHistory } from "vue-router";

// 機能ごとのページを分かりやすくインポート
import CalendarView from "../pages/shop/reservation/CalendarView.vue";
import SettingsLayout from "../pages/shop/settings/SettingsLayout.vue";
import StaffList from "../pages/shop/settings/staff/StaffList.vue";
import ShopForm from "../pages/shop/settings/shop/ShopForm.vue";
import MenuList from "../pages/shop/settings/menu/MenuList.vue";
import MenuCreate from "../pages/shop/settings/menu/MenuCreate.vue";
import MenuEdit from "../pages/shop/settings/menu/MenuEdit.vue";
import CustomerList from "../pages/shop/customer/CustomerList.vue";
import Schedule from "../pages/shop/settings/schedule/Schedule.vue";

import Login from "../pages/shop/auth/Login.vue";
import { useShopAuth } from "../composables/useShopAuth";
import { useShopStore } from "@/stores/shop";
const routes = [
    { path: "/", redirect: "/shop/login" },
    { path: "/shop/login", component: Login },
    {
        path: "/shop/calendar",
        component: CalendarView,
        meta: { requiresAuth: true },
    },

    {
        path: "/shop/customers",
        component: CustomerList,
        meta: { requiresAuth: true },
    },
    {
        path: "/customers/create",
        name: "shop.customer.create",
        component: () => import("../pages/shop/customer/CustomerCreate.vue"),
        meta: { requiresAuth: true },
    },
    // ✅ 設定系をグループ化
    {
        path: "/shop/settings",
        component: SettingsLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: "shops",
                component: ShopForm,
                meta: { requiresAuth: true },
            },
            {
                path: "staffs",
                component: StaffList,
                meta: { requiresAuth: true },
            },
            {
                path: "staffs/create", // ← これを追加
                component: () =>
                    import("../pages/shop/settings/staff/StaffCreate.vue"),
                meta: { requiresAuth: true },
            },
            {
                path: "staffs/:id/edit",
                component: () =>
                    import("../pages/shop/settings/staff/StaffEdit.vue"),
                meta: { requiresAuth: true },
            },
            {
                path: "menus",
                component: MenuList,
                meta: { requiresAuth: true },
            },
            {
                path: "menus/create",
                component: MenuCreate,
                meta: { requiresAuth: true },
            },
            {
                path: "menus/:id/edit",
                component: MenuEdit,
                meta: { requiresAuth: true },
            },
            {
                path: "schedules",
                component: Schedule,
                meta: { requiresAuth: true },
            },
        ],
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
