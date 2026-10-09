/**
 * Extract numeric user id from plain id or composite id (e.g. user_123).
 */
export function parseUserId(id) {
    if (id == null || id === "") {
        return null;
    }
    const str = String(id);
    const parts = str.split("_");
    const raw = parts.length > 1 ? parts.slice(1).join("_") : str;
    const num = Number(raw);
    return Number.isFinite(num) ? num : null;
}

/**
 * Navigate to seller profile (or my-page if same user).
 */
export function navigateToUserProfile(router, locale, currentUser, targetUser) {
    const id = parseUserId(targetUser?.id);
    if (!id || !router) {
        return;
    }

    const loc = locale || localStorage.getItem("locale") || "en";
    const isSelf =
        currentUser?.id &&
        Number(currentUser.id) === Number(targetUser.id);

    if (isSelf) {
        router.push({ path: `/${loc}/my-page` });
        return;
    }

    router.push({
        path: `/${loc}/seller-page`,
        query: { user_id: id },
    });
}
