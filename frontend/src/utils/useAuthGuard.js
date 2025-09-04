import { ref, onMounted } from 'vue'
import { getUserPermissions, hasAllPermissions, clearPermissionsCache } from '@/utils/permissionUtils'

export function useAuthGuard(options = {}) {
    const isLoading = ref(false)
    const hasPermission = ref(true)
    const userPermissions = ref([])

    // Deprecated: Sử dụng router guard thay thế
    console.warn('useAuthGuard is deprecated. Use router meta.requiredPermissions instead.')

    onMounted(async () => {
        // Chỉ để backward compatibility
        if (options.requiredPermissions) {
            try {
                isLoading.value = true
                const permissions = await getUserPermissions()
                userPermissions.value = permissions
                hasPermission.value = hasAllPermissions(permissions, options.requiredPermissions)
            } catch (error) {
                console.error('Permission check failed:', error)
                hasPermission.value = false
            } finally {
                isLoading.value = false
            }
        }
    })

    return {
        isLoading,
        hasPermission,
        userPermissions
    }
}

// Utility function để clear cache khi logout
export function clearAuthCache() {
    clearPermissionsCache()
}