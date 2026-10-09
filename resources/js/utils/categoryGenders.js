/**
 * Load genders that have subcategory data for a category (withGender=1).
 * Expected items shape:
 * { mens: { id, visible, name }, woman: { id, visible, name } }
 */
export async function fetchAvailableGenders(http, categoryId) {
    if (!categoryId) {
        return [];
    }

    const availRes = await http.get("basic/getCategories", {
        params: { category_id: categoryId, withGender: 1 },
    });

    const avail = availRes?.data?.items || {};
    const list = [];

    const isGenderObject = (entry) =>
        entry && typeof entry === "object" && ("visible" in entry || "id" in entry);

    // New API shape
    if (isGenderObject(avail.mens) || isGenderObject(avail.woman)) {
        const pushIfVisible = (entry, slug) => {
            if (!isGenderObject(entry)) {
                return;
            }
            if (Number(entry.visible) !== 1 || !entry.id) {
                return;
            }
            list.push({
                id: entry.id,
                name: entry.name || "",
                slug,
            });
        };

        pushIfVisible(avail.mens, "male");
        pushIfVisible(avail.woman, "female");
        return list;
    }

    // Legacy flat flags: mens:0|1 / woman:0|1
    const gendersRes = await http.get("basic/getGenders", {
        params: { category_id: categoryId },
    });
    const allGenders = gendersRes?.data?.items || [];

    return allGenders.filter((g) => {
        const slug = String(g.slug || "").toLowerCase();
        if (["male", "mens", "m", "man", "men"].includes(slug)) {
            return Number(avail.mens) === 1;
        }
        if (["female", "woman", "f", "women"].includes(slug)) {
            return Number(avail.woman) === 1;
        }
        return false;
    });
}
