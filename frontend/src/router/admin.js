const admin = [
    {
        path: '/admin',
        component: () => import('@/layouts/adminLayout.vue'),
        children: [
            {
                path: '',
                name: 'admin-dashboard',
                component: () => import('@/pages/admins/Dashboard.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['dashboard']
                }
            },
            {
                path: 'thue',
                name: 'admin-taxes',
                component: () => import('@/pages/admins/taxes_fees/tax.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['finance']
                }
            },
            {
                path: 'phi',
                name: 'admin-fees',
                component: () => import('@/pages/admins/taxes_fees/fee.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['finance']
                }
            },
            {
                path: 'danh-muc',
                name: 'admin-category',
                component: () => import('@/pages/admins/categories/index.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['category']
                }
            },
            {
                path: 'thuoc-tinh',
                name: 'admin-attribute',
                component: () => import('@/pages/admins/attributes/index.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['attribute']
                }
            },
            {
                path: 'thuoc-tinh/gia-tri/:id',
                name: 'admin-attribute-value',
                component: () => import('@/pages/admins/attributes/values.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['attribute']
                }
            },
            {
                path: 'san-pham',
                name: 'admin-product',
                component: () => import('@/pages/admins/products/index.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['product']
                }
            },
            {
                path: 'cau-hinh-san',
                name: 'admin-config-platform',
                component: () => import('@/pages/admins/configs/platform.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['setting']
                }
            },
            {
                path: 'cau-hinh-banner',
                name: 'admin-banner',
                component: () => import('@/pages/admins/banners/index.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['setting']
                }
            },
            {
                path: 'ma-giam-gia-san',
                name: 'admin-vouchers-platform',
                component: () => import('@/pages/admins/vouchers/index.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['voucher']
                }
            },
            {
                path: 'cau-hinh-vai-tro',
                name: 'admin-role',
                component: () => import('@/pages/admins/configs/role.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['role']
                }
            },
            {
                path: 'cap-bac',
                name: 'admin-rank',
                component: () => import('@/pages/admins/ranks/index.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['rank']
                }
            },
            {
                path: 'nguoi-dung',
                name: 'admin-user',
                component: () => import('@/pages/admins/accounts/user.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['user']
                }
            },
            {
                path: 'nhan-vien',
                name: 'admin-staff',
                component: () => import('@/pages/admins/accounts/staff.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['staff']
                }
            },
            {
                path: 'san-pham/chi-tiet/:id',
                name: 'admin-product-detail',
                component: () => import('@/pages/admins/products/detail.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['product']
                }
            },
            {
                path: 'lenh-rut-tien',
                name: 'admin-payouts',
                component: () => import('@/pages/admins/payouts/index.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['finance']
                }
            },
            {
                path: 'cau-hinh-vi-pham',
                name: 'admin-violation-type',
                component: () => import('@/pages/admins/violations/type.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['setting']
                }
            },
            {
                path: 'cua-hang/to-cao',
                name: 'admin-violation-process',
                component: () => import('@/pages/admins/violations/index.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['shop']
                }
            },
            {
                path: 'cua-hang-vi-pham',
                name: 'admin-violation-shop',
                component: () => import('@/pages/admins/shops/violation.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['shop']
                }
            },
            {
                path: 'cua-hang',
                name: 'admin-shop',
                component: () => import('@/pages/admins/shops/index.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['shop']
                }
            },
            {
                path: 'ho-so',
                name: 'admin-profile',
                component: () => import('@/pages/admins/accounts/profile.vue'),
                meta: {
                    requiresAuth: true,
                    requiredRole: true
                }
            },
            {
                path: 'so-du-san',
                name: 'admin-platform-wallet',
                component: () => import('@/pages/admins/wallet/index.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['finance']
                }
            },
            {
                path: 'don-hang',
                name: 'admin-orders',
                component: () => import('@/pages/admins/orders/index.vue'),
                meta: {
                    requiresAuth: true,
                    requiredPermissions: ['order']
                }
            },
        ]
    }
];
export default admin;