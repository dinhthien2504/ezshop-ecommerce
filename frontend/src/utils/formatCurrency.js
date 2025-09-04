export const formatCurrency = (amount) => {
    if (!amount) return '0 VNĐ'
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount)
}

export const formatNumber = (num) => {
    if (!num) return '0'
    return new Intl.NumberFormat('vi-VN').format(num)
}
