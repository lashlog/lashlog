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
    const { shop, fetchShop } = useShopAuth(router);
    if (to.meta.requiresAuth === true) {
        console.log("test", shop.value);
        // 初期状態ではshopがnullなので、明示的にチェック
        if (shop.value === null) {
            try {
                await fetchShop();
            } catch (e) {
                console.warn("認証されていません。ログイン画面に遷移します");
            }
        }
        if (!shop.value) {
            return next("/login");
        }
    }
    // ログイン済みで /login に来た場合はカレンダーに飛ばす
    if (to.path === "/login" && shop.value) {
        return next("/calendar");
    }
    next();
});
export default router;
