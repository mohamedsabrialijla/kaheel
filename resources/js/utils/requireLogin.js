/**
 * Guard for actions that require authentication.
 * Shows login-required modal when guest.
 * @returns {boolean} true if logged in
 */
export function requireLogin(store, onAllowed) {
    const token = localStorage.getItem("token") || localStorage.getItem("authToken");
    const loggedIn = !!(token && token !== "null" && token !== "undefined");

    if (loggedIn) {
        if (typeof onAllowed === "function") {
            onAllowed();
        }
        return true;
    }

    store?.commit?.("showLoginRequired");
    return false;
}
