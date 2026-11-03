<template>
    <div class="w-full flex flex-col">
        <!-- Label -->
        <label
            v-if="label"
            :for="inputId"
            class="block text-sm font-medium text-text-secondary mb-1.5 select-none"
        >
            {{ label }}
            <span v-if="required" class="text-red-400 ml-0.5">*</span>
        </label>

        <!-- Input Container -->
        <div class="relative flex items-center">
            <!-- Leading Icon / Slot -->
            <div
                v-if="$slots.leading || leadingIcon"
                class="absolute left-3.5 flex items-center pointer-events-none text-text-muted"
            >
                <slot name="leading">
                    <BaseIcon v-if="leadingIcon" :name="leadingIcon" :size="18" />
                </slot>
            </div>

            <!-- Input Element -->
            <input
                :id="inputId"
                :type="type"
                :value="modelValue"
                :placeholder="placeholder"
                :disabled="disabled"
                :readonly="readonly"
                :required="required"
                :autocomplete="autocomplete"
                v-bind="$attrs"
                @input="$emit('update:modelValue', $event.target.value)"
                @blur="$emit('blur', $event)"
                @focus="$emit('focus', $event)"
                @change="$emit('change', $event)"
                :class="[
                    'w-full rounded-xl bg-dark-surface border text-white placeholder-text-muted text-sm transition-all duration-200 outline-none',
                    ($slots.leading || leadingIcon) ? 'pl-10' : 'pl-4',
                    ($slots.trailing || trailingIcon) ? 'pr-10' : 'pr-4',
                    'py-2.5',
                    errorMessage
                        ? 'border-red-500/80 focus:border-red-500 focus:ring-1 focus:ring-red-500/50'
                        : 'border-dark-border focus:border-primary focus:ring-1 focus:ring-primary focus:shadow-glow-primary/20',
                    disabled ? 'opacity-50 cursor-not-allowed bg-dark-bg' : '',
                    readonly ? 'bg-dark-bg/60 cursor-default' : ''
                ]"
            />

            <!-- Trailing Icon / Slot -->
            <div
                v-if="$slots.trailing || trailingIcon"
                class="absolute right-3.5 flex items-center text-text-muted"
            >
                <slot name="trailing">
                    <BaseIcon v-if="trailingIcon" :name="trailingIcon" :size="18" />
                </slot>
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
import BaseIcon from './BaseIcon.vue'

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
    type: {
        type: String,
        default: 'text',
    },
    placeholder: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    readonly: {
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
    leadingIcon: {
        type: String,
        default: '',
    },
    trailingIcon: {
        type: String,
        default: '',
    },
    autocomplete: {
        type: String,
        default: undefined,
    },
})

defineEmits(['update:modelValue', 'blur', 'focus', 'change'])

const inputId = computed(() => {
    if (props.id) return props.id
    if (props.label) return `input-${props.label.toLowerCase().replace(/\s+/g, '-')}`
    return undefined
})
</script>
