<template>
    <div
        v-if="normalizedTree.length > 0"
        class="w-full lg:w-[264px] bg-[#FAFAFA] flex flex-col select-none"
    >
        <!-- Mobile Drawer Header (< lg) -->
        <div
            v-if="showMobileHeader"
            class="flex items-center justify-between pb-3 mb-3 border-b border-[#CDD5DF] lg:hidden"
        >
            <h2 class="text-[18px] leading-[28px] font-bold text-[#00284B] uppercase font-sans">
                {{ tt('DANH MỤC SẢN PHẨM') }}
            </h2>
            <button
                type="button"
                @click="$emit('close')"
                class="w-11 h-11 -mr-2 inline-flex items-center justify-center rounded-[4px] text-[#364152] hover:text-[#00284B] hover:bg-[#EEF2F6] transition-colors cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]"
                :aria-label="tt('Đóng bộ lọc')"
            >
                <span aria-hidden="true" class="text-[22px] leading-none font-normal">&times;</span>
            </button>
        </div>

        <!-- Section Heading (Figma 979:30396: 20px/32px Bold #00284B) -->
        <h2
            v-if="!showMobileHeader"
            class="text-[20px] leading-[32px] font-bold text-[#00284B] uppercase font-sans mb-3"
        >
            {{ tt('DANH MỤC SẢN PHẨM') }}
        </h2>

        <!-- 3-Tier Category Tree Accordion (L0 -> L1 -> L2 from props.categories) -->
        <div v-if="normalizedTree.length > 0" class="w-full flex flex-col">
            <div
                v-for="root in normalizedTree"
                :key="root.id"
                class="w-full flex flex-col"
            >
                <!-- L0 Root Row (Figma 979:30397 / 979:30434 / 979:30437) -->
                <button
                    type="button"
                    @click="handleRootClick(root)"
                    class="w-full min-h-[51px] py-[12px] border-b border-[#CDD5DF] flex items-center justify-between gap-4 text-left cursor-pointer transition-colors group focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]"
                    :aria-expanded="root.children.length > 0 ? isRootExpanded(root.id) : undefined"
                >
                    <span
                        class="text-[16px] leading-[26px] font-bold font-sans transition-colors"
                        :class="
                            isRootActive(root)
                                ? 'text-[#005EA4]'
                                : 'text-[#364152] group-hover:text-[#00284B]'
                        "
                    >
                        {{ root.title }}
                    </span>

                    <img
                        v-if="root.children.length > 0"
                        :src="
                            isRootExpanded(root.id)
                                ? '/assets/figma/san-pham/filter-chevron-down.svg'
                                : '/assets/figma/san-pham/filter-chevron-right.svg'
                        "
                        alt=""
                        aria-hidden="true"
                        width="20"
                        height="20"
                        class="w-5 h-5 shrink-0"
                    />
                    <span
                        v-else
                        class="w-[14px] h-[14px] rounded-[2px] shrink-0 flex items-center justify-center transition-colors"
                        :class="
                            isIdSelected(root.id)
                                ? ''
                                : 'border border-[#9AA4B2] bg-transparent group-hover:border-[#005EA4]'
                        "
                    >
                        <img
                            v-if="isIdSelected(root.id)"
                            src="/assets/figma/san-pham/filter-checkbox-checked.svg"
                            alt=""
                            aria-hidden="true"
                            width="14"
                            height="14"
                            class="w-[14px] h-[14px]"
                        />
                    </span>
                </button>

                <!-- L1 + L2 Expanded Panel (Figma 979:30400: bg #EEF2F6, padding 24px, gap 20px) -->
                <div
                    v-if="root.children.length > 0"
                    class="filter-accordion-grid"
                    :class="{ 'is-expanded': isRootExpanded(root.id) }"
                    :aria-hidden="!isRootExpanded(root.id)"
                >
                    <div class="overflow-hidden min-h-0">
                        <div class="w-full bg-[#EEF2F6] p-6 flex flex-col gap-5">
                            <template v-for="l1 in root.children" :key="l1.id">
                                <!-- L1 WITH L2 Children (e.g. "Xích thang cuốn", Figma 979:30405) -->
                                <div v-if="l1.children.length > 0" class="w-full flex flex-col gap-3">
                                    <div class="w-full min-h-[44px] lg:min-h-[22px] flex items-center justify-between gap-2">
                                        <!-- Clickable Label + Group Checkbox -->
                                        <button
                                            type="button"
                                            role="checkbox"
                                            :aria-checked="
                                                getGroupState(l1) === 'checked'
                                                    ? 'true'
                                                    : getGroupState(l1) === 'indeterminate'
                                                    ? 'mixed'
                                                    : 'false'
                                            "
                                            :tabindex="isRootExpanded(root.id) ? 0 : -1"
                                            @click="toggleL1Group(l1)"
                                            class="flex-1 min-w-0 min-h-[44px] lg:min-h-[22px] flex items-center gap-2 text-left cursor-pointer group focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4] rounded-[2px]"
                                        >
                                            <span
                                                class="text-[14px] leading-[22px] font-sans transition-colors break-words"
                                                :class="
                                                    getGroupState(l1) !== 'unchecked'
                                                        ? 'font-medium text-[#00284B]'
                                                        : 'font-normal text-[#364152] group-hover:text-[#00284B]'
                                                "
                                            >
                                                {{ l1.title }}
                                            </span>

                                            <span
                                                class="w-[14px] h-[14px] rounded-[2px] shrink-0 flex items-center justify-center transition-colors"
                                                :class="
                                                    getGroupState(l1) === 'checked'
                                                        ? ''
                                                        : getGroupState(l1) === 'indeterminate'
                                                        ? 'bg-[#005EA4]'
                                                        : 'border border-[#9AA4B2] bg-transparent group-hover:border-[#005EA4]'
                                                "
                                            >
                                                <img
                                                    v-if="getGroupState(l1) === 'checked'"
                                                    src="/assets/figma/san-pham/filter-checkbox-checked.svg"
                                                    alt=""
                                                    aria-hidden="true"
                                                    width="14"
                                                    height="14"
                                                    class="w-[14px] h-[14px]"
                                                />
                                                <span
                                                    v-else-if="getGroupState(l1) === 'indeterminate'"
                                                    class="w-2 h-[2px] bg-[#FAFAFA] rounded-[1px]"
                                                    aria-hidden="true"
                                                />
                                            </span>
                                        </button>

                                        <!-- Expand / Collapse L2 Toggle Button (Figma 979:30410) -->
                                        <button
                                            type="button"
                                            :tabindex="isRootExpanded(root.id) ? 0 : -1"
                                            @click.stop="toggleL1Expand(l1.id)"
                                            class="w-11 h-11 lg:w-5 lg:h-5 flex items-center justify-center shrink-0 cursor-pointer rounded-[2px] hover:bg-[#E3E8EF] transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]"
                                            :aria-expanded="isL1Expanded(l1.id)"
                                            :aria-label="tt('Mở rộng danh mục con')"
                                        >
                                            <img
                                                :src="
                                                    isL1Expanded(l1.id)
                                                        ? '/assets/figma/san-pham/filter-chevron-down.svg'
                                                        : '/assets/figma/san-pham/filter-chevron-right.svg'
                                                "
                                                alt=""
                                                aria-hidden="true"
                                                width="20"
                                                height="20"
                                                class="w-5 h-5"
                                            />
                                        </button>
                                    </div>

                                    <!-- L2 Leaf Items List (Figma 979:30411: px-[20px], gap-3) -->
                                    <div
                                        class="filter-accordion-grid"
                                        :class="{ 'is-expanded': isL1Expanded(l1.id) }"
                                        :aria-hidden="!isRootExpanded(root.id) || !isL1Expanded(l1.id)"
                                    >
                                        <div class="overflow-hidden min-h-0">
                                            <div class="w-full px-5 flex flex-col gap-3">
                                                <button
                                                    v-for="l2 in l1.children"
                                                    :key="l2.id"
                                                    type="button"
                                                    role="checkbox"
                                                    :aria-checked="isIdSelected(l2.id) ? 'true' : 'false'"
                                                    :tabindex="isRootExpanded(root.id) && isL1Expanded(l1.id) ? 0 : -1"
                                                    @click="toggleId(l2.id)"
                                                    class="w-full min-h-[44px] lg:min-h-[22px] flex items-center justify-between gap-2 text-left cursor-pointer group focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4] rounded-[2px]"
                                                >
                                                    <span
                                                        class="text-[14px] leading-[22px] font-sans transition-colors break-words"
                                                        :class="
                                                            isIdSelected(l2.id)
                                                                ? 'font-medium text-[#00284B]'
                                                                : 'font-normal text-[#364152] group-hover:text-[#00284B]'
                                                        "
                                                    >
                                                        {{ l2.title }}
                                                    </span>

                                                    <span
                                                        class="w-[14px] h-[14px] rounded-[2px] shrink-0 flex items-center justify-center transition-colors"
                                                        :class="
                                                            isIdSelected(l2.id)
                                                                ? ''
                                                                : 'border border-[#9AA4B2] bg-transparent group-hover:border-[#005EA4]'
                                                        "
                                                    >
                                                        <img
                                                            v-if="isIdSelected(l2.id)"
                                                            src="/assets/figma/san-pham/filter-checkbox-checked.svg"
                                                            alt=""
                                                            aria-hidden="true"
                                                            width="14"
                                                            height="14"
                                                            class="w-[14px] h-[14px]"
                                                        />
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- L1 WITHOUT L2 Children (Leaf at L1, Figma 979:30401 / 979:30430) -->
                                <button
                                    v-else
                                    type="button"
                                    role="checkbox"
                                    :aria-checked="isIdSelected(l1.id) ? 'true' : 'false'"
                                    :tabindex="isRootExpanded(root.id) ? 0 : -1"
                                    @click="toggleId(l1.id)"
                                    class="w-full min-h-[44px] lg:min-h-[22px] flex items-center justify-start gap-2 text-left cursor-pointer group focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4] rounded-[2px]"
                                >
                                    <span
                                        class="text-[14px] leading-[22px] font-sans transition-colors break-words"
                                        :class="
                                            isIdSelected(l1.id)
                                                ? 'font-medium text-[#00284B]'
                                                : 'font-normal text-[#364152] group-hover:text-[#00284B]'
                                        "
                                    >
                                        {{ l1.title }}
                                    </span>

                                    <span
                                        class="w-[14px] h-[14px] rounded-[2px] shrink-0 flex items-center justify-center transition-colors"
                                        :class="
                                            isIdSelected(l1.id)
                                                ? ''
                                                : 'border border-[#9AA4B2] bg-transparent group-hover:border-[#005EA4]'
                                        "
                                    >
                                        <img
                                            v-if="isIdSelected(l1.id)"
                                            src="/assets/figma/san-pham/filter-checkbox-checked.svg"
                                            alt=""
                                            aria-hidden="true"
                                            width="14"
                                            height="14"
                                            class="w-[14px] h-[14px]"
                                        />
                                    </span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
    selectedIds: {
        type: Array,
        default: () => [],
    },
    showMobileHeader: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['update:selectedIds', 'close'])

function getRawChildren(node) {
    if (!node || typeof node !== 'object') return []
    if (Array.isArray(node.children) && node.children.length > 0) return node.children
    if (Array.isArray(node.nodes) && node.nodes.length > 0) return node.nodes
    if (Array.isArray(node.subcategories) && node.subcategories.length > 0) return node.subcategories
    return []
}

function normalizeNode(node) {
    if (!node || typeof node !== 'object') return null
    const id = node.id
    const title = String(node.title || node.name || '').trim()
    if (!id || !title) return null

    const rawChildren = getRawChildren(node)
    const children = rawChildren.map(normalizeNode).filter(Boolean)

    return {
        id: Number.isNaN(Number(id)) ? String(id) : Number(id),
        slug: String(node.slug || id),
        title,
        children,
    }
}

const normalizedTree = computed(() => {
    if (!Array.isArray(props.categories)) return []
    return props.categories.map(normalizeNode).filter(Boolean)
})

const selectedSet = computed(
    () => new Set((Array.isArray(props.selectedIds) ? props.selectedIds : []).map((id) => String(id)))
)

const expandedRootIds = ref([])
const expandedL1Ids = ref([])
const hasUserToggled = ref(false)

function collectLeafIdsUnderNode(node) {
    if (!node) return []
    if (!node.children || node.children.length === 0) {
        return [node.id]
    }
    return node.children.flatMap((child) => collectLeafIdsUnderNode(child))
}

function syncDefaultExpansion() {
    const tree = normalizedTree.value
    if (!tree.length) return

    const nextRoots = new Set(hasUserToggled.value ? expandedRootIds.value.map(String) : [])
    const nextL1s = new Set(hasUserToggled.value ? expandedL1Ids.value.map(String) : [])

    // Auto-expand branches that contain any active selectedIds
    let foundSelectedBranch = false
    for (const root of tree) {
        const rootLeafIds = collectLeafIdsUnderNode(root).map(String)
        const rootSelfSelected = selectedSet.value.has(String(root.id))
        const hasSelectedDescendant =
            rootSelfSelected || rootLeafIds.some((id) => selectedSet.value.has(id))

        if (hasSelectedDescendant) {
            foundSelectedBranch = true
            nextRoots.add(String(root.id))
            for (const l1 of root.children) {
                const l1LeafIds = collectLeafIdsUnderNode(l1).map(String)
                if (
                    selectedSet.value.has(String(l1.id)) ||
                    l1LeafIds.some((id) => selectedSet.value.has(id))
                ) {
                    nextL1s.add(String(l1.id))
                }
            }
        }
    }

    // Default initial state matching Figma 979:30394: first root & its L1 groups expanded
    if (!hasUserToggled.value && !foundSelectedBranch) {
        const firstRoot = tree[0]
        if (firstRoot) {
            nextRoots.add(String(firstRoot.id))
            firstRoot.children.forEach((l1) => {
                if (l1.children.length > 0) {
                    nextL1s.add(String(l1.id))
                }
            })
        }
    }

    expandedRootIds.value = Array.from(nextRoots)
    expandedL1Ids.value = Array.from(nextL1s)
}

watch([normalizedTree, () => props.selectedIds], syncDefaultExpansion, {
    immediate: true,
    deep: true,
})

function isRootExpanded(rootId) {
    return expandedRootIds.value.includes(String(rootId))
}

function isL1Expanded(l1Id) {
    return expandedL1Ids.value.includes(String(l1Id))
}

function isIdSelected(id) {
    return selectedSet.value.has(String(id))
}

function isRootActive(root) {
    if (isIdSelected(root.id)) return true
    const leafIds = collectLeafIdsUnderNode(root)
    return leafIds.some((id) => isIdSelected(id))
}

function getGroupState(l1) {
    const childIds = (l1.children || []).map((c) => c.id)
    if (childIds.length === 0) {
        return isIdSelected(l1.id) ? 'checked' : 'unchecked'
    }
    const selectedCount = childIds.filter((id) => isIdSelected(id)).length
    if (selectedCount === 0) return 'unchecked'
    if (selectedCount === childIds.length) return 'checked'
    return 'indeterminate'
}

function handleRootClick(root) {
    if (root.children.length === 0) {
        toggleId(root.id)
        return
    }
    hasUserToggled.value = true
    const key = String(root.id)
    const idx = expandedRootIds.value.indexOf(key)
    if (idx > -1) {
        expandedRootIds.value.splice(idx, 1)
    } else {
        expandedRootIds.value.push(key)
        // Auto-expand L1 groups inside newly opened root if none are open yet
        const anyL1Open = root.children.some((l1) => expandedL1Ids.value.includes(String(l1.id)))
        if (!anyL1Open) {
            root.children.forEach((l1) => {
                if (l1.children.length > 0) {
                    expandedL1Ids.value.push(String(l1.id))
                }
            })
        }
    }
}

function toggleL1Expand(l1Id) {
    hasUserToggled.value = true
    const key = String(l1Id)
    const idx = expandedL1Ids.value.indexOf(key)
    if (idx > -1) {
        expandedL1Ids.value.splice(idx, 1)
    } else {
        expandedL1Ids.value.push(key)
    }
}

function toggleId(id) {
    const targetKey = String(id)
    const current = [...(Array.isArray(props.selectedIds) ? props.selectedIds : [])]
    const idx = current.findIndex((item) => String(item) === targetKey)
    if (idx > -1) {
        current.splice(idx, 1)
    } else {
        current.push(id)
    }
    emit('update:selectedIds', current)
}

function toggleL1Group(l1) {
    const childIds = (l1.children || []).map((c) => c.id)
    if (childIds.length === 0) {
        toggleId(l1.id)
        return
    }

    const state = getGroupState(l1)
    const childKeySet = new Set(childIds.map(String))
    const current = [...(Array.isArray(props.selectedIds) ? props.selectedIds : [])]

    if (state === 'checked') {
        const next = current.filter((item) => !childKeySet.has(String(item)))
        emit('update:selectedIds', next)
    } else {
        const existingSet = new Set(current.map(String))
        const next = [...current]
        childIds.forEach((cid) => {
            if (!existingSet.has(String(cid))) {
                next.push(cid)
            }
        })
        emit('update:selectedIds', next)
    }
}
</script>

<style scoped>
.filter-accordion-grid {
    display: grid;
    grid-template-rows: 0fr;
    transition: grid-template-rows 260ms cubic-bezier(0.22, 1, 0.36, 1);
}

.filter-accordion-grid.is-expanded {
    grid-template-rows: 1fr;
}
</style>
