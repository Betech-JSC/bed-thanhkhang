<template>
    <div
        v-show="show"
        class="fixed bottom-0 right-0 flex flex-col overflow-hidden bg-white"
        :class="[
            embed ? 'left-0' : 'left-0 md:left-[var(--sidebar-width)]',
            selectable || embed ? 'top-0 z-50' : 'top-[57px] md:top-0 z-20 md:z-30',
        ]"
    >
        <input
            type="file"
            class="hidden"
            accept="image/png, image/gif, image/jpeg, image/svg+xml, application/pdf ,image/webp"
            multiple="true"
            ref="file"
            @change="fileChange"
        />

        <!-- Standard Toolbar -->
        <header
            v-if="!embed"
            class="relative z-30 shrink-0 bg-white border-b border-gray-200 px-3 py-2.5 sm:px-6 lg:px-8 flex flex-col gap-2.5"
        >
            <div class="flex items-center justify-between gap-2 min-w-0">
                <div class="flex items-center gap-1 min-w-0">
                    <button
                        v-if="selectable"
                        type="button"
                        class="inline-flex items-center justify-center min-w-[40px] min-h-[40px] -ml-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary/40 shrink-0"
                        :aria-label="tt('models.files.cancel')"
                        @click="close"
                    >
                        <ph-caret-left class="w-5 h-5" />
                    </button>
                    <h1 class="text-base sm:text-lg font-semibold text-gray-800 whitespace-nowrap shrink-0">
                        {{ tt('models.files.file_manager') }}
                    </h1>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button
                        type="button"
                        class="lg:hidden inline-flex items-center justify-center gap-1.5 h-9 px-2.5 sm:px-3 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded shadow-sm hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/40 whitespace-nowrap shrink-0"
                        @click="showFolderDrawer = true"
                    >
                        <ph:folder-notch-open class="w-4 h-4 text-primary shrink-0" />
                        <span>Thư mục</span>
                    </button>
                    <Button
                        @click.prevent="browse"
                        class="btn-primary whitespace-nowrap shrink-0 !min-w-0 !px-2.5 sm:!px-3 !text-xs sm:!text-sm"
                    >
                        <ph:upload-simple class="w-4 h-4 shrink-0" />
                        <span>{{ tt('models.files.select_file') }}</span>
                    </Button>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-2">
                <input
                    type="text"
                    :value="searchInput"
                    :placeholder="tt('models.files.input_file')"
                    class="w-full sm:w-64 xl:w-72 py-1.5 px-3 border border-gray-300 focus:border-solid focus:outline-none focus:ring-0 rounded text-base sm:text-sm hover:border-gray-400 focus:border-gray-500"
                    @input="onChange"
                />

                <div class="flex items-center justify-between sm:justify-end gap-2 w-full sm:w-auto shrink-0">
                    <Button
                        @click.prevent="showFolderModal = true"
                        class="btn-outline-primary whitespace-nowrap shrink-0 !min-w-0 !px-3 flex-1 sm:flex-initial"
                    >
                        <ph-plus-circle-light class="w-4 h-4 shrink-0" />
                        <span>{{ tt('models.files.add_folder') }}</span>
                    </Button>

                    <!-- Compact Actions Menu (< xl) -->
                    <div ref="actionsMenuWrapper" class="relative xl:hidden flex-1 sm:flex-initial">
                        <button
                            type="button"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 h-9 px-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded shadow-sm hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/40 whitespace-nowrap shrink-0"
                            :aria-expanded="showActionsMenu"
                            aria-haspopup="menu"
                            @click.stop="showActionsMenu = !showActionsMenu"
                        >
                            <span>Thao tác</span>
                            <span
                                v-if="clipboard.length > 0"
                                class="inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-semibold rounded-full bg-primary-50 text-primary"
                            >
                                {{ clipboard.length }}
                            </span>
                            <ph:caret-down
                                class="w-4 h-4 text-gray-500 transition-transform shrink-0"
                                :class="{ 'rotate-180': showActionsMenu }"
                            />
                        </button>

                        <div
                            v-if="showActionsMenu"
                            role="menu"
                            class="absolute right-0 top-full mt-1.5 z-30 w-60 max-w-[calc(100vw-1.5rem)] bg-white border border-gray-200 rounded-md shadow-lg py-1"
                        >
                            <button
                                type="button"
                                role="menuitem"
                                class="w-full min-h-[40px] px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:pointer-events-none flex items-center justify-between gap-2"
                                :disabled="currentPath === '/'"
                                @click="handleActionMenu(() => renameCurrentFolder())"
                            >
                                <span>{{ tt('models.files.rename_current_folder') }}</span>
                            </button>
                            <button
                                type="button"
                                role="menuitem"
                                class="w-full min-h-[40px] px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:pointer-events-none flex items-center justify-between gap-2"
                                :disabled="selectedFiles.length !== 1"
                                @click="handleActionMenu(() => showRename())"
                            >
                                <span>{{ tt('models.files.rename') }}</span>
                            </button>
                            <button
                                type="button"
                                role="menuitem"
                                class="w-full min-h-[40px] px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:pointer-events-none flex items-center justify-between gap-2"
                                :disabled="selectedFiles.length === 0"
                                @click="handleActionMenu(() => copyToClipboard())"
                            >
                                <span>{{ tt('models.files.copy') }}</span>
                            </button>
                            <button
                                type="button"
                                role="menuitem"
                                class="w-full min-h-[40px] px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:pointer-events-none flex items-center justify-between gap-2"
                                :disabled="selectedFiles.length === 0"
                                @click="handleActionMenu(() => cutToClipboard())"
                            >
                                <span>{{ tt('models.files.cut') }}</span>
                            </button>
                            <div class="my-1 border-t border-gray-100"></div>
                            <button
                                type="button"
                                role="menuitem"
                                class="w-full min-h-[40px] px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 disabled:opacity-40 disabled:pointer-events-none flex items-center justify-between gap-2"
                                :disabled="clipboard.length === 0 || isPasting"
                                @click="handleActionMenu(() => pasteFromClipboard())"
                            >
                                <span>{{ isPasting ? tt('models.files.pasting') : tt('models.files.paste') }}</span>
                                <span
                                    v-if="clipboard.length > 0"
                                    class="text-xs font-medium text-gray-500"
                                >
                                    ({{ clipboard.length }})
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Desktop Inline Actions (>= xl) -->
                    <div class="hidden xl:flex items-center gap-2 shrink-0">
                        <Button
                            @click="renameCurrentFolder"
                            class="btn-outline-primary whitespace-nowrap shrink-0 !min-w-0 !px-3"
                            :disabled="currentPath === '/'"
                        >
                            <ph:folder-notch-open class="w-4 h-4 shrink-0" />
                            <span>{{ tt('models.files.rename_current_folder') }}</span>
                        </Button>
                        <Button
                            @click="showRename()"
                            class="btn-outline-primary whitespace-nowrap shrink-0 !min-w-0 !px-3"
                            :disabled="selectedFiles.length !== 1"
                        >
                            <ph:pencil-simple class="w-4 h-4 shrink-0" />
                            <span>{{ tt('models.files.rename') }}</span>
                        </Button>
                        <Button
                            @click="copyToClipboard()"
                            class="btn-outline-primary whitespace-nowrap shrink-0 !min-w-0 !px-3"
                            :disabled="selectedFiles.length === 0"
                        >
                            <ph:copy class="w-4 h-4 shrink-0" />
                            <span>{{ tt('models.files.copy') }}</span>
                        </Button>
                        <Button
                            @click="cutToClipboard()"
                            class="btn-outline-primary whitespace-nowrap shrink-0 !min-w-0 !px-3"
                            :disabled="selectedFiles.length === 0"
                        >
                            <ph:scissors class="w-4 h-4 shrink-0" />
                            <span>{{ tt('models.files.cut') }}</span>
                        </Button>
                        <Button
                            @click="pasteFromClipboard()"
                            class="btn-outline-primary whitespace-nowrap shrink-0 !min-w-0 !px-3"
                            :disabled="clipboard.length === 0 || isPasting"
                        >
                            <ph:clipboard class="w-4 h-4 shrink-0" />
                            <span>
                                {{ isPasting ? tt('models.files.pasting') : tt('models.files.paste') }}
                                {{ clipboard.length > 0 ? `(${clipboard.length})` : '' }}
                            </span>
                        </Button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Compact Header Bar for Embed Mode -->
        <header
            v-else
            class="relative z-30 shrink-0 bg-white border-b border-gray-200 px-3 py-2 sm:px-4 flex flex-wrap items-center justify-between gap-2"
        >
            <div class="flex flex-wrap items-center gap-2">
                <button
                    type="button"
                    class="lg:hidden inline-flex items-center justify-center gap-1.5 h-9 px-2.5 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded shadow-sm hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/40 whitespace-nowrap shrink-0"
                    @click="showFolderDrawer = true"
                >
                    <ph:folder-notch-open class="w-4 h-4 text-primary shrink-0" />
                    <span>Thư mục</span>
                </button>
                <Button
                    @click.prevent="browse"
                    class="btn-primary whitespace-nowrap shrink-0 !min-w-0 !px-2.5 sm:!px-3 !text-xs sm:!text-sm"
                >
                    <ph:upload-simple class="w-4 h-4 shrink-0" />
                    <span>{{ tt('models.files.select_file') }}</span>
                </Button>
                <Button
                    @click.prevent="showFolderModal = true"
                    class="btn-outline-primary whitespace-nowrap shrink-0 !min-w-0 !px-2.5 sm:!px-3 !text-xs sm:!text-sm"
                >
                    <ph-plus-circle-light class="w-4 h-4 shrink-0" />
                    <span>{{ tt('models.files.add_folder') }}</span>
                </Button>
            </div>
            <input
                type="text"
                :value="searchInput"
                :placeholder="tt('models.files.input_file')"
                class="w-full sm:w-56 md:w-64 py-1.5 px-3 border border-gray-300 focus:border-solid focus:outline-none focus:ring-0 rounded text-base sm:text-sm hover:border-gray-400 focus:border-gray-500"
                @input="onChange"
            />
        </header>

        <div
            class="relative flex flex-1 min-h-0 overflow-hidden"
            @dragover.prevent="isDragging = true"
        >
            <div
                class="absolute inset-0 overflow-hidden border-4 border-dashed before:absolute before:bg-green-300 before:bg-opacity-25 before:inset-0 before:z-10"
                :class="
                    isDragging
                        ? 'z-40 border-green-300 before:visible visible pointer-events-auto'
                        : '-z-10 border-transparent before:invisible invisible pointer-events-none'
                "
                @dragleave.prevent="isDragging = false"
                @drop.prevent=";(isDragging = false), drop($event)"
            ></div>

            <!-- Mobile / Tablet Folder Drawer (< lg) -->
            <div
                v-if="showFolderDrawer"
                class="fixed inset-0 z-40 flex lg:hidden"
                role="dialog"
                aria-modal="true"
                aria-label="Thư mục"
            >
                <div
                    class="fixed inset-0 bg-black/40 transition-opacity"
                    @click="showFolderDrawer = false"
                ></div>
                <div class="relative z-10 flex flex-col w-72 max-w-[85vw] h-full bg-white shadow-xl border-r border-gray-200">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 shrink-0">
                        <span class="text-base font-semibold text-gray-800">Thư mục</span>
                        <button
                            type="button"
                            class="inline-flex items-center justify-center w-10 h-10 -mr-2 rounded-md text-gray-500 hover:text-gray-800 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary/40"
                            aria-label="Đóng danh sách thư mục"
                            @click="showFolderDrawer = false"
                        >
                            <ph:x class="w-5 h-5" />
                        </button>
                    </div>
                    <div class="flex-1 min-h-0 p-4 overflow-y-auto">
                        <Field
                            v-if="tree && tree.length"
                            :field="{
                                key: 'FileManager',
                                label: false,
                                type: 'tree',
                                maxLevel: 10,
                                expandDefaultLevel: 2,
                                keyBy: 'slug',
                                labelBy: 'name',
                                childrenBy: 'children',
                                options: tree,
                                draggable: true,
                            }"
                        />
                    </div>
                </div>
            </div>

            <!-- Desktop Folder Sidebar (>= lg) -->
            <aside class="hidden lg:block w-72 xl:w-80 shrink-0 p-5 pb-16 overflow-y-auto bg-white border-r border-gray-200">
                <Field
                    v-if="tree && tree.length"
                    :field="{
                        key: 'FileManager',
                        label: false,
                        type: 'tree',
                        maxLevel: 10,
                        expandDefaultLevel: 2,
                        keyBy: 'slug',
                        labelBy: 'name',
                        childrenBy: 'children',
                        options: tree,
                        draggable: true,
                    }"
                />
            </aside>

            <main
                ref="scrollContainer"
                class="flex-1 min-w-0 flex flex-col overflow-y-auto group-image-admin"
                :class="{ 'pb-24': canSelectMultiple && selectedFiles.length > 0 }"
            >
                <!-- Breadcrumb Navigation -->
                <div class="w-full px-3 pt-3 sm:px-6 sm:pt-4 lg:px-8 mx-auto max-w-7xl shrink-0">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm min-w-0" aria-label="Breadcrumb">
                            <button
                                type="button"
                                @click="navigateToPath('/')"
                                class="text-blue-600 hover:text-blue-800 cursor-pointer flex items-center justify-center min-w-[32px] min-h-[32px] rounded hover:bg-gray-100"
                                aria-label="Thư mục gốc"
                            >
                                <ph:house class="w-4 h-4" />
                            </button>
                            <template v-for="(part, index) in pathParts" :key="index">
                                <span class="text-gray-400">/</span>
                                <button
                                    type="button"
                                    @click="navigateToPath(getPathUpToIndex(index))"
                                    class="text-left break-all text-blue-600 hover:text-blue-800 cursor-pointer py-1"
                                    :class="{ 'font-semibold text-gray-900': index === pathParts.length - 1 }"
                                >
                                    {{ part }}
                                </button>
                            </template>
                        </nav>

                        <button
                            v-if="currentPath !== '/'"
                            type="button"
                            class="inline-flex items-center gap-1.5 min-h-[36px] px-2.5 py-1 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/40 whitespace-nowrap shrink-0"
                            :title="tt('models.files.go_up')"
                            @click="navigateUp"
                        >
                            <ph:arrow-bend-up-left class="w-4 h-4 shrink-0" />
                            <span>{{ tt('models.files.go_up') }}</span>
                        </button>
                    </div>
                </div>

                <!-- Empty Folder State -->
                <div
                    v-if="canDeleteFolder"
                    class="flex-1 flex flex-col items-center justify-center px-4 py-10 text-center"
                >
                    <h2 class="text-base sm:text-xl font-medium text-gray-700 max-w-md">
                        {{ tt('models.files.drop') }}
                        <a @click="browse" class="link cursor-pointer">{{ tt('models.files.click_here').toLowerCase() }}</a>
                        {{ tt('models.files.select_files').toLowerCase() }}
                    </h2>
                    <div v-if="currentPath !== '/'" class="mt-6">
                        <Button @click="deleteFolder" class="space-x-2 btn-outline-primary whitespace-nowrap">
                            <carbon:subtract-alt class="w-4 h-4 shrink-0" />
                            <span>{{ tt('models.files.delete_folder') }}</span>
                        </Button>
                    </div>
                </div>

                <!-- Files & Folders Grid -->
                <div
                    v-if="searchFiles && searchFiles.length"
                    class="w-full px-3 pt-4 pb-16 mx-auto space-y-4 max-w-7xl sm:px-6 lg:px-8"
                >
                    <ul
                        role="list"
                        class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6 gap-3 sm:gap-4"
                        v-if="data"
                    >
                        <li
                            class="relative min-w-0"
                            v-for="file in searchFiles"
                            :key="file.path || file.static_url"
                            @contextmenu.prevent="openContextMenu($event, file)"
                            draggable="true"
                            @dragstart="onDragStart($event, file)"
                            @dragend="onDragEnd"
                            @dragover.prevent="onDragOver($event, file)"
                            @dragleave="onDragLeave($event, file)"
                            @drop.prevent="onDrop($event, file)"
                        >
                            <div
                                class="group w-full rounded bg-gray-100 overflow-hidden aspect-[1/1] flex cursor-pointer justify-center items-center border border-transparent hover:border-gray-400 relative outline outline-offset-2 outline-2 transition-all"
                                :class="{
                                    'outline-black': selectedFiles.includes(file),
                                    'outline-transparent': !selectedFiles.includes(file),
                                    'opacity-50': draggedItem && draggedItem.path === file.path,
                                    'ring-4 ring-blue-400 ring-offset-2 bg-blue-50':
                                        dropTarget && dropTarget.path === file.path && file.type === 'dir',
                                }"
                                @click="onSelect(file)"
                                @dblclick="onDoubleClick(file)"
                            >
                                <button
                                    type="button"
                                    class="absolute top-1.5 left-1.5 z-10 w-9 h-9 min-w-[36px] min-h-[36px] rounded-md bg-white/90 hover:bg-white text-gray-700 shadow-sm border border-gray-200/80 flex items-center justify-center opacity-100 lg:opacity-0 lg:group-hover:opacity-100 lg:group-focus-within:opacity-100 transition-opacity focus:outline-none focus:ring-2 focus:ring-primary/40"
                                    :aria-label="`${tt('models.files.rename')} / ${tt('models.files.copy')} ${file.filename}`"
                                    @click.stop="openContextMenu($event, file)"
                                >
                                    <ph:dots-three-vertical class="w-4 h-4" />
                                </button>

                                <button
                                    v-if="file.type === 'dir'"
                                    type="button"
                                    class="absolute bottom-1.5 left-1.5 z-10 min-h-[36px] px-2.5 rounded-md bg-white/95 hover:bg-white text-gray-800 text-xs font-medium shadow-sm border border-gray-200/90 flex items-center justify-center opacity-100 lg:opacity-0 lg:group-hover:opacity-100 lg:group-focus-within:opacity-100 transition-opacity focus:outline-none focus:ring-2 focus:ring-primary/40"
                                    @click.stop="navigateToFolder(file)"
                                >
                                    {{ tt('models.files.open') }}
                                </button>

                                <Thumbnail :file="file" @remove="onRemove(file)" />
                            </div>
                            <p class="block mt-2 text-sm font-medium text-gray-900 truncate pointer-events-none">
                                {{ file.filename }}
                            </p>
                            <p class="block space-x-1 text-xs font-medium text-gray-500 pointer-events-none">
                                <span class="uppercase">
                                    {{ file.extension }}
                                </span>
                                <span>•</span>
                                <span>
                                    {{ file.formatted_file_size }}
                                </span>
                            </p>
                        </li>
                    </ul>

                    <Pagination v-if="data" :links="data.links" @changePage="getFiles($event)" />
                </div>
            </main>
        </div>

        <div
            v-if="canSelectMultiple && selectedFiles.length > 0"
            class="absolute bottom-0 left-0 right-0 z-20 flex items-center justify-between sm:justify-center gap-3 w-full min-h-[4rem] px-4 pt-2.5 pb-[calc(0.625rem+env(safe-area-inset-bottom))] bg-white border-t border-gray-200 shadow-sm"
        >
            <Button class="whitespace-nowrap !min-w-0 !px-4" @click="selectedFiles = []">
                {{ tt('models.files.unchecked') }}
            </Button>
            <Button class="btn-primary whitespace-nowrap !min-w-0 !px-4" @click="submitFileSelect()">
                {{ tt('models.files.select') }} ({{ selectedFiles.length }})
            </Button>
        </div>

        <Dialog
            :header="tt('models.files.add_folder')"
            v-model:visible="showFolderModal"
            :modal="true"
            :draggable="false"
            :breakpoints="{
                '640px': '95vw',
            }"
            :style="{ width: '32rem', maxWidth: '95vw' }"
        >
            <Field
                v-model="folderForm.name"
                :field="{
                    rules: 'required',
                    name: 'name',
                    label: tt('models.files.new_name'),
                }"
            />
            <template #footer>
                <Button variant="white" @click="showFolderModal = false" :label="tt('models.files.cancel')" />
                <Button
                    type="button"
                    class="btn-primary ml-2"
                    @click="createFolder(folderForm.name)"
                    :label="tt('models.files.save')"
                />
            </template>
        </Dialog>

        <Dialog
            :header="tt('models.files.rename')"
            v-model:visible="showRenameModal"
            :modal="true"
            :draggable="false"
            :breakpoints="{
                '640px': '95vw',
            }"
            :style="{ width: '32rem', maxWidth: '95vw' }"
        >
            <Field
                v-model="renameForm.newName"
                :field="{
                    rules: 'required',
                    name: 'newName',
                    label: tt('models.files.new_name'),
                }"
            />
            <template #footer>
                <Button variant="white" @click="showRenameModal = false" :label="tt('models.files.cancel')" />
                <Button type="button" class="btn-primary ml-2" @click="renameFile" :label="tt('models.files.save')" />
            </template>
        </Dialog>

        <ContextMenu ref="contextMenu" :model="contextMenuItems" />
    </div>
