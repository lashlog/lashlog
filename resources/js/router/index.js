import { createRouter, createWebHistory } from "vue-router";
import { h } from 'vue';

// 機能ごとのページを分かりやすくインポート
import ReservationPage from "../pages/shop/reservation/ReservationPage.vue";
import CalendarView from "../pages/shop/reservation/CalendarView.vue";
import SettingsLayout from "../pages/shop/settings/SettingsLayout.vue";
import StaffList from "../pages/shop/settings/staff/StaffList.vue";
import ShopForm from "../pages/shop/settings/shop/ShopForm.vue";
import MenuList from "../pages/shop/settings/menu/MenuList.vue";
import MenuCreate from "../pages/shop/settings/menu/MenuCreate.vue";
import MenuEdit from "../pages/shop/settings/menu/MenuEdit.vue";
import CustomerList from "../pages/shop/customer/CustomerList.vue";
import SalesList from "../pages/shop/sales/SalesList.vue";
import UserList from "../pages/customer/UserList.vue";
import CustomerLogin from "../pages/customer/Login.vue";
import CustomerRegister from "../pages/customer/Register.vue";
import Schedule from "../pages/shop/settings/schedule/Schedule.vue";
import ReservationSourceCreate from "../pages/shop/settings/reservation_source/ReservationSourceCreate.vue";
import ReservationSourceEdit from "../pages/shop/settings/reservation_source/ReservationSourceEdit.vue";
import ReservationSourceList from "../pages/shop/settings/reservation_source/ReservationSourceList.vue";
import Plans from "../pages/subscribe/Plans.vue";
import Complete from "../pages/subscribe/Complete.vue";
import Register from "../pages/subscribe/Register.vue";
import Dashboard from "../pages/subscribe/Dashboard.vue";

import Login from "../pages/shop/auth/Login.vue";
import { useShopAuth } from "../composables/useShopAuth";
import { useShopStore } from "@/stores/shop";
import StaffLogin from "../pages/staff/Login.vue";
import AttendancePage from "../pages/staff/AttendancePage.vue";
import ShiftPage from "../pages/staff/ShiftPage.vue";
import CalendarPage from "../pages/staff/CalendarPage.vue";
import ChartPage from "../pages/staff/ChartPage.vue";
import { useStaffAuth } from "../composables/useStaffAuth";
import { useStaffStore } from "@/stores/staff";
const routes = [
    { path: "/", redirect: "/plans" },
    { path: "/plans", component: Plans },
    { path: "/complete", component: Complete },
    { path: "/register", component: Register },
    { path: "/dashboard", component: Dashboard, meta: { requiresAuth: true } },
    { path: "/shop/login", component: Login },
    { path: "/staff/login", component: StaffLogin },
    { path: "/customer/login", component: CustomerLogin },
    { path: "/customer/register", component: CustomerRegister },
    { path: "/customer/users", component: UserList },
    {
        path: "/shop/calendar",
        component: ReservationPage,
        meta: { requiresAuth: true },
    },
    {
        path: "/shop/sales",
        component: SalesList,
        meta: { requiresAuth: true },
    },

    {
        path: "/shop/customers",
        name: "shop.customer.index",
        component: CustomerList,
        meta: { requiresAuth: true },
    },
    {
        path: "/shop/customers/create",
        name: "shop.customer.create",
        component: () => import("../pages/shop/customer/CustomerCreate.vue"),
        meta: { requiresAuth: true },
    },
    {
        path: "/shop/customers/:id/edit",
        name: "shop.customer.edit",
        component: () => import("../pages/shop/customer/CustomerEdit.vue"),
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
                name: "shop.settings.shops",
            },
            {
                path: "staffs",
                component: StaffList,
                meta: { requiresAuth: true },
                name: "shop.settings.staff.index",
            },
            {
                path: "staffs/create", // ← これを追加
                component: () =>
                    import("../pages/shop/settings/staff/StaffCreate.vue"),
                meta: { requiresAuth: true },
                name: "shop.settings.staff.create",
            },
            {
                path: "staffs/:id/edit",
                component: () =>
                    import("../pages/shop/settings/staff/StaffEdit.vue"),
                meta: { requiresAuth: true },
                name: "shop.settings.staff.edit",
            },
            {
                path: "menus",
                component: MenuList,
                meta: { requiresAuth: true },
                name: "shop.settings.menu.index",
            },
            {
                path: "menus/create",
                component: MenuCreate,
                meta: { requiresAuth: true },
                name: "shop.settings.menu.create",
            },
            {
                path: "menus/:id/edit",
                component: MenuEdit,
                meta: { requiresAuth: true },
                name: "shop.settings.menu.edit",
            },
            {
                path: "schedules",
                component: Schedule,
                meta: { requiresAuth: true },
                name: "shop.settings.schedule",
            },
            {
                path: "reservation-sources",
                component: ReservationSourceList,
                meta: { requiresAuth: true },
                name: "shop.settings.reservation_sources",
            },
            {
                path: "reservation-sources/:id/edit",
                component: ReservationSourceEdit,
                meta: { requiresAuth: true },
                name: "shop.settings.reservation_sources.edit",
            },
            {
                path: "reservation-sources/create",
                component: ReservationSourceCreate,
                meta: { requiresAuth: true },
                name: "shop.settings.reservation_sources.create",
            },
        ],
    },
    {
        path: "/staff",
        component: { render: () => h('router-view') },
        meta: { requiresStaffAuth: true },
        children: [
            { path: "attendance", component: AttendancePage },
            { path: "shift", component: ShiftPage },
            { path: "calendar", component: CalendarPage },
            { path: "chart", component: ChartPage }
        ]
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
    const staffStore = useStaffStore();
    const { fetchStaff } = useStaffAuth();
    console.log("ルート遷移:", to.path);

    if (to.meta.requiresAuth === true) {
        if (shopStore.shop === null) {
            try {
                await fetchShop();
            } catch (e) {
                console.warn("認証されていません。ログイン画面に遷移します");
            }
        }
        if (!shopStore.shop) {
            return next("/shop/login");
        }
    }
    if (to.meta.requiresStaffAuth === true) {
        if (staffStore.staff === null) {
            try {
                await fetchStaff();
            } catch (e) {
                console.warn("スタッフ未認証");
            }
        }
        if (!staffStore.staff) {
            return next("/staff/login");
        }
    }
    if (to.path === "/shop/login" && shopStore.shop) {
        return next("/shop/calendar");
    }
    if (to.path === "/staff/login" && staffStore.staff) {
        return next("/staff/attendance");
    }

    next();
});
export default router;
