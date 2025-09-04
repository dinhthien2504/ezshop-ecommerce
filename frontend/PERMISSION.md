# Hệ thống phân quyền đã được tối ưu

## 🚀 **TỐI ƯU ĐÃ THỰC HIỆN**

### ✅ **Trước khi tối ưu:**
- useAuthGuard chạy trong `onBeforeMount` → Trang load xong mới kiểm tra quyền
- Người dùng thấy trang trước khi bị chuyển hướng
- Trải nghiệm không mượt mà

### ✅ **Sau khi tối ưu:**
- Router Guard kiểm tra quyền **TRƯỚC KHI** tải trang
- Người dùng không thấy trang nếu không có quyền
- Chuyển thẳng đến trang lỗi 401/403

---

## 📋 **CÁC THÀNH PHẦN MỚI**

### 1. **Router Guard** (`/router/guards.js`)
- Kiểm tra quyền trước khi tải component
- Cache permissions để tối ưu hiệu suất
- Tự động chuyển hướng đến trang lỗi

### 2. **Permission Utils** (`/utils/permissionUtils.js`)
- Centralized permission management
- Cache permissions 5 phút
- Helper functions cho permission check

### 3. **Error Pages**
- `/pages/errors/401.vue` - Chưa đăng nhập
- `/pages/errors/403.vue` - Không có quyền

### 4. **Router Meta**
```javascript
{
    path: 'admin/users',
    meta: { 
        requiresAuth: true, 
        requiredPermissions: ['user.view'] 
    }
}
```

---

## 🛠️ **CÁCH SỬ DỤNG MỚI**

### **1. Bảo vệ Routes (Recommend):**
```javascript
// router/admin.js
{
    path: 'nguoi-dung',
    name: 'admin-user',
    component: () => import('../pages/admins/accounts/user.vue'),
    meta: { 
        requiresAuth: true, 
        requiredPermissions: ['user'] 
    }
}
```

### **2. Bảo vệ UI Elements:**
```vue
<template>
    <!-- Kiểm tra quyền trong template -->
    <div v-if="hasPermission('user')">
        <button>Thêm User</button>
    </div>
    
    <!-- Với multiple permissions -->
    <div v-if="hasAllPermissions(['user', 'staff'])">
        <AdminPanel />
    </div>
</template>
```

### **3. Kiểm tra quyền trong component:**
```vue
<script setup>
import { getUserPermissions, hasAllPermissions } from '@/utils/permissionUtils'

const permissions = await getUserPermissions()
const canEdit = hasAllPermissions(permissions, ['user'])
</script>
```

---

## 🔧 **CÁC API ĐÃ ĐƯỢC PHÂN QUYỀN**

### **User Management:**
- `user` - Quản lý người dùng
- `staff` - Quản lý nhân viên

### **Product Management:**
- `product` - Quản lý sản phẩm
- `category` - Quản lý danh mục
- `attribute` - Quản lý thuộc tính

### **Shop Management:**
- `shop` - Quản lý cửa hàng

### **System Management:**
- `role` - Quản lý vai trò
- `setting` - Cấu hình hệ thống
- `dashboard` - Dashboard quản trị
- `finance` - Quản lý tài chính
- `voucher` - Quản lý mã giảm giá
- `rank` - Quản lý hạng thành viên

---

## 🎯 **WORKFLOW MỚI**

```
User truy cập /admin/users
        ↓
Router Guard kiểm tra token
        ↓
Kiểm tra meta.requiredPermissions
        ↓
Gọi API /role/user (có cache)
        ↓
So sánh quyền yêu cầu vs quyền user
        ↓
✅ Có quyền: Load component
❌ Không có quyền: Chuyển đến /forbidden
❌ Chưa login: Chuyển đến /unauthorized
```

---

## 📝 **LƯU Ý**

1. **useAuthGuard cũ đã deprecated** - Sử dụng router meta thay thế
2. **Cache permissions** - Tự động expire sau 5 phút
3. **Clear cache khi logout** - Gọi `clearAuthCache()`
4. **Error pages responsive** - Tự động responsive trên mobile

---

## 🧪 **TESTING**

1. **Test không có token:**
   - Truy cập `/admin` → Chuyển `/unauthorized`

2. **Test không có quyền:**
   - Login user không có quyền admin
   - Truy cập `/admin/users` → Chuyển `/forbidden`

3. **Test có quyền:**
   - Login admin
   - Truy cập `/admin/users` → Load bình thường

**🎉 Hệ thống bây giờ sẽ kiểm tra quyền TRƯỚC KHI tải trang, không còn hiện trang rồi mới chuyển hướng!**

---

## 🔍 **CÁCH HOẠT ĐỘNG CHI TIẾT**

