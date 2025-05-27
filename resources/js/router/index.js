import { createRouter, createWebHistory } from "vue-router";

// 各ページコンポーネントを読み込み
import CalendarView from "../components/CalendarView.vue";
import ShopList from "../components/ShopList.vue";
import StaffList from "../components/StaffList.vue";
import MenuList from "../components/MenuList.vue";
import CustomerList from "../components/CustomerList.vue";

const routes = [
    { path: "/", redirect: "/calendar" }, // デフォルトはカレンダーへ
    { path: "/calendar", component: CalendarView },
    { path: "/shops", component: ShopList },
    { path: "/staffs", component: StaffList },
    { path: "/menus", component: MenuList },
    { path: "/customers", component: CustomerList },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
