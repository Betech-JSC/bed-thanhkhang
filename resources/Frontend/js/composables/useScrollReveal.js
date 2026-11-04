const DEFAULT_OPTIONS = {
    threshold: 0.05,
    rootMargin: '0px 0px 40px 0px',
    once: true,
    baseClasses: 'transition-transform duration-500 ease-out',
    hiddenClasses: 'translate-y-4',
    visibleClasses: 'translate-y-0',
}

function splitClasses(classString) {
    return String(classString || '')
        .split(/\s+/)
        .map((c) => c.trim())
        .filter(Boolean)
}

function isReducedMotionPreferred() {
    return (
        typeof window !== 'undefined' &&
        typeof window.matchMedia === 'function' &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches === true
    )
}

export function useScrollReveal(options = {}) {
    const resolved = {
        ...DEFAULT_OPTIONS,
        ...(typeof options === 'object' && options !== null ? options : {}),
    }

    const baseList = splitClasses(resolved.baseClasses)
    const hiddenList = splitClasses(resolved.hiddenClasses)
    const visibleList = splitClasses(resolved.visibleClasses)

    function setupReveal(el) {
        if (!el || typeof el.classList === 'undefined') return null

        if (
            isReducedMotionPreferred() ||
            typeof window === 'undefined' ||
            typeof IntersectionObserver === 'undefined'
        ) {
            el.classList.remove(...hiddenList)
            el.classList.add(...visibleList)
            return null
        }

        el.classList.add(...baseList, ...hiddenList)

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.remove(...hiddenList)
                        entry.target.classList.add(...visibleList)
                        if (resolved.once) {
                            observer.unobserve(entry.target)
                        }
                    } else if (!resolved.once) {
                        entry.target.classList.remove(...visibleList)
                        entry.target.classList.add(...hiddenList)
                    }
                })
            },
            {
                threshold: resolved.threshold,
                rootMargin: resolved.rootMargin,
            }
        )

        observer.observe(el)
        el._scrollRevealObserver = observer
        return observer
    }

    function cleanupReveal(el) {
        if (el && el._scrollRevealObserver) {
            el._scrollRevealObserver.disconnect()
            delete el._scrollRevealObserver
        }
    }

    return {
        options: resolved,
        setupReveal,
        cleanupReveal,
    }
}

export const vScrollReveal = {
    mounted(el, binding) {
        const customOptions =
            binding && typeof binding.value === 'object' && binding.value !== null
                ? binding.value
                : {}
        const { setupReveal } = useScrollReveal(customOptions)
        setupReveal(el)
    },
    unmounted(el) {
        if (el && el._scrollRevealObserver) {
            el._scrollRevealObserver.disconnect()
            delete el._scrollRevealObserver
        }
    },
    getSSRProps() {
        return {}
    },
}

export default vScrollReveal
