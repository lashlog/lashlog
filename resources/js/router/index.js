import { createRouter, createWebHistory } from "vue-router";

// 機能ごとのページを分かりやすくインポート
import CalendarView from "../pages/Reservation/CalendarView.vue";
import ShopList from "../pages/Shop/ShopList.vue";
import StaffList from "../pages/Staff/StaffList.vue";
import ShopForm from "../pages/Shop/ShopForm.vue";
import MenuList from "../pages/Menu/MenuList.vue";
import CustomerList from "../pages/Customer/CustomerList.vue";

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
