import { ref, onMounted } from 'vue'
import { 
    getUserPermissions, 
    hasPermission as checkPermission,
    hasAllPermissions as checkAllPermissions,
    hasAnyPermission as checkAnyPermissions 
} from '@/utils/permissionUtils'

export function usePermissions() {
    const permissions = ref([])
    const loading = ref(false)

    const loadPermissions = async () => {
        try {
            loading.value = true
            permissions.value = await getUserPermissions()
        } catch (error) {
            console.error('Failed to load permissions:', error)
            permissions.value = []
        } finally {
            loading.value = false
        }
    }

    const hasPermission = (permission) => {
        return checkPermission(permissions.value, permission)
    }

    const hasAnyPermission = (perms) => {
        return checkAnyPermissions(permissions.value, perms)
    }

    const hasAllPermissions = (perms) => {
        return checkAllPermissions(permissions.value, perms)
    }

    onMounted(() => {
        loadPermissions()
    })

    return {
        permissions,
        loading,
        hasPermission,
        hasAnyPermission,
        hasAllPermissions,
        loadPermissions
    }
}