import { ref } from 'vue'

const isOpen = ref(false)
const initialContext = ref(null)

let eventListenerBound = false

export function openQuoteModal(context = null) {
    initialContext.value = context ?? null
    isOpen.value = true
}

export function closeQuoteModal() {
    isOpen.value = false
}

export function toggleQuoteModal(context = null) {
    if (isOpen.value) {
        closeQuoteModal()
    } else {
        openQuoteModal(context)
    }
}

function bindGlobalEventListener() {
    if (eventListenerBound || typeof window === 'undefined') return
    eventListenerBound = true
    window.addEventListener('open-quote-modal', (event) => {
        openQuoteModal(event?.detail ?? null)
    })
}

export function useQuoteModal() {
    bindGlobalEventListener()
    return {
        isOpen,
        initialContext,
        openQuoteModal,
        closeQuoteModal,
        toggleQuoteModal,
    }
}

export default useQuoteModal
