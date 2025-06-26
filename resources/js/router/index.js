import { createRouter, createWebHistory } from "vue-router";

// 機能ごとのページを分かりやすくインポート
import CalendarView from "../pages/admin/Reservation/CalendarView.vue";
import ShopList from "../pages/admin/Shop/ShopList.vue";
import StaffList from "../pages/admin/Staff/StaffList.vue";
import ShopForm from "../pages/admin/Shop/ShopForm.vue";
import MenuList from "../pages/admin/Menu/MenuList.vue";
import CustomerList from "../pages/admin/Customer/CustomerList.vue";

const routes = [
    { path: "/", redirect: "/calendar" },
    { path: "/calendar", component: CalendarView },
    { path: "/shops", component: ShopForm },
    { path: "/staffs", component: StaffList },
    { path: "/menus", component: MenuList },
    { path: "/customers", component: CustomerList },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
