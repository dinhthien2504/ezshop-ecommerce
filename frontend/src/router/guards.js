import { getUserPermissions, getUserRole, hasAllPermissions } from '@/utils/permissionUtils'
import message from '@/utils/messageState'
export async function routeGuard(to, from, next) {
    // Kiểm tra token trước
    const token = localStorage.getItem('token')
    // Nếu route không yêu cầu authentication
    if (!to.meta?.requiresAuth) {
        return next()
    }
    // Nếu không có token
    if (!token) {
        return next({
            name: 'unauthorized',
            query: { redirect: to.fullPath }
        })
    }

    // KIỂM TRA SHOP GUARD - Bước mới thêm
    if (to.meta?.requiresShop || (to.path.startsWith('/kenh-nguoi-ban') && !to.meta?.allowWithoutShop)) {
        const shop_id = localStorage.getItem('shop_id')
        
        // Kiểm tra shop_id có tồn tại và khác 0
        if (!shop_id || shop_id === '0' || shop_id === 0) {
            message.emit("show-message", {
                title: "Thông báo",
                text: "Bạn cần đăng ký shop để truy cập chức năng này",
                type: "warning",
            })
            return next({
                name: 'register-shop'
            })
        }
    }

    // Kiểm tra đặc biệt cho routes /admin - yêu cầu phải có role
    if (to.path.startsWith('/admin')) {
        try {
            const role = await getUserRole()

            // Nếu role_id là null hoặc không có role
            if (!role || !role.id) {
                return next({
                    name: 'forbidden',
                    query: {
                        redirect: to.fullPath,
                        requiredRole: false,
                        reason: 'no_role'
                    }
                })
            }
        } catch (error) {
            console.error('Role check failed:', error)
            return next({
                name: 'unauthorized',
                query: { redirect: to.fullPath }
            })
        }
    }

    // Nếu route không yêu cầu permissions cụ thể
    if (!to.meta?.requiredPermissions || to.meta.requiredPermissions.length === 0) {
        return next()
    }

    try {
        // Kiểm tra quyền
        const permissions = await getUserPermissions()
        const hasRequiredPermissions = hasAllPermissions(permissions, to.meta.requiredPermissions)

        if (hasRequiredPermissions) {
            return next()
        } else {
            return next({
                name: 'forbidden',
                query: {
                    redirect: to.fullPath,
                    required: to.meta.requiredPermissions.join(',')
                }
            })
        }
    } catch (error) {
        console.error('Permission check failed:', error)
        return next({
            name: 'unauthorized',
            query: { redirect: to.fullPath }
        })
    }
}
