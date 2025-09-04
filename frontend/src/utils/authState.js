import { ref, watch } from "vue";

// Hàm helper để xử lý dữ liệu từ localStorage
const safeParseLocalStorage = (key, defaultValue = null) => {
  const value = localStorage.getItem(key);
  try {
    if (value === null || value === 'null' || value === 'undefined') return defaultValue;
    if (value.startsWith('{') || value.startsWith('[')) {
      return JSON.parse(value);
    }
    return value || defaultValue;
  } catch {
    return defaultValue;
  }
};

// Khởi tạo state
const user_name_auth = ref(safeParseLocalStorage('user_name', ''));
const avatar_user = ref(safeParseLocalStorage('avatar', ''));
const google_auth = ref(safeParseLocalStorage('google_id'));
const user_role = ref(safeParseLocalStorage('role'));

// Watch và cập nhật localStorage
watch(user_name_auth, (newVal) => {
  localStorage.setItem('user_name', newVal || '');
});

watch(avatar_user, (newVal) => {
  localStorage.setItem('avatar', newVal || '');
});

watch(google_auth, (newVal) => {
  if (newVal && typeof newVal === 'object') {
    localStorage.setItem('google_id', JSON.stringify(newVal));
  } else if (newVal) {
    localStorage.setItem('google_id', newVal);
  } else {
    localStorage.removeItem('google_id');
  }
});

watch(user_role, (newVal) => {
  if (newVal && typeof newVal === 'object') {
    localStorage.setItem('role', JSON.stringify(newVal));
  } else if (newVal) {
    localStorage.setItem('role', newVal);
  } else {
    localStorage.removeItem('role');
  }
});

// Hàm set user
const set_user = (name, avatar, gg) => {
  user_name_auth.value = name || '';
  avatar_user.value = avatar || '';

  // Xử lý google_auth đặc biệt
  if (gg && typeof gg === 'object') {
    google_auth.value = gg;
  } else if (gg === null || gg === undefined) {
    google_auth.value = null;
  } else {
    google_auth.value = gg;
  }
};

// Hàm clear user
const clear_user = () => {
  user_name_auth.value = "";
  avatar_user.value = "";
  google_auth.value = null; // Sử dụng null thay vì empty string
  user_role.value = null;

  // Xóa tất cả items liên quan
  ['user_name', 'avatar', 'google_id', 'token', 'role', 'shop_id'].forEach(key => {
    localStorage.removeItem(key);
  });
};

export { user_name_auth, avatar_user, google_auth, user_role, set_user, clear_user };