### **1. Kiến trúc hệ thống phân quyền**

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │    Backend      │    │   Database      │
│                 │    │                 │    │                 │
│ • Router Guards │◄──►│ • Middleware    │◄──►│ • tbl_users     │
│ • Permission    │    │ • Controllers   │    │ • tbl_roles     │
│   Utils         │    │ • Models        │    │ • tbl_permissions│
│ • Auth State    │    │ • Sanctum       │    │ • tbl_role_perms│
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### **2. Database Schema**

```sql
-- Bảng vai trò
tbl_roles
├── id (PK)
├── title (unique)         -- 'admin', 'staff', 'user'
├── description           -- Mô tả vai trò
├── role_status          -- Trạng thái hoạt động
└── timestamps

-- Bảng quyền
tbl_permissions  
├── id (PK)
├── title (unique)         -- 'user', 'product', 'shop'
├── description           -- Mô tả quyền
└── timestamps

-- Bảng phân quyền (Many-to-Many)
tbl_role_permissions
├── id (PK)
├── role_id (FK → tbl_roles.id)
├── permission_id (FK → tbl_permissions.id)
├── permission_value      -- 1: có quyền, 0: không có quyền
└── timestamps

-- Bảng người dùng
tbl_users
├── id (PK)
├── user_name
├── email
├── password
├── role_id (FK → tbl_roles.id, nullable)
└── ... other fields
```

### **3. Backend Security Flow**

#### **3.1 PermissionMiddleware.php**
```php
public function handle(Request $request, Closure $next, ...$permissions)
{
    $user = Auth::user();
    
    // 1. Kiểm tra đăng nhập
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    // 2. Kiểm tra role_id cho admin routes  
    if ($request->is('api/admin/*') && !$user->role_id) {
        return response()->json([
            'message' => 'Forbidden',
            'error' => 'no_role'
        ], 403);
    }

    // 3. Lấy quyền của user từ database
    $userPermissions = RolePermission::where('role_id', $user->role_id)
        ->where('permission_value', 1)
        ->with('permission')
        ->get()
        ->pluck('permission.title')
        ->toArray();

    // 4. Kiểm tra quyền yêu cầu
    foreach ($permissions as $permission) {
        if (!in_array($permission, $userPermissions)) {
            return response()->json([
                'message' => 'Forbidden',
                'required_permission' => $permission
            ], 403);
        }
    }

    return $next($request);
}
```

#### **3.2 Route Protection**
```php
// routes/user.php
Route::middleware(['auth:sanctum', 'permission:user|staff'])->group(function () {
    Route::get('/user-list', [UserController::class, 'getUsers']);
    Route::post('/user/change-status/{id}', [UserController::class, 'changeStatus']);
});

// routes/role.php  
Route::middleware(['auth:sanctum', 'permission:role'])->group(function () {
    Route::get('/role', [RoleController::class, 'getRoles']);
    Route::post('/role', [RoleController::class, 'store']);
});

// routes/shop.php
Route::middleware(['auth:sanctum', 'permission:shop'])->group(function () {
    Route::get('shops', [ShopController::class, 'getShops']);
    Route::post('shop/change-status/{id}', [ShopController::class, 'changeStatus']);
});
```

### **4. Frontend Security Flow**

#### **4.1 Router Guard Process**
```javascript
// router/guards.js
export async function routeGuard(to, from, next) {
    const token = localStorage.getItem('token')
    
    // BƯỚC 1: Kiểm tra requiresAuth
    if (!to.meta?.requiresAuth) {
        return next() // Route công khai → OK
    }
    
    // BƯỚC 2: Kiểm tra token
    if (!token) {
        return next({
            name: 'unauthorized',
            query: { redirect: to.fullPath }
        })
    }
    
    // BƯỚC 3: Kiểm tra đặc biệt cho admin routes
    if (to.path.startsWith('/admin')) {
        try {
            const role = await getUserRole()
            if (!role || !role.id) {
                return next({
                    name: 'forbidden',
                    query: { reason: 'no_role' }
                })
            }
        } catch (error) {
            return next({ name: 'unauthorized' })
        }
    }
    
    // BƯỚC 4: Kiểm tra permissions cụ thể
    if (!to.meta?.requiredPermissions?.length) {
        return next() // Không yêu cầu permission → OK
    }
    
    try {
        const permissions = await getUserPermissions()
        const hasRequired = hasAllPermissions(permissions, to.meta.requiredPermissions)
        
        if (hasRequired) {
            return next() // Có đủ quyền → OK
        } else {
            return next({
                name: 'forbidden',
                query: { required: to.meta.requiredPermissions.join(',') }
            })
        }
    } catch (error) {
        return next({ name: 'unauthorized' })
    }
}
```

#### **4.2 Permission Caching**
```javascript
// utils/permissionUtils.js
let permissionsCache = null
let roleCache = null  
let cacheExpiry = 0
const CACHE_DURATION = 5 * 60 * 1000 // 5 phút

export async function getUserPermissions() {
    const now = Date.now()
    
    // Kiểm tra cache còn hiệu lực không
    if (permissionsCache && now < cacheExpiry) {
        return permissionsCache
    }
    
    // Gọi API lấy permissions mới
    const response = await api.get('/role/user')
    const permissions = response.data.permissions.map(p => p.title)
    
    // Cập nhật cache
    permissionsCache = permissions
    cacheExpiry = now + CACHE_DURATION
    
    return permissions
}
```

