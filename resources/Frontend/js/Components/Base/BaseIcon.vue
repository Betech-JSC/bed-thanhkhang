<template>
    <component
        :is="iconComponent"
        v-if="iconComponent"
        :width="finalWidth"
        :height="finalHeight"
        :class="['inline-block align-middle flex-shrink-0 transition-colors', customClass]"
        :style="iconStyle"
        aria-hidden="true"
    />
    <span v-else :class="['inline-block', customClass]"></span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    size: {
        type: [Number, String],
        default: null,
    },
    width: {
        type: [Number, String],
        default: null,
    },
    height: {
        type: [Number, String],
        default: null,
    },
    customClass: {
        type: [String, Array, Object],
        default: '',
    },
})

// Eagerly import all icons from Icons folder (safe for SSR and Vite bundle)
const iconModules = import.meta.glob('../Icons/*.vue', { eager: true })

const iconMap = {}

// Helper to convert PascalCase to kebab-case
const toKebab = (str) =>
    str
        .replace(/([a-z0-9])([A-Z])/g, '$1-$2')
        .replace(/([A-Z])([A-Z])(?=[a-z])/g, '$1-$2')
        .toLowerCase()

for (const path in iconModules) {
    const fileName = path.split('/').pop().replace('.vue', '')
    const comp = iconModules[path].default || iconModules[path]

    // Register PascalCase (e.g. ArrowUp)
    iconMap[fileName] = comp
    // Register lowercase (e.g. arrowup)
    iconMap[fileName.toLowerCase()] = comp
    // Register kebab-case (e.g. arrow-up)
    iconMap[toKebab(fileName)] = comp
}

const iconComponent = computed(() => {
    if (!props.name) return null
    return iconMap[props.name] || iconMap[props.name.toLowerCase()] || iconMap[toKebab(props.name)] || null
})

const finalWidth = computed(() => {
    if (props.width !== null) return props.width
    if (props.size !== null) return props.size
    return undefined
})

const finalHeight = computed(() => {
    if (props.height !== null) return props.height
    if (props.size !== null) return props.size
    return undefined
})

const formatDimension = (val) => {
    if (val === null || val === undefined) return undefined
    if (typeof val === 'number') return `${val}px`
    if (!isNaN(val)) return `${val}px`
    return val
}

const iconStyle = computed(() => {
    const style = {}
    if (finalWidth.value) style.width = formatDimension(finalWidth.value)
    if (finalHeight.value) style.height = formatDimension(finalHeight.value)
    return style
})
</script>
