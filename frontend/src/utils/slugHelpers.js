import getSlug from 'speakingurl'

export function slugify(text) {
    return getSlug(text, { lang: 'vi' })
}

export function getIdFromSlug(slug, items, nameField = 'name', idField = 'id') {
    const item = items.find(item => slugify(item[nameField]) === slug)
    return item?.[idField] ?? ''
}

export function getSlugFromId(id, items, nameField = 'name', idField = 'id') {
    const item = items.find(item => Number(item[idField]) === Number(id))
    return item ? slugify(item[nameField]) : ''
}

export function extractIdFromSlug(slug) {
    // Tách số cuối cùng sau dấu '-'
    const parts = slug.split('-');
    const last = parts[parts.length - 1];
    // Nếu là số thì trả về số, nếu không thì trả về chuỗi cuối
    return /^\d+$/.test(last) ? Number(last) : last;
}

