import { ref, watch } from 'vue'

const isDark = ref(
    document.documentElement.classList.contains('dark')
)

watch(isDark, (value) => {
    document.documentElement.classList.toggle('dark', value)
})

export function useDarkMode() {
    return { isDark }
}
