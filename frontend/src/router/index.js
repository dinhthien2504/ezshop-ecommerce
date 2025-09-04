import { createRouter, createWebHistory } from "vue-router";
import admin from "./admin";
import shop from "./shop";
import { routeGuard } from "./guards";

const routes = [
  // Error pages
  {
    path: "/unauthorized",
    name: "unauthorized",
    component: () => import("@/pages/errors/401.vue"),
  },
  {
    path: "/forbidden",
    name: "forbidden",
    component: () => import("@/pages/errors/403.vue"),
  },
  {
    path: "/",
    component: () => import("@/layouts/client.vue"),
    children: [
      {
        path: "",
        name: "home",
        component: () => import("@/pages/clients/home.vue"),
      },
      {
        path: "gio-hang",
        name: "cart",
        component: () => import("@/pages/clients/carts/index.vue"),
      },
      {
        path: "thanh-toan",
        name: "checkout",
        component: () => import("@/pages/clients/orders/checkout.vue"),
      },
      {
        path: "hoan-tat-dat-hang",
        name: "return-checkout",
        component: () => import("@/pages/clients/orders/return-checkout.vue"),
      },
      {
        path: "san-pham",
        name: "products",
        component: () => import("@/pages/clients/products/index.vue"),
      },
      {
        path: ":slug-:id(\\d+)",
        name: "product-detail",
        component: () => import("@/pages/clients/products/detail.vue"),
      },
      {
        path: "dang-nhap",
        name: "login",
        component: () => import("@/pages/clients/auth/login.vue"),
      },
      {
        path: "dang-ky",
        name: "register",
        component: () => import("@/pages/clients/auth/register.vue"),
      },
      {
        path: "nguoi-dung",
        component: () => import("@/pages/clients/auth/userLayout.vue"),
        children: [
          {
            path: "",
            name: "user-profile",
            component: () => import("@/pages/clients/auth/profile.vue"),
          },
          {
            path: "dia-chi",
            name: "user-address",
            component: () => import("@/pages/clients/auth/address.vue"),
          },
          {
            path: "doi-mat-khau",
            name: "user-change-password",
            component: () => import("@/pages/clients/auth/password.vue"),
          },
          {
            path: "don-mua",
            name: "order-history",
            component: () => import("@/pages/clients/orders/order-history.vue"),
          },
          {
            path: "don-hang-:id",
            name: "order-history-detail",
            component: () => import("@/pages/clients/orders/order-history-detail.vue"),
          },
          {
            path: "yeu-thich",
            name: "user-wishlist",
            component: () => import("@/pages/clients/auth/wishlist.vue"),
          },
          {
            path: "theo-doi",
            name: "user-following",
            component: () => import("@/pages/clients/auth/follow.vue"),
          },
        ],
      },
      {
        path: "cua-hang-:slug-:id",
        name: "shop-profile",
        component: () => import("@/pages/clients/shop/shopProfile.vue"),
      },
      {
        path: "thong-bao",
        name: "notification",
        component: () => import("@/pages/clients/notification.vue"),
      },
    ],
  },
  ...admin,
  ...shop,
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    return { top: 0, behavior: "auto" };
  },
});

// Apply global route guard
router.beforeEach(routeGuard);

export default router;