### **5. Authentication State Management**

#### **5.1 Global Auth State**
```javascript
// utils/authState.js
const user_name_auth = ref(localStorage.getItem('user_name') || '')
const avatar_user = ref(localStorage.getItem('avatar') || '')
const user_role = ref(JSON.parse(localStorage.getItem('role') || 'null'))

// Sync với localStorage
watch(user_role, (newVal) => {
    if (newVal) {
        localStorage.setItem('role', JSON.stringify(newVal))
    } else {
        localStorage.removeItem('role')
    }
})

// Clear khi logout
const clear_user = () => {
    user_name_auth.value = ""
    avatar_user.value = ""
    user_role.value = null
    
    ['user_name', 'avatar', 'token', 'role'].forEach(key => {
        localStorage.removeItem(key)
    })
}
```

### **6. Error Handling & User Experience**

#### **6.1 Error Pages**
```vue
<!-- pages/errors/401.vue -->
<template>
    <div class="error-page unauthorized-page">
        <div class="error-container">
            <h1 class="error-code">401</h1>
            <h2>Chưa đăng nhập</h2>
            <p>Bạn cần đăng nhập để truy cập khu vực này</p>
            
            <div class="error-actions">
                <button @click="goToLogin">Đăng nhập ngay</button>
                <button @click="goHome">Về trang chủ</button>
            </div>
        </div>
    </div>
</template>
```

```vue
<!-- pages/errors/403.vue -->
<template>
    <div class="error-page forbidden-page">
        <div class="error-container">
            <h1 class="error-code">403</h1>
            <h2>Truy cập bị từ chối</h2>
            
            <!-- Phân biệt lỗi: No Role vs No Permission -->
            <div v-if="isNoRoleError" class="no-role">
                <p>Tài khoản chưa được cấp vai trò để truy cập khu vực quản trị</p>
            </div>
            
            <div v-else class="no-permission">
                <p>Bạn không có quyền truy cập trang này</p>
                <div v-if="requiredPermissions">
                    <strong>Quyền yêu cầu:</strong> {{ requiredPermissions }}
                </div>
            </div>
        </div>
    </div>
</template>
```

### **7. Performance Optimizations**

#### **7.1 API Caching với useConfig**
```javascript
// composables/useConfig.js
const configData = ref(null)
const lastFetch = ref(null)
const CACHE_DURATION = 5 * 60 * 1000

export function useConfig() {
    const isCacheValid = computed(() => {
        return configData.value && 
               lastFetch.value && 
               Date.now() - lastFetch.value < CACHE_DURATION
    })
    
    const fetchConfig = async (force = false) => {
        if (!force && isCacheValid.value) {
            return configData.value // Sử dụng cache
        }
        
        // Tránh duplicate calls
        if (isLoading.value) {
            return new Promise((resolve) => {
                const interval = setInterval(() => {
                    if (!isLoading.value && configData.value) {
                        clearInterval(interval)
                        resolve(configData.value)
                    }
                }, 100)
            })
        }
        
        // Fetch từ API
        const res = await api.get('/config')
        configData.value = res.data.config
        lastFetch.value = Date.now()
        
        return configData.value
    }
}
```

### **8. Security Best Practices**

1. **Double Protection**: Frontend + Backend validation
2. **Token-based Auth**: Sanctum cho API security  
3. **Role Hierarchy**: Admin > Staff > User
4. **Permission Granularity**: Specific permissions cho từng module
5. **Cache Security**: Auto-expire và clear on logout
6. **Route Meta**: Declarative permission requirements
7. **Error Disclosure**: Limited information trong error messages

### **9. Workflow Complete**

```
🌐 User Request → 🔐 Frontend Guard → 🛡️ Backend Middleware → 💾 Database Check → ✅ Access Granted
                      ↓ FAIL             ↓ FAIL              ↓ FAIL
                   📄 Error Page      🚫 403 Response    ❌ Permission Denied
```

### **10. Real Example Flow**

```
Ví dụ: User truy cập /admin/nguoi-dung

1. Router Guard nhận request
2. Kiểm tra to.meta.requiresAuth = true
3. Kiểm tra localStorage.getItem('token') = có token
4. Kiểm tra to.path.startsWith('/admin') = true
5. Gọi getUserRole() → kiểm tra role_id ≠ null
6. Kiểm tra to.meta.requiredPermissions = ['user'] 
7. Gọi getUserPermissions() → ['user', 'staff', 'dashboard']
8. hasAllPermissions(['user', 'staff', 'dashboard'], ['user']) = true
9. next() → Load component AdminUser.vue
```

**Hệ thống này đảm bảo security ở mọi layer và cung cấp UX mượt mà cho người dùng!**
