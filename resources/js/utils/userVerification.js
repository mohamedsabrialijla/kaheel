/**
 * True when the user completed Yakeen / Nafath identity verification.
 */
export function isIdentityVerified(user) {
    if (!user) {
        return false;
    }
    const v = user.nafath_verified;
    return v === true || v === 1 || v === "1";
}
