<template>
    <div class="error-page forbidden-page">
        <div class="container-fluid h-100 d-flex align-items-center justify-content-center">
            <div class="error-container">
                <div class="error-visual">
                    <div class="error-code-wrapper">
                        <h1 class="error-code">403</h1>
                        <div class="error-underline"></div>
                    </div>
                    <div class="error-icon-wrapper">
                        <div class="icon-background">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                    </div>
                </div>
                
                <div class="error-content d-flex flex-column align-items-center gap-3">
                    <h2 class="error-title mb-0">Truy cập bị từ chối</h2>
                    
                    <div v-if="isNoRoleError" class="error-description">
                        Tài khoản của bạn chưa được cấp vai trò để truy cập khu vực quản trị. Vui lòng liên hệ quản trị viên để được cấp vai trò phù hợp.
                    </div>
                    
                    <div v-else class="error-description">
                        Bạn không có quyền truy cập vào trang này. 
                        <span v-if="requiredPermissions">Quyền yêu cầu: <strong>{{ requiredPermissions }}</strong>.</span>
                        Vui lòng liên hệ quản trị viên để được cấp quyền phù hợp.
                    </div>
                    
                    <div class="error-actions">
                        <button 
                            @click="goBack" 
                            class="btn btn-danger rounded-1"
                        >
                            <i class="fas fa-arrow-left me-2"></i>
                            <span>Quay lại</span>
                        </button>
                        <button 
                            @click="goHome" 
                            class="btn btn-secondary rounded-1"
                        >
                            <i class="fas fa-home me-2"></i>
                            <span>Trang chủ</span>
                        </button>
                    </div>
                </div>
                
                <div class="error-footer">
                    <span class="error-code-text">Lỗi 403 - Forbidden Access</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

const requiredPermissions = computed(() => {
    return route.query.required ? route.query.required.split(',').join(', ') : null
})

const isNoRoleError = computed(() => {
    return route.query.reason === 'no_role'
})

const goBack = () => {
    if (window.history.length > 1) {
        router.go(-1)
    } else {
        goHome()
    }
}

const goHome = () => {
    router.push('/')
}
</script>

<style scoped>
.error-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    font-family: 'Montserrat', sans-serif;
    padding: 0;
    margin: 0;
    width: 100vw;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
}

.error-container {
    max-width: none;
    background: transparent;
    border-radius: 0;
    box-shadow: none;
    padding: 60px 40px;
    text-align: center;
    position: relative;
    overflow: hidden;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.error-container::before {
    display: none;
}

.error-code-wrapper {
    position: relative;
    margin-bottom: 30px;
}

.error-code {
    font-size: 120px;
    font-weight: 800;
    color: #d52220;
    margin: 0;
    line-height: 1;
    text-shadow: 0 4px 15px rgba(213, 34, 32, 0.3);
}

.error-underline {
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #d52220 0%, #ee2624 100%);
    margin: 10px auto;
    border-radius: 2px;
}

.error-icon-wrapper {
    margin: 30px 0;
}

.icon-background {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #d52220 0%, #ee2624 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    box-shadow: 0 10px 30px rgba(213, 34, 32, 0.3);
    animation: bounce 2s infinite;
}

.icon-background i {
    font-size: 40px;
    color: white;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

.error-content {
    margin-bottom: 40px;
}

.error-title {
    font-size: 32px;
    font-weight: 700;
    color: #333;
}

.alert-box {
    background: #fff5f5;
    border: 2px solid #fee;
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 30px;
    display: flex;
    align-items: flex-start;
    gap: 20px;
    text-align: left;
}

.alert-box i {
    font-size: 32px;
    color: #d52220;
    margin-top: 5px;
    flex-shrink: 0;
}

.error-description {
    font-size: 16px;
    color: #666;
    line-height: 1.6;
    margin: 0 auto;
    max-width: 500px;
}

.error-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    width: 185px;
}

.error-footer {
    border-top: 1px solid #f0f0f0;
}

.error-code-text {
    color: #999;
    font-size: 14px;
    font-weight: 500;
}

@media (max-width: 768px) {
    .error-container {
        padding: 40px 20px;
        margin: 0;
    }
    
    .error-code {
        font-size: 80px;
    }
    
    .error-title {
        font-size: 24px;
    }
    
    .error-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .btn {
        width: 100%;
        max-width: 250px;
    }
}
</style>
