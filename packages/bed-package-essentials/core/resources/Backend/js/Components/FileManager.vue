<template>
    <div
        class="fixed top-0 bottom-0 right-0 z-50 overflow-hidden bg-white"
        v-show="show"
        :class="embed ? 'left-0 overflow-auto' : 'left-from-sidebar'"
    >
        <input
            type="file"
            class="hidden"
            accept="image/png, image/gif, image/jpeg, image/svg+xml, application/pdf ,image/webp"
            multiple="true"
            ref="file"
            @change="fileChange"
        />
        <div class="topbar" v-if="!embed">
            <h1 class="flex items-center font-semibold text-gray-700">
                <div
                    class="p-4 -ml-4 cursor-pointer hover:text-gray-900"
                    @click="$emit('update:show', false)"
                    v-if="selectable"
                >
                    <ph-caret-left />
                </div>
                {{ tt('models.files.file_manager') }}
            </h1>
            <div class="flex ml-auto space-x-3">
                <input
                    type="text"
                    :placeholder="tt('models.files.input_file')"
                    class="flex-inline w-[400px] py-[0.5rem] px-[1rem] border border-gray-300 focus:border-solid focus:outline-none focus:ring-0 rounded hover:border-gray-400 focus:border-gray-500"
                    @input="onChange"
                />
                <Button @click.prevent="showFolderModal = true" class="space-x-2 btn-outline-primary">
                    <ph-plus-circle-light />
                    <span> {{ tt('models.files.add_folder') }} </span>
                </Button>
                <Button @click.prevent="browse" class="space-x-2 btn-primary">
                    <ph:upload-simple />
                    <span> {{ tt('models.files.select_file') }} </span>
                </Button>

                <Button
                    @click="renameCurrentFolder"
                    class="space-x-2 btn-outline-primary"
                    :disabled="currentPath === '/'"
                >
                    <ph:folder-notch-open />
                    <span> {{ tt('models.files.rename_current_folder') }} </span>
                </Button>
                <Button
                    @click="showRename"
                    class="space-x-2 btn-outline-primary"
                    :disabled="selectedFiles.length !== 1"
                >
                    <ph:pencil-simple />
                    <span> {{ tt('models.files.rename') }} </span>
                </Button>
                <Button
                    @click="copyToClipboard"
                    class="space-x-2 btn-outline-primary"
                    :disabled="selectedFiles.length === 0"
                >
                    <ph:copy />
                    <span> {{ tt('models.files.copy') }} </span>
                </Button>
                <Button
                    @click="cutToClipboard"
                    class="space-x-2 btn-outline-primary"
                    :disabled="selectedFiles.length === 0"
                >
                    <ph:scissors />
                    <span> {{ tt('models.files.cut') }} </span>
                </Button>
                <Button
                    @click="pasteFromClipboard"
                    class="space-x-2 btn-outline-primary"
                    :disabled="clipboard.length === 0 || isPasting"
                >
                    <ph:clipboard />
                    <span>
                        {{ isPasting ? tt('models.files.pasting') : tt('models.files.paste') }}
                        {{ clipboard.length > 0 ? `(${clipboard.length})` : '' }}
                    </span>
                </Button>
            </div>
        </div>
        <div class="flex items-stretch flex-1 h-full overflow-hidden" @dragover.prevent="isDragging = true">
            <div
                class="fixed inset-0 overflow-hidden border-4 border-dashed before:absolute before:bg-green-300 before:bg-opacity-25 before:inset-0 before:z-10 left-from-sidebar"
                :class="
                    isDragging
                        ? 'z-10 border-green-300 before:visible visible'
                        : 'z-0 border-transparent before:invisible invisible'
                "
                @dragleave.prevent="isDragging = false"
                @drop.prevent=";(isDragging = false), (dragCounter = 0), drop($event)"
            ></div>

            <!-- Details sidebar -->
            <aside class="hidden p-8 pb-16 overflow-y-auto bg-white border-l border-r border-gray-200 w-80 lg:block">
                <template v-if="embed">
                    <Button @click.prevent="browse" class="w-full space-x-2 btn-primary">
                        <ph:upload-simple />
                        <span> {{ tt('models.files.select_file') }} </span>
                    </Button>
                    <hr class="my-2" />
                </template>
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
                class="overflow-y-auto grow group-image-admin"
                :class="canDeleteFolder ? 'flex items-center flex-col justify-center' : 'flex-1'"
            >
                <!-- Breadcrumb Navigation -->
                <div v-if="!canDeleteFolder" class="px-4 pt-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <nav class="flex items-center space-x-2 text-sm">
                        <a
                            @click="navigateToPath('/')"
                            class="text-blue-600 hover:text-blue-800 cursor-pointer flex items-center"
                        >
                            <ph:house class="w-4 h-4" />
                        </a>
                        <template v-for="(part, index) in pathParts" :key="index">
                            <span class="text-gray-400">/</span>
                            <a
                                @click="navigateToPath(getPathUpToIndex(index))"
                                class="text-blue-600 hover:text-blue-800 cursor-pointer"
                                :class="{ 'font-semibold text-gray-900': index === pathParts.length - 1 }"
                            >
                                {{ part }}
                            </a>
                        </template>
                    </nav>
                </div>

                <div v-if="canDeleteFolder">
                    <h1 v-if="canDeleteFolder" class="text-xl">
                        {{ tt('models.files.drop') }}
                        <a @click="browse" class="link">{{ tt('models.files.click_here').toLowerCase() }}</a>
                        {{ tt('models.files.select_files').toLowerCase() }}
                    </h1>
                </div>
                <div v-if="canDeleteFolder" class="mt-6">
                    <Button @click="deleteFolder" class="space-x-2 btn-outline-primary">
                        <carbon:subtract-alt />
                        <span> {{ tt('models.files.delete_folder') }} </span>
                    </Button>
                </div>
                <div
                    v-if="searchFiles && searchFiles.length"
                    class="px-4 pt-8 pb-16 mx-auto space-y-4 max-w-7xl sm:px-6 lg:px-8"
                >
                    <ul role="list" class="grid grid-cols-3 gap-4 lg:grid-cols-4 2xl:grid-cols-6" v-if="data">
                        <li
                            class="relative"
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
            class="absolute bottom-0 left-0 right-0 flex items-center justify-center w-full h-16 space-x-2 bg-white border-t"
        >
            <Button @click="selectedFiles = []"> {{ tt('models.files.unchecked') }} </Button>
            <Button class="btn-primary" @click="submitFileSelect()">
                {{ tt('models.files.select') }} ({{ selectedFiles.length }})
            </Button>
        </div>
        <Dialog
            header="Folder"
            v-model:visible="showFolderModal"
            :breakpoints="{
                '960px': '75vw',
                '640px': '90vw',
            }"
            :style="{ width: '50vw' }"
            :draggable="true"
        >
            <Field
                v-model="folderForm.name"
                :field="{
                    rules: 'required',
                    name: 'name',
                }"
            />
            <template #footer>
                <Button variant="white" @click="showFolderModal = false" :label="tt('models.files.cancel')" />
                <Button
                    type="button"
                    class="ml-2"
                    @click="createFolder(folderForm.name), (showFolderModal = false)"
                    :label="tt('models.files.save')"
                />
            </template>
        </Dialog>
        <Dialog
            :header="tt('models.files.rename')"
            v-model:visible="showRenameModal"
            :breakpoints="{
                '960px': '75vw',
                '640px': '90vw',
            }"
            :style="{ width: '50vw' }"
            :draggable="true"
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
                <Button type="button" class="ml-2" @click="renameFile" :label="tt('models.files.save')" />
            </template>
        </Dialog>
        <ContextMenu ref="contextMenu" :model="contextMenuItems" />
    </div>
