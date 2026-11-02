import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'

export const useThemeStore = defineStore('theme', () => {
    const darkMode = ref(false)
    const STORAGE_KEY = 'theme-preference'

    // Load theme from localStorage on init
    const initializeTheme = () => {
        const savedTheme = localStorage.getItem(STORAGE_KEY)
        if (savedTheme) {
            darkMode.value = savedTheme === 'dark'
        } else {
            // Default to light mode when there's no saved preference
            // (Previously we respected system preference; change requested to default to light)
            darkMode.value = false
        }
        applyTheme()
    }

    // Apply theme to document
    const applyTheme = () => {
        const html = document.documentElement
        if (darkMode.value) {
            html.classList.add('dark')
        } else {
            html.classList.remove('dark')
        }
        // Save to localStorage
        localStorage.setItem(STORAGE_KEY, darkMode.value ? 'dark' : 'light')
    }

    // Toggle dark mode
    const toggleDarkMode = () => {
        darkMode.value = !darkMode.value
        applyTheme()
    }

    // Set dark mode explicitly
    const setDarkMode = (isDark) => {
        darkMode.value = isDark
        applyTheme()
    }

    // Watch for changes
    watch(darkMode, () => {
        applyTheme()
    })

    return {
        darkMode,
        initializeTheme,
        toggleDarkMode,
        setDarkMode,
        isDarkMode: computed(() => darkMode.value),
    }
})