</template>
<script>
import Pagination from '@Core/Components/Pagination.vue'
import Thumbnail from '@Core/Components/Thumbnail.vue'
import ContextMenu from 'primevue/contextmenu'

const MAX_SIZE_OF_IMAGE = 5
const MAX_SIZE_OF_VIDEO = 50

export default {
    components: { Thumbnail, Pagination, ContextMenu },
    provide() {
        return {
            fileManagerContext: this,
        }
    },
    props: {
        show: {
            default: false,
        },
        multiple: {
            default: false,
        },
        selectable: {
            default: true,
        },
    },

    setup(_props, { emit }) {
        const close = () => {
            emit('update:show', false)
        }

        return {
            close,
        }
    },

    data() {
        return {
            uploadingFiles: [],
            selectedFiles: [],
            isDragging: false,
            timer: null,
            timerScoll: null,
            data: {
                tree: null,
                directories: [],
                files: [],
            },
            tree: null,
            currentPath: '/',
            showFolderModal: false,
            showFolderDrawer: false,
            showActionsMenu: false,
            folderForm: {
                name: null,
            },
            search: null,
            searchInput: '',
            embed: Boolean(this.$page.props.route?.query?.embed && this.$page.props.route.query.embed !== 'false'),
            selectMultiple: this.$page.props.route?.query?.['select-multiple'] == 'true',
            limit: 50,
            page: 1,
            fetchData: true,
            showRenameModal: false,
            renameForm: {
                path: null,
                newName: null,
                item: null,
            },
            clipboard: [],
            clipboardAction: null,
            contextMenuItems: [],
            selectedContextFile: null,
            isPasting: false,
            draggedItem: null,
            dropTarget: null,
            onTreeSelectHandler: null,
        }
    },

    created() {
        this.getFiles()
    },

    mounted() {
        this.onTreeSelectHandler = (item) => {
            this.selectedItem(item)
        }
        this.$bus.on('treeSelectedItemFileManager', this.onTreeSelectHandler)

        const scrollContainer = this.$refs.scrollContainer
        if (scrollContainer) {
            scrollContainer.addEventListener('scroll', this.handleScroll)
        }

        document.addEventListener('keydown', this.handleKeyboard)
        document.addEventListener('click', this.handleDocumentClick)
    },

    unmounted() {
        const scrollContainer = this.$refs.scrollContainer
        if (scrollContainer) {
            scrollContainer.removeEventListener('scroll', this.handleScroll)
        }

        if (this.onTreeSelectHandler) {
            this.$bus.off('treeSelectedItemFileManager', this.onTreeSelectHandler)
        }

        if (this.timerScoll) {
            clearTimeout(this.timerScoll)
            this.timerScoll = null
        }

        if (this.timer) {
            clearTimeout(this.timer)
            this.timer = null
        }

        document.removeEventListener('keydown', this.handleKeyboard)
        document.removeEventListener('click', this.handleDocumentClick)
    },

    watch: {
        show(value) {
            if (value && this.data === null) {
                this.getFiles()
            }
        },
    },

    computed: {
        searchFiles() {
            if (!this.data || !this.data.files) {
                if (!this.search) {
                    this.getFiles()
                    return []
                }
                return []
            }

            // Merge directories and files
            const directories = this.data.directories || []
            // Handle files as object or array
            const filesData = this.data.files || []
            const files = Array.isArray(filesData) ? filesData : Object.values(filesData)

            const allItems = [...directories, ...files]

            if (!this.search) return allItems

            this.getFiles({ keyword: this.search })
            this.fetchData = false

            return allItems
        },
        canDeleteFolder() {
            if (!this.data) return false

            const files = this.data.files || []
            const filesArray = Array.isArray(files) ? files : Object.values(files)
            const directories = this.data.directories || []

            return filesArray.length === 0 && directories.length === 0
        },
        canSelectMultiple() {
            return this.multiple || this.selectMultiple
        },
        pathParts() {
            if (this.currentPath === '/') return []
            return this.currentPath.split('/').filter(Boolean)
        },
    },

    methods: {
        handleScroll() {
            if (this.timerScoll) {
                clearTimeout(this.timerScoll)
                this.timerScoll = null
            }

            if (this.fetchData) {
                this.timerScoll = setTimeout(this.scrollImage, 300)
            }
        },
        handleDocumentClick(event) {
            if (
                this.showActionsMenu &&
                this.$refs.actionsMenuWrapper &&
                !this.$refs.actionsMenuWrapper.contains(event.target)
            ) {
                this.showActionsMenu = false
            }
        },
        handleActionMenu(callback) {
            this.showActionsMenu = false
            callback()
        },
        scrollImage() {
            this.page = this.page + 1
            this.getFiles({ page: this.page })
        },
        selectedItem(item) {
            this.currentPath = item.path
            this.data.files = []
            this.data.directories = []
            this.search = null
            this.searchInput = ''
            this.selectedFiles = []
            this.showFolderDrawer = false
            this.getFiles({}, true)
            this.page = 1
            this.fetchData = true
        },
        getFiles(params = {}, loadTree = false) {
            if (this.fetchData) {
                const requestPage = Number(params.page ?? this.page ?? 1)
                this.$axios
                    .get(
                        this.route('admin.files.index', {
                            page: 1,
                            limit: this.limit,
                            search: null,
                            path: this.currentPath,
                            ...params,
                        })
                    )
                    .then((res) => {
                        let files = this.data.files
                        let new_files = res.data.files ? res.data.files : null

                        // Convert new_files from object to array if needed
                        if (new_files && !Array.isArray(new_files)) {
                            new_files = Object.values(new_files)
                        }

                        // Convert existing files from object to array if needed
                        if (files && !Array.isArray(files)) {
                            files = Object.values(files)
                        }

                        const normalizedNewFiles = Array.isArray(new_files) ? new_files : []

                        if (requestPage <= 1) {
                            this.data = {
                                tree: res.data.tree,
                                directories: res.data.directories || [],
                                files: normalizedNewFiles,
                            }
                            if (!this.tree || loadTree) {
                                this.tree = res.data.tree
                            }
                            if (normalizedNewFiles.length === 0) {
                                this.fetchData = false
                            }
                        } else if (normalizedNewFiles.length === 0) {
                            this.fetchData = false
                        } else {
                            files = [...(files || []), ...normalizedNewFiles]
                            this.data = {
                                tree: res.data.tree,
                                directories: res.data.directories || [],
                                files: files,
                            }

                            if (!this.tree || loadTree) {
                                this.tree = res.data.tree
                            }
                        }
                    })
            }
        },
        async copyUrl(file) {
            try {
                await navigator.clipboard.writeText(this.staticUrl(file.static_url))
            } catch ($e) {}
        },
        submitToParentIframe() {
            let htmlImages = ''
            this.selectedFiles.forEach((file) => {
                if (file.type === 'dir' || !file.static_url) return
                let src = '/static' + new URL(file.static_url).pathname
                src = src.replace('/static/static/', '/static/')
                htmlImages += `<img src="${src}">`
            })

            window.parent.postMessage({
                mceAction: 'insertContent',
                content: htmlImages,
            })
            window.parent.postMessage({
                mceAction: 'close',
            })
            return
        },

        onSelect(file) {
            if (this.embed) {
                if (this.canSelectMultiple) {
                    this.toggleFileSelect(file)
                } else {
                    if (file.type === 'dir') {
                        this.navigateToFolder(file)
                        return
                    }
                    this.selectedFiles = [file]
                    this.submitToParentIframe()
                }
                return
            }

            if (!this.selectable && file.type !== 'dir') return

            // Allow selecting folders for rename/copy/move operations
            if (!this.canSelectMultiple && file.type === 'file') {
                this.selectedFiles[0] = file
                this.submitFileSelect()
            } else {
                this.toggleFileSelect(file)
            }
        },
        onDoubleClick(file) {
            // Double-click on folder to navigate
            if (file.type === 'dir') {
                this.navigateToFolder(file)
            }
        },
        navigateToFolder(folder) {
            this.navigateToPath(folder.path)
        },
        navigateToPath(path) {
            this.currentPath = path
            this.data.files = []
            this.data.directories = []
            this.search = null
            this.searchInput = ''
            this.selectedFiles = []
            this.showFolderDrawer = false
            this.getFiles({}, true)
            this.page = 1
            this.fetchData = true
        },
        getPathUpToIndex(index) {
            const parts = this.pathParts.slice(0, index + 1)
            return '/' + parts.join('/')
        },
        navigateUp() {
            if (this.currentPath === '/') return

            const parts = this.currentPath.split('/').filter(Boolean)
            parts.pop() // Remove last part

            const parentPath = parts.length > 0 ? '/' + parts.join('/') : '/'
            this.navigateToPath(parentPath)
        },
        toggleFileSelect(file) {
            if (!this.selectedFiles.includes(file)) {
                this.selectedFiles.push(file)
            } else {
                const fileIndex = this.selectedFiles.indexOf(file)
                this.selectedFiles.splice(fileIndex, 1)
            }
        },
        submitFileSelect() {
            if (this.embed) {
                if (this.canSelectMultiple) {
                    return this.submitToParentIframe()
                }
                return
            }
            this.$emit('on-select', this.selectedFiles)
            this.selectedFiles = []
            this.$emit('update:show', false)
        },
        browse() {
            this.$refs.file.click()
        },
        drop(event) {
            this.uploadFiles(event.dataTransfer.files)
        },
        fileChange() {
            this.uploadFiles(this.$refs.file.files)
        },
        uploadFiles(images) {
            if (images.length === 0) return

            for (const image of images) {
                const fileCheck = this.fileCheck(image)
                if (!fileCheck.valid) {
                    alert(
                        this.tt('models.files.maximum_size') +
                            ' ' +
                            fileCheck.maxSize +
                            this.tt('models.files.try_again')
                    )
                    this.$refs.file.value = ''
                    return false
                }
            }

            var formData = new FormData()
            for (let index = 0; index < images.length; index++) {
                const image = images[index]

                if (this.isImage(image.name)) {
                    const reader = new FileReader()
                    reader.onload = (e) => {
                        this.uploadingFiles.push({
                            filename: image.name,
                            base64_code: e.target.result,
                        })
                    }
                    reader.readAsDataURL(image)
                } else {
                    this.uploadingFiles.push({
                        filename: image.name,
                        base64_code: null,
                        size: image.size,
                    })
                }
                formData.append('files[' + index + ']', image)
                formData.append('path', this.currentPath)
            }
            this.$refs.file.value = ''
            this.postFiles(formData)
        },
        postFiles(formData) {
            this.$axios.post(this.route('admin.files.store'), formData).then((response) => {
                if (response.status === 200) {
                    this.getFiles()
                    if (response.data && response.data.failureFiles && response.data.failureFiles.length > 0) {
                        const failedCount = response.data.failureFiles.length
                        const failedNames = response.data.failureFiles.join(', ')
                        const message = `Tải lên thất bại ${failedCount} file: ${failedNames}. Vui lòng kiểm tra định dạng, dung lượng file (tối đa 5MB cho ảnh) hoặc quyền ghi thư mục.`
                        if (this.$toast) {
                            this.$toast.add({
                                severity: 'error',
                                summary: 'Tải lên thất bại',
                                detail: message,
                                life: 10000,
                            })
                        } else {
                            alert(message)
                        }
                    }
                }
            })
        },
        onRemove(file) {
            this.$axios
                .post(this.route('admin.files.destroy', { id: 0 }), {
                    files: [file],
                })
                .then((response) => {
                    if (response.status === 200) {
                        this.selectedFiles = (this.selectedFiles || []).filter(
                            (selected) =>
                                (selected.path && selected.path !== file.path) ||
                                (selected.static_url && selected.static_url !== file.static_url)
                        )
                        this.data.files = []
                        this.page = 1
                        this.fetchData = true
                        this.getFiles({ page: 1 }, true)
                    }
                })
        },
        fileCheck(file) {
            const maxSize = this.isImage(file.name) ? MAX_SIZE_OF_IMAGE : MAX_SIZE_OF_VIDEO
            const fileSize = file.size / 1024 / 1024

            return { valid: fileSize <= maxSize, maxSize }
        },
        onChange(event) {
            this.searchInput = event.target.value
            if (this.timer) {
                clearTimeout(this.timer)
                this.timer = null
            }

            this.timer = setTimeout(() => {
                const nextSearch = this.searchInput.trim()
                const hadSearch = Boolean(this.search)
                this.search = nextSearch || null
                this.page = 1
                this.fetchData = true
                if (!this.search && hadSearch) {
                    this.getFiles({}, true)
                }
            }, 500)
        },
        createFolder(name) {
            const trimmedName = (name ?? '').trim()
            if (!trimmedName) {
                if (this.$toast) {
                    this.$toast.add({
                        severity: 'warn',
                        summary: this.tt('models.files.add_folder'),
                        detail: 'Vui lòng nhập tên thư mục.',
                        life: 3000,
                    })
                }
                return
            }

            this.$axios
                .post(this.route('admin.files.folders.create'), {
                    name: trimmedName,
                    path: this.currentPath,
                })
                .then(() => {
                    this.getFiles({}, true)
                    this.folderForm.name = null
                    this.showFolderModal = false
                })
        },
        deleteFolder() {
            if (confirm(this.tt('models.files.confirm_delete')) == true) {
                this.$axios
                    .post(
                        this.route('admin.files.folders.delete', {
                            path: this.currentPath,
                        })
                    )
                    .then(() => {
                        this.currentPath = '/'
                        this.fetchData = true
                        this.getFiles({}, true)
                    })
            }
        },
        renameCurrentFolder() {
            if (this.currentPath === '/') return

            // Create a fake folder object for current path
            const currentFolder = {
                path: this.currentPath,
                filename: this.currentPath.split('/').filter(Boolean).pop(),
                type: 'dir',
            }

            this.showRename(currentFolder)
        },
        showRename(file) {
            if (!file || file instanceof Event || !file.path) {
                file = this.selectedFiles.length === 1 ? this.selectedFiles[0] : null
            }

            if (!file) return

            this.renameForm.item = file
            this.renameForm.path = file.path

            const filename = file.filename
            const lastDotIndex = filename.lastIndexOf('.')
            // For folders, don't look for extension
            this.renameForm.newName =
                file.type === 'dir' || lastDotIndex <= 0 ? filename : filename.substring(0, lastDotIndex)

            this.showRenameModal = true
        },
        renameFile() {
            const trimmedName = (this.renameForm.newName ?? '').trim()
            if (!trimmedName) {
                if (this.$toast) {
                    this.$toast.add({
                        severity: 'warn',
                        summary: this.tt('models.files.rename'),
                        detail: 'Vui lòng nhập tên mới.',
                        life: 3000,
                    })
                }
                return
            }

            const isRenamingCurrentFolder = this.renameForm.path === this.currentPath

            this.$axios
                .post(this.route('admin.files.rename'), {
                    path: this.renameForm.path,
                    newName: trimmedName,
                })
                .then((res) => {
                    if (res.data.success) {
                        // If we renamed the current folder, navigate to the new path
                        if (isRenamingCurrentFolder && res.data.newPath) {
                            this.currentPath = res.data.newPath
                        }

                        this.getFiles({}, true)
                        this.showRenameModal = false
                        this.renameForm = { path: null, newName: null, item: null }
                    } else {
                        alert(res.data.message)
                    }
                })
                .catch(() => {
                    alert(this.tt('models.files.rename_error'))
                })
        },
        copyToClipboard(files = null) {
            const filesToCopy = !files || files instanceof Event ? this.selectedFiles : files

            if (!filesToCopy || filesToCopy.length === 0) {
                return
            }

            try {
                // Ensure filesToCopy is an array
                this.clipboard = Array.isArray(filesToCopy) ? [...filesToCopy] : [filesToCopy]
                this.clipboardAction = 'copy'

                // Show notification
                if (this.$toast) {
                    this.$toast.add({
                        severity: 'success',
                        summary: this.tt('models.files.copied'),
                        detail: `${this.clipboard.length} ${this.tt('models.files.items_copied')}`,
                        life: 3000,
                    })
                }
            } catch (error) {
                console.error('Error copying to clipboard:', error)
            }
        },
        cutToClipboard(files = null) {
            const filesToCut = !files || files instanceof Event ? this.selectedFiles : files

            if (!filesToCut || filesToCut.length === 0) {
                return
            }

            try {
                // Ensure filesToCut is an array
                this.clipboard = Array.isArray(filesToCut) ? [...filesToCut] : [filesToCut]
                this.clipboardAction = 'cut'

                // Show notification
                if (this.$toast) {
                    this.$toast.add({
                        severity: 'success',
                        summary: this.tt('models.files.cut'),
                        detail: `${this.clipboard.length} ${this.tt('models.files.items_cut')}`,
                        life: 3000,
                    })
                }
            } catch (error) {
                console.error('Error cutting to clipboard:', error)
            }
        },
        pasteFromClipboard() {
            this.performPaste(this.currentPath)
        },
        pasteIntoFolder(folder) {
            this.performPaste(folder.path)
        },
        performPaste(destinationPath) {
            if (this.clipboard.length === 0 || this.isPasting) {
                return
            }

            try {
                this.isPasting = true

                const items = this.clipboard.map((file) => ({
                    path: file.path,
                    type: file.type,
                }))

                const routeName = this.clipboardAction === 'copy' ? 'admin.files.copy' : 'admin.files.move'

                this.$axios
                    .post(this.route(routeName), {
                        items: items,
                        destinationPath: destinationPath,
                    })
                    .then((res) => {
                        if (!res.data) {
                            console.error('No response data from paste operation')
                            return
                        }

                        const results = Array.isArray(res.data) ? res.data : []
                        const successCount = results.filter((r) => r && r.success).length
                        const failCount = results.filter((r) => r && !r.success).length

                        // Show notifications
                        if (successCount > 0) {
                            const message = `${successCount} ${this.tt('models.files.items_pasted')}`

                            if (this.$toast) {
                                this.$toast.add({
                                    severity: 'success',
                                    summary: this.tt('models.files.paste_success'),
                                    detail: message,
                                    life: 3000,
                                })
                            } else {
                                console.log('Paste success:', message)
                            }
                        }

                        if (failCount > 0) {
                            const errorMessage = `${failCount} ${this.tt('models.files.items_failed')}`

                            if (this.$toast) {
                                this.$toast.add({
                                    severity: 'error',
                                    summary: this.tt('models.files.paste_error'),
                                    detail: errorMessage,
                                    life: 5000,
                                })
                            } else {
                                alert(this.tt('models.files.paste_error') + ': ' + errorMessage)
                            }
                        }

                        // Clear clipboard after cut operation
                        if (this.clipboardAction === 'cut') {
                            this.clipboard = []
                            this.clipboardAction = null
                        }

                        // Refresh file list
                        this.getFiles({}, true)
                        this.selectedFiles = []
                    })
                    .catch((err) => {
                        console.error('Error pasting files:', err)
                        const errorMsg = err.response?.data?.message || this.tt('models.files.paste_error')

                        if (this.$toast) {
                            this.$toast.add({
                                severity: 'error',
                                summary: this.tt('models.files.paste_error'),
                                detail: errorMsg,
                                life: 5000,
                            })
                        } else {
                            alert(errorMsg)
                        }
                    })
                    .finally(() => {
                        this.isPasting = false
                    })
            } catch (error) {
                console.error('Error in performPaste:', error)
                this.isPasting = false
                alert(this.tt('models.files.paste_error'))
            }
        },
        openContextMenu(event, file) {
            this.selectedContextFile = file
            const targetRect = event.currentTarget?.getBoundingClientRect?.()
            const menuEvent = {
                pageX: event.pageX > 0 ? event.pageX : targetRect ? targetRect.left + window.scrollX : 16,
                pageY: event.pageY > 0 ? event.pageY : targetRect ? targetRect.bottom + window.scrollY : 16,
                stopPropagation: () => event.stopPropagation?.(),
                preventDefault: () => event.preventDefault?.(),
            }
            const menuItems = []

            // Add "Open" option for directories
            if (file.type === 'dir') {
                menuItems.push({
                    label: this.tt('models.files.open'),
                    icon: 'pi pi-folder-open',
                    command: () => {
                        this.hideContextMenu()
                        this.navigateToFolder(file)
                    },
                })

                // Add "Paste here" option if clipboard has items
                if (this.clipboard.length > 0) {
                    menuItems.push({
                        label: this.tt('models.files.paste_here'),
                        icon: 'pi pi-arrow-down',
                        command: () => {
                            this.hideContextMenu()
                            this.pasteIntoFolder(file)
                        },
                    })
                }

                menuItems.push({ separator: true })
            }

            // Add common operations
            menuItems.push(
                {
                    label: this.tt('models.files.rename'),
                    icon: 'pi pi-pencil',
                    command: () => {
                        this.hideContextMenu()
                        this.showRename(file)
                    },
                },
                {
                    label: this.tt('models.files.copy'),
                    icon: 'pi pi-copy',
                    command: () => {
                        this.hideContextMenu()
                        this.copyToClipboard([file])
                    },
                },
                {
                    label: this.tt('models.files.cut'),
                    icon: 'pi pi-times',
                    command: () => {
                        this.hideContextMenu()
                        this.cutToClipboard([file])
                    },
                },
                {
                    separator: true,
                },
                {
                    label: this.tt('models.files.delete'),
                    icon: 'pi pi-trash',
                    command: () => {
                        this.hideContextMenu()
                        this.onRemove(file)
                    },
                }
            )

            this.contextMenuItems = menuItems

            // Use nextTick to ensure menu is rendered before showing
            this.$nextTick(() => {
                if (this.$refs.contextMenu) {
                    this.$refs.contextMenu.show(menuEvent)
                }
            })
        },
        hideContextMenu() {
            if (this.$refs.contextMenu) {
                this.$refs.contextMenu.hide()
            }
        },
        handleKeyboard(e) {
            if (!this.show) return

            if (e.key === 'Escape') {
                if (this.showActionsMenu) {
                    this.showActionsMenu = false
                    return
                }
                if (this.showFolderDrawer) {
                    this.showFolderDrawer = false
                    return
                }
                if (this.showFolderModal || this.showRenameModal) {
                    return
                }
                if (this.selectable) {
                    this.close()
                }
                return
            }

            const isTyping =
                ['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target?.tagName) ||
                e.target?.isContentEditable ||
                this.showFolderModal ||
                this.showRenameModal

            if (isTyping) return

            // Backspace to go up
            if (e.key === 'Backspace') {
                if (this.currentPath !== '/') {
                    e.preventDefault()
                    this.navigateUp()
                }
            }

            if ((e.ctrlKey || e.metaKey) && e.key === 'c') {
                if (this.selectedFiles.length > 0) {
                    e.preventDefault()
                    this.copyToClipboard()
                }
            }

            if ((e.ctrlKey || e.metaKey) && e.key === 'x') {
                if (this.selectedFiles.length > 0) {
                    e.preventDefault()
                    this.cutToClipboard()
                }
            }

            if ((e.ctrlKey || e.metaKey) && e.key === 'v') {
                if (this.clipboard.length > 0) {
                    e.preventDefault()
                    this.pasteFromClipboard()
                }
            }

            if (e.key === 'F2' && this.selectedFiles.length === 1) {
                e.preventDefault()
                this.showRename(this.selectedFiles[0])
            }

            if (e.key === 'Delete' && this.selectedFiles.length > 0) {
                e.preventDefault()
                this.selectedFiles.forEach((file) => this.onRemove(file))
            }

            // Enter to open folder
            if (e.key === 'Enter' && this.selectedFiles.length === 1 && this.selectedFiles[0].type === 'dir') {
                e.preventDefault()
                this.navigateToFolder(this.selectedFiles[0])
            }
        },
        onDragStart(event, file) {
            this.draggedItem = file
            event.dataTransfer.effectAllowed = 'move'
            event.dataTransfer.setData('text/plain', file.path)

            // Add a visual indicator
            if (event.target) {
                event.target.style.opacity = '0.5'
            }
        },
        onDragEnd(event) {
            // Reset opacity
            if (event.target) {
                event.target.style.opacity = ''
            }

            // Clear drag state after a short delay to allow drop to complete
            setTimeout(() => {
                this.draggedItem = null
                this.dropTarget = null
            }, 100)
        },
        onDragOver(event, file) {
            // Only allow dropping into folders, not files
            if (file.type !== 'dir') {
                return
            }

            // Don't allow dropping a folder into itself
            if (this.draggedItem && this.draggedItem.path === file.path) {
                return
            }

            // Prevent dropping a folder into its own child
            if (
                this.draggedItem &&
                this.draggedItem.type === 'dir' &&
                file.path.startsWith(this.draggedItem.path + '/')
            ) {
                return
            }

            event.preventDefault()
            this.dropTarget = file
        },
        onDragLeave(_event, file) {
            // Clear drop target when leaving
            if (this.dropTarget && this.dropTarget.path === file.path) {
                this.dropTarget = null
            }
        },
        onDrop(event, file) {
            event.stopPropagation()

            // Only allow dropping into folders
            if (file.type !== 'dir') {
                return
            }

            // Don't allow dropping a folder into itself
            if (this.draggedItem && this.draggedItem.path === file.path) {
                this.dropTarget = null
                return
            }

            // Prevent dropping a folder into its own child
            if (
                this.draggedItem &&
                this.draggedItem.type === 'dir' &&
                file.path.startsWith(this.draggedItem.path + '/')
            ) {
                this.dropTarget = null
                return
            }

            if (!this.draggedItem) {
                return
            }

            // Perform move operation
            const items = [
                {
                    path: this.draggedItem.path,
                    type: this.draggedItem.type,
                },
            ]

            this.$axios
                .post(this.route('admin.files.move'), {
                    items: items,
                    destinationPath: file.path,
                })
                .then((res) => {
                    const results = Array.isArray(res.data) ? res.data : []
                    const successCount = results.filter((r) => r && r.success).length

                    if (successCount > 0) {
                        if (this.$toast) {
                            this.$toast.add({
                                severity: 'success',
                                summary: this.tt('models.files.paste_success'),
                                detail: `${this.tt('models.files.items_pasted')}`,
                                life: 3000,
                            })
                        }

                        // Refresh file list
                        this.getFiles({}, true)
                        this.selectedFiles = []
                    } else {
                        const errorMessage = results[0]?.message || this.tt('models.files.paste_error')

                        if (this.$toast) {
                            this.$toast.add({
                                severity: 'error',
                                summary: this.tt('models.files.paste_error'),
                                detail: errorMessage,
                                life: 5000,
                            })
                        }
                    }
                })
                .catch((err) => {
                    console.error('Error moving file:', err)
                    const errorMsg = err.response?.data?.message || this.tt('models.files.paste_error')

                    if (this.$toast) {
                        this.$toast.add({
                            severity: 'error',
                            summary: this.tt('models.files.paste_error'),
                            detail: errorMsg,
                            life: 5000,
                        })
                    }
                })
                .finally(() => {
                    this.draggedItem = null
                    this.dropTarget = null
                })
        },
    },
}
</script>
