<template>
    <Draggable
        tag="ul"
        :modelValue="items"
        @update:modelValue="$emit('update:modelValue', $event)"
        item-key="hash_id"
        handle=".handle"
        :animation="200"
        group="category"
        :disabled="!draggable"
        ghostClass="ghost"
        class="tree-view-list flex flex-col space-y-0.5"
    >
        <template #item="{ element }">
            <li class="flex-col w-full select-none tree-view-item-wrapper">
                <div
                    class="flex items-center w-full px-2 py-1 mt-1 rounded cursor-pointer group handle tree-view-item transition-all duration-150 ease-in-out"
                    :class="{
                        'is-selected font-medium': isSelectedNode(element),
                        'text-blue-500':
                            selectable &&
                            modelValue !== undefined &&
                            modelValue.some((x) => x[keyBy] === element[keyBy]),
                    }"
                    @click="onClickRow($event, element)"
                >
                    <!-- Caret placeholder and icon -->
                    <div
                        @click.stop="onClickIcon(element)"
                        class="p-0.5 rounded hover:bg-gray-200 cursor-pointer flex items-center justify-center w-6 h-6 flex-shrink-0 mr-1 text-gray-400 group-hover:text-gray-600 transition-colors"
                        :class="{ 'opacity-0 pointer-events-none': !(element[childrenBy] && element[childrenBy].length > 0) }"
                    >
                        <ph:caret-right-fill v-if="!elementIsActive(element)" class="w-3 h-3" />
                        <ph:caret-down-fill v-else class="w-3 h-3" />
                    </div>

                    <!-- Dynamic Folder/Bullet Icon -->
                    <span class="flex items-center justify-center w-5 h-5 mr-1.5 flex-shrink-0 transition-transform group-hover:scale-105">
                        <template v-if="field.key === 'FileManager'">
                            <ph:folder-open-fill v-if="elementIsActive(element)" class="w-5 h-5 text-amber-500" />
                            <ph:folder-fill v-else class="w-5 h-5 text-amber-500" />
                        </template>
                        <template v-else>
                            <ph:folder-open-light v-if="elementIsActive(element)" class="w-5 h-5 text-gray-400" />
                            <ph:folder-light v-else class="w-5 h-5 text-gray-400" />
                        </template>
                    </span>

                    <!-- Label -->
                    <label
                        class="flex-1 truncate cursor-pointer text-sm text-gray-700 group-hover:text-gray-900 select-none py-1"
                    >
                        {{ element[labelBy] }}
                    </label>
                </div>
                <TreeViewItem
                    :key="element.hash_id || element.slug || element.id"
                    v-show="currentLevel < maxLevel"
                    :level="currentLevel + 1"
                    class="tree-view-sublist"
                    :class="[
                        {
                            'max-h-0 overflow-hidden hidden':
                                !elementIsActive(element),
                        },
                        selectable ? 'ml-2' : '',
                    ]"
                    :active="!elementIsActive(element)"
                    :field="field"
                    :model-value="modelValue"
                    @update:modelValue="$emit('update:modelValue', $event)"
                    v-model:options="element[childrenBy]"
                />
            </li>
        </template>
    </Draggable>
</template>
<script>
import Draggable from "vuedraggable";

export default {
    props: ["modelValue", "field", "level", "active", "options"],
    emits: ["update:modelValue", "change", "update:options"],
    components: {
        Draggable,
    },
    inject: {
        fileManagerContext: {
            default: null,
        },
        productCategoryContext: {
            default: null,
        },
    },
    computed: {
        keyBy() {
            return this.field.keyBy || "id";
        },
        labelBy() {
            return this.field.labelBy || "label";
        },
        childrenBy() {
            return this.field.childrenBy ?? "children";
        },
        currentLevel() {
            return this.level === undefined ? 1 : this.level;
        },
        currentLevelIsActive() {
            if (this.active !== undefined) {
                return this.active;
            } else if (this.currentLevel <= this.expandDefaultLevel) {
                return true;
            } else {
                return false;
            }
        },
        maxLevel() {
            return this.field.maxLevel ?? 3;
        },
        expandDefaultLevel() {
            return this.field.expandDefaultLevel ?? 1;
        },
        draggable() {
            return this.field.draggable ?? false;
        },
        selectable() {
            return this.field.selectable ?? false;
        },
        busKey() {
            return "treeSelectedItem" + (this.field.key ?? "");
        },
        isSelectedNode() {
            return (element) => {
                if (this.fileManagerContext) {
                    return this.fileManagerContext.currentPath === element.path;
                }
                if (this.productCategoryContext) {
                    return this.productCategoryContext.form.id === element.id;
                }
                return false;
            };
        },
    },
    data() {
        return {
            items: [],
        };
    },

    watch: {
        "field.options"() {
            this.fetchSource();
        },
        options() {
            this.items = this.options;
        }
    },

    created() {
        this.fetchSource();
    },

    methods: {
        fetchSource() {
            if (this.currentLevel === 1) {
                if (this.field.options !== undefined) {
                    this.items = this.field.options;
                } else {
                    this.$axios
                        .post(
                            route("admin.helper.model-data"),
                            this.field.source
                        )
                        .then((res) => {
                            this.items = res.data;
                        });
                }
            } else {
                this.items = this.options;
            }
        },
        elementIsActive(element) {
            if (element.active !== undefined) {
                return element.active;
            } else if (this.currentLevel + 1 <= this.expandDefaultLevel) {
                return true;
            } else {
                return false;
            }
        },
        onClickIcon(element) {
            if (!this.selectable) {
                element.active = !this.elementIsActive(element);
            }
        },
        onClickRow(event, element) {
            if (!this.selectable) {
                this.$bus.emit(this.busKey, element);
            }
        },
    },
};
</script>
<style lang="scss">
.ghost {
    @apply opacity-50 border-t;
}
.tree-view-list {
    @apply flex-col;
}
.tree-view-sublist {
    margin-left: 0.85rem;
    padding-left: 0.85rem;
    border-left: 1px dashed rgba(209, 213, 219, 0.6);
}
.tree-view-item {
    border-left: 3px solid transparent;
    &.is-selected {
        background-color: rgba(41, 112, 255, 0.08); /* primary 8% */
        color: #2970FF; /* primary DEFAULT */
        border-left-color: #2970FF;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        
        label {
            color: #1E56CC; /* primary dark */
            font-weight: 500;
        }
    }
    &:not(.is-selected):hover {
        @apply bg-gray-50;
    }
}
</style>