</template>
<script>
import Pagination from '@Core/Components/Pagination.vue'
import Thumbnail from '@Core/Components/Thumbnail.vue'
import ContextMenu from 'primevue/contextmenu'
import { onMounted, onUnmounted } from 'vue'

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

    setup(props, { emit }) {
        const close = () => {
            emit('update:show', false)
        }

        const closeOnEscape = (e) => {
            if (e.key === 'Escape' && props.show) {
                close()
            }
        }

        onMounted(() => document.addEventListener('keydown', closeOnEscape))
        onUnmounted(() => {
            document.removeEventListener('keydown', closeOnEscape)
        })

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
            showFolderModal: null,
            folderForm: {
                name: null,
            },
            search: null,
            embed: this.$page.props.route.query.embed,
            selectMultiple: this.$page.props.route.query['select-multiple'] == 'true',
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
        }
    },

    created() {
        this.getFiles()
    },

    mounted() {
        this.$bus.on('treeSelectedItemFileManager', (item) => {
            this.selectedItem(item)
        })

        let images = document.querySelector('.group-image-admin')

        images.addEventListener('scroll', () => {
            if (this.timerScoll) {
                clearTimeout(this.timerScoll)
                this.timerScoll = null
            }

            if (this.fetchData) {
                this.timerScoll = setTimeout(this.scrollImage, 300)
            }
        })

        document.addEventListener('keydown', this.handleKeyboard)
    },

    unmounted() {
        let images = document.querySelector('.group-image-admin')

        if (images) {
            images.removeEventListener('scroll', this.scrollImage)
        }

        document.removeEventListener('keydown', this.handleKeyboard)
    },

    beforeDestroy() {
        this.$bus.off('treeSelectedItemFileManager')
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
        scrollImage() {
            this.page = this.page + 1
            this.getFiles({ page: this.page })
        },
        selectedItem(item) {
            this.currentPath = item.path
            this.data.files = []
            this.data.directories = []
            this.search = null
            this.selectedFiles = []
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
                    // For folders in embed mode, allow selection
                    if (file.type === 'dir') {
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
            this.selectedFiles = []
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
            if (this.timer) {
                clearTimeout(this.timer)
                this.timer = null
            }

            this.timer = setTimeout(() => {
                this.search = event.target.value
                this.page = 1
                this.fetchData = true
            }, 500)
        },
        createFolder(name) {
            this.$axios
                .post(this.route('admin.files.folders.create'), {
                    name: name,
                    path: this.currentPath,
                })
                .then((res) => {
                    this.getFiles({}, true)
                    this.folderForm.name = null
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
                    .then((res) => {
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
            if (!file && this.selectedFiles.length === 1) {
                file = this.selectedFiles[0]
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
            const isRenamingCurrentFolder = this.renameForm.path === this.currentPath

            this.$axios
                .post(this.route('admin.files.rename'), {
                    path: this.renameForm.path,
                    newName: this.renameForm.newName,
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
                .catch((err) => {
                    alert(this.tt('models.files.rename_error'))
                })
        },
        copyToClipboard(files = null) {
            const filesToCopy = files || this.selectedFiles

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
            const filesToCut = files || this.selectedFiles

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
                    this.$refs.contextMenu.show(event)
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

            // Backspace to go up (only if not typing in input)
            if (e.key === 'Backspace' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
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
<style lang="scss" scoped>
.left-from-sidebar {
    left: var(--sidebar-width);
}
.topbar {
    @apply absolute flex items-center flex-shrink-0 w-full px-4 bg-white border-b sm:px-10 md:px-12;
    height: var(--topbar-height);
}

.topbar + div {
    @apply fixed right-0;
    top: var(--topbar-height);
    height: calc(100% - var(--topbar-height));
    left: var(--sidebar-width);
}
</style>
