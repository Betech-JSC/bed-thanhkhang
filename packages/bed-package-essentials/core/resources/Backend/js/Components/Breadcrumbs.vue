<template>
    <nav class="flex min-w-0" aria-label="Breadcrumb" v-if="$page.props.breadcrumbs">
        <ol
            role="list"
            class="flex flex-wrap sm:flex-nowrap items-center sm:items-stretch w-full min-w-0 px-3 sm:px-6 gap-x-2 sm:gap-x-4 gap-y-1 bg-white border rounded-t-md overflow-hidden"
        >
            <li class="flex min-w-0">
                <div class="flex items-center">
                    <Link href="/" class="text-gray-400 hover:text-gray-500">
                        <heroicons-outline:home
                            class="flex-shrink-0 w-5 h-5"
                            aria-hidden="true"
                        />
                        <span class="sr-only">Home</span>
                    </Link>
                </div>
            </li>
            <li
                v-for="breadcrumb in $page.props.breadcrumbs"
                :key="breadcrumb.name"
                class="flex min-w-0"
            >
                <div class="flex items-center min-w-0">
                    <svg
                        class="flex-shrink-0 w-6 h-full text-gray-200"
                        viewBox="0 0 24 44"
                        preserveAspectRatio="none"
                        fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true"
                    >
                        <path d="M.293 0l22 22-22 22h1.414l22-22-22-22H.293z" />
                    </svg>
                    <Link
                        :href="breadcrumb.url"
                        class="ml-2 sm:ml-4 text-sm font-medium text-gray-500 capitalize hover:text-gray-700 truncate"
                        :aria-current="breadcrumb.current ? 'page' : undefined"
                        >{{ tt(breadcrumb.name) }}
                    </Link>
                </div>
            </li>
        </ol>
    </nav>
</template>
