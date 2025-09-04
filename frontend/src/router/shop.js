const shop = [
  {
    path: "/kenh-nguoi-ban",
    component: () => import("@/layouts/shopLayout.vue"),
    children: [
      {
        path: "dang-ky",
        name: "register-shop",
        component: () => import("@/pages/clients/shop/register.vue"),
        meta: {
          requiresAuth: true,
          allowWithoutShop: true // Cho phép truy cập mà không cần shop
        }
      },
      {
        path: "them-san-pham",
        name: "add-product-shop",
        component: () => import("@/pages/clients/shop/product-add.vue"),
        meta: {
          requiresAuth: true,
          requiresShop: true
        }
      },
      {
        path: "chinh-sua-san-pham/:id",
        name: "edit-product-shop",
        component: () => import("@/pages/clients/shop/product-edit.vue"),
        meta: {
          requiresAuth: true,
          requiresShop: true
        }
      },
      {
        path: "san-pham",
        name: "product-shop",
        component: () => import("@/pages/clients/shop/product.vue"),
        meta: {
          requiresAuth: true,
          requiresShop: true
        }
      },
      {
        path: "don-hang",
        name: "order-shop",
        component: () => import("@/pages/clients/shop/order.vue"),
        meta: {
          requiresAuth: true,
          requiresShop: true
        }
      },
      {
        path: "tai-chinh",
        name: "financial-shop",
        component: () => import("@/pages/clients/shop/financial.vue"),
        meta: {
          requiresAuth: true,
          requiresShop: true
        }
      },
      {
        path: "doanh-thu",
        name: "revenue-shop",
        component: () => import("@/pages/clients/shop/revenue.vue"),
        meta: {
          requiresAuth: true,
          requiresShop: true
        }
      },
      {
        path: "khuyen-mai",
        name: "voucher-shop",
        component: () => import("@/pages/clients/shop/voucher.vue"),
        meta: {
          requiresAuth: true,
          requiresShop: true
        }
      },
      {
        path: "ho-so",
        name: "profile-shop",
        component: () => import("@/pages/clients/shop/profile.vue"),
        meta: {
          requiresAuth: true,
          requiresShop: true
        }
      },
      {
        path: "thuoc-tinh",
        name: "attribute-shop",
        component: () => import("@/pages/clients/shop/attribute.vue"),
        meta: {
          requiresAuth: true,
          requiresShop: true
        }
      },
      {
        path: 'thuoc-tinh/gia-tri/:id',
        name: 'shop-attribute-value',
        component: () => import("@/pages/clients/shop/attribute-value.vue"),
        meta: {
          requiresAuth: true,
          requiresShop: true
        }
      },
    ],
  },
];
export default shop