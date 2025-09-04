import api from '@/configs/api'

// Cache permissions và role để tránh gọi API liên tục
let permissionsCache = null
let roleCache = null
let cacheExpiry = 0
const CACHE_DURATION = 5 * 60 * 1000 // 5 phút

export async function getUserPermissions() {
    const now = Date.now()
    
    // Kiểm tra cache
    if (permissionsCache && now < cacheExpiry) {
        return permissionsCache
    }
    
    const token = localStorage.getItem('token')
    if (!token) {
        throw new Error('No token found')
    }
    
    try {
        const response = await api.get('/role/user')
        const permissions = response.data.permissions.map(p => p.title)
        const role = response.data.role
        
        // Cập nhật cache
        permissionsCache = permissions
        roleCache = role
        cacheExpiry = now + CACHE_DURATION
        
        return permissions
    } catch (error) {
        // Clear cache nếu có lỗi
        permissionsCache = null
        roleCache = null
        cacheExpiry = 0
        throw error
    }
}

export async function getUserRole() {
    const now = Date.now()
    
    // Kiểm tra cache
    if (roleCache && now < cacheExpiry) {
        return roleCache
    }
    
    // Nếu chưa có cache, gọi getUserPermissions để load cả permissions và role
    await getUserPermissions()
    return roleCache
}

export function clearPermissionsCache() {
    permissionsCache = null
    roleCache = null
    cacheExpiry = 0
}

export function hasPermission(permissions, requiredPermission) {
    return permissions.includes(requiredPermission)
}

export function hasAllPermissions(permissions, requiredPermissions) {
    return requiredPermissions.every(permission => permissions.includes(permission))
}

export function hasAnyPermission(permissions, requiredPermissions) {
    return requiredPermissions.some(permission => permissions.includes(permission))
}
