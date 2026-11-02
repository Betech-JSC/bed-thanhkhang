import { useThemeStore } from '@/stores/themeStore'
import { computed } from 'vue'

export const useTheme = () => {
    const themeStore = useThemeStore()

    return {
        isDarkMode: computed(() => themeStore.isDarkMode),
        toggleDarkMode: () => themeStore.toggleDarkMode(),
        setDarkMode: (isDark) => themeStore.setDarkMode(isDark),
    }
}
