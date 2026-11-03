<template>
    <div class="w-full flex flex-col">
        <!-- Label -->
        <label
            v-if="label"
            :for="selectId"
            class="block text-sm font-medium text-text-secondary mb-1.5 select-none"
        >
            {{ label }}
            <span v-if="required" class="text-red-400 ml-0.5">*</span>
        </label>

        <!-- Select Container -->
        <div class="relative flex items-center">
            <!-- Native Select Element -->
            <select
                :id="selectId"
                :value="modelValue"
                :disabled="disabled"
                :required="required"
                v-bind="$attrs"
                @change="$emit('update:modelValue', $event.target.value); $emit('change', $event.target.value)"
                @blur="$emit('blur', $event)"
                @focus="$emit('focus', $event)"
                :class="[
                    'w-full appearance-none rounded-xl bg-dark-surface border text-white text-sm transition-all duration-200 outline-none px-4 py-2.5 pr-10 cursor-pointer',
                    !modelValue ? 'text-text-muted' : 'text-white',
                    errorMessage
                        ? 'border-red-500/80 focus:border-red-500 focus:ring-1 focus:ring-red-500/50'
                        : 'border-dark-border focus:border-primary focus:ring-1 focus:ring-primary focus:shadow-glow-primary/20',
                    disabled ? 'opacity-50 cursor-not-allowed bg-dark-bg pointer-events-none' : ''
                ]"
            >
                <!-- Placeholder Option -->
                <option v-if="placeholder" value="" disabled :selected="!modelValue" class="bg-[#141C2E] text-text-muted">
                    {{ placeholder }}
                </option>

                <!-- Options List -->
                <option
                    v-for="(option, idx) in normalizedOptions"
                    :key="`${option.value}-${idx}`"
                    :value="option.value"
                    :disabled="option.disabled"
                    class="bg-[#141C2E] text-white py-1"
                >
                    {{ option.label }}
                </option>
            </select>

            <!-- Dropdown Chevron Icon -->
            <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-text-muted flex items-center">
                <svg
                    class="w-4 h-4 transition-transform duration-200"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>

        <!-- Validation Error Message -->
        <p
            v-if="errorMessage"
            class="mt-1.5 text-xs text-red-400 flex items-center gap-1 leading-normal"
        >
            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path
                    fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd"
                ></path>
            </svg>
            <span>{{ errorMessage }}</span>
        </p>

        <!-- Help Hint -->
        <p v-else-if="hint" class="mt-1 text-xs text-text-muted leading-normal">
            {{ hint }}
        </p>
    </div>
</template>

<script setup>
import { computed } from 'vue'

defineOptions({
    inheritAttrs: false,
})

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    label: {
        type: String,
        default: '',
    },
    id: {
        type: String,
        default: '',
    },
    options: {
        type: Array,
        default: () => [],
    },
    placeholder: {
        type: String,
        default: 'Chọn một mục',
    },
    required: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    errorMessage: {
        type: String,
        default: '',
    },
    hint: {
        type: String,
        default: '',
    },
})

defineEmits(['update:modelValue', 'change', 'blur', 'focus'])

const selectId = computed(() => {
    if (props.id) return props.id
    if (props.label) return `select-${props.label.toLowerCase().replace(/\s+/g, '-')}`
    return undefined
})

const normalizedOptions = computed(() => {
    return props.options.map((opt) => {
        if (typeof opt === 'object' && opt !== null) {
            return {
                value: opt.value !== undefined ? opt.value : opt.id,
                label: opt.label !== undefined ? opt.label : (opt.name || opt.title || opt.value),
                disabled: !!opt.disabled,
            }
        }
        return {
            value: opt,
            label: opt,
            disabled: false,
        }
    })
})
</script>
