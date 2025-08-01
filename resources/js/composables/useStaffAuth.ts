import { ref } from "vue";
import axios from "axios";

const staff = ref(null);

export function useStaffAuth() {
    const login = async (email: string, password: string) => {
        await axios.get("/sanctum/csrf-cookie", { withCredentials: true });
        await axios.post(
            "/api/staff/login",
            { email, password },
            { withCredentials: true }
        );
        const res = await axios.get("/api/staff/me", { withCredentials: true });
        staff.value = res.data;
    };

    const logout = async () => {
        await axios.post("/api/staff/logout", {}, { withCredentials: true });
        staff.value = null;
    };
    const fetchStaff = async () => {
        try {
            const res = await axios.get("/api/staff/me", {
                withCredentials: true,
            });
            staff.value = res.data;
        } catch (e) {
            console.error("スタッフ情報の取得に失敗しました", e);
            staff.value = null;
        }
    };
    return {
        staff,
        login,
        logout,
        fetchStaff, // 👈 これを返す
    };
}
