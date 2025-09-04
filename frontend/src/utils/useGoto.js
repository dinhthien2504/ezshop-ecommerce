// src/composables/useGoTo.js
import { useRouter } from 'vue-router'

export default function useGoTo() {
  const router = useRouter()
  const goTo = (path) => router.push(path)
  return { goTo }
}
