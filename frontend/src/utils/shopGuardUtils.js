import api from '@/configs/api'

// Kiểm tra shop_id từ localStorage
export function getShopId() {
    const shop_id = localStorage.getItem('shop_id')
    return shop_id ? parseInt(shop_id) : 0
}

// Kiểm tra xem user có shop hay không
export function hasShop() {
    const shop_id = getShopId()
    return shop_id > 0
}

// Lấy thông tin shop từ API (nếu cần)
export async function getShopInfo() {
    try {
        const response = await api.get('/shop')
        return response.data
    } catch (error) {
        console.error('Failed to get shop info:', error)
        return null
    }
}

// Refresh shop_id từ server
export async function refreshShopId() {
    try {
        const response = await api.get('/shop')
        if (response.data.shop_id) {
            localStorage.setItem('shop_id', response.data.shop_id.toString())
            return response.data.shop_id
        } else {
            localStorage.setItem('shop_id', '0')
            return 0
        }
    } catch (error) {
        console.error('Failed to refresh shop ID:', error)
        return 0
    }
}

// Clear shop data khi logout
export function clearShopData() {
    localStorage.removeItem('shop_id')
}