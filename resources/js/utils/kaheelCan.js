import { mapGetters } from "vuex";

export function isKaheelFullAdmin(user) {
    if (!user) return false;
    return user.account_role === "admin" || (!user.staff_sort && user.account_role !== "admin_staff");
}

export function kaheelCan(user, code) {
    if (!code) return false;
    if (isKaheelFullAdmin(user)) return true;
    const list = Array.isArray(user?.role) ? user.role : [];
    return list.includes(code);
}

export const kaheelCanMixin = {
    computed: {
        ...mapGetters({ kaheelUser: "getUser" }),
        isFullAdmin() {
            return isKaheelFullAdmin(this.kaheelUser);
        },
    },
    methods: {
        can(code) {
            return kaheelCan(this.kaheelUser, code);
        },
    },
};
