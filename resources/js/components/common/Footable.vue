<template>
    <div class="rounded bg-surface text-on-surface box-shadow content-wrapper py-3">
        <div class="custom-scrollbar overflow-auto border border-border rounded">
            <table class="w-full">
                <thead>
                    <tr class="bg-grey-300">
                        <th v-for="headerKey in json.headers.data" :key="headerKey"
                            :class="json.headers.styles[headerKey]?.join(' ')" class="px-3 py-3 ">
                            <p class="font-medium ">{{ formatHeader(headerKey)
                            }}</p>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <!-- Loading state -->
                    <tr v-if="loading" class="border-t border-border rounded">
                        <td :colspan="json.headers.data.length" class=" px-3 py-4">
                            <div class="flex justify-center">
                                <BarScaleLoading />
                            </div>
                        </td>
                    </tr>

                    <!-- Empty state -->
                    <tr v-else-if="json.body.data.length === 0" class="border-t border-border rounded">
                        <td :colspan="json.headers.data.length" class="text-center px-3 py-4">
                            <p class="text-center capitalize">no data found</p>
                        </td>
                    </tr>

                    <!-- Data rows -->
                    <tr v-else v-for="(row, index) in json.body.data" :key="index"
                        class="border-t border-border hover:bg-hover even:bg-hover">
                        <td v-for="headerKey in json.headers.data" :key="headerKey"
                            :class="json.body.styles[headerKey]?.join(' ')"
                            class="max-w-[200px] overflow-hidden text-center px-3 py-4 truncate">
                            <template v-if="headerKey === 'no' && pagination">
                                {{ pagination?.startIndex + index }}
                            </template>

                            <template v-else-if="headerKey === 'no' && !pagination">
                                {{ index + 1 }}
                            </template>

                            <template v-else-if="headerKey === 'actions'">
                                <template v-if="Array.isArray(row[headerKey])">
                                    <button v-for="(action, i) in row[headerKey]" :key="i"
                                        :class="['mx-1 px-1 py-1 rounded cursor-pointer', action.classes]"
                                        @click="action.action">
                                        <component v-if="action?.icon" :is="action.icon" class="text-sm w-4.5 h-4.5" />
                                        <p v-if="action?.name" class="text-sm ">{{ action?.name }}</p>
                                    </button>
                                </template>
                            </template>

                            <template v-else>
                                <div v-html="row[headerKey] ?? 'N/A'"></div>
                            </template>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div v-if="pagination" class="flex justify-between items-center px-3 py-4">
            <p class="page-info">
                Page {{ pagination?.currentPage }} of {{ pagination.totalPages }}
            </p>
            <div>
                <button v-for="page in paginationButtons" :key="page"
                    :class="['mx-1 px-3 py-1 rounded disabled:cursor-not-allowed', page === props.pagination.currentPage ? 'bg-primary text-on-primary' : 'bg-secondary text-on-secondary']"
                    @click="page !== '...' && changePage(page)" :disabled="page === '...'">
                    {{ page }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    json: {
        type: Object,
        required: true
    },
    pagination: {
        type: Object,
    },
    loading: {
        type: Boolean,
        default: false
    },
});

const emits = defineEmits(['pageChanged']);

const paginationButtons = computed(() => {
    const pages = []
    const total = props.pagination.totalPages
    const current = props.pagination.currentPage

    if (total <= 5) {
        // Show all pages if total pages are 5 or less
        for (let i = 1; i <= total; i++) {
            pages.push(i)
        }
    } else {
        // Always show the first page
        pages.push(1)

        if (current > total - 4) {
            // Add ellipsis if current page is beyond the third page
            pages.push('...')
        }

        // Add pages around the current page
        for (let i = Math.max(2, current - 1); i <= Math.min(total - 1, current + 1); i++) {
            pages.push(i)
        }

        if (current < total - 3) {
            // Add ellipsis if current page is far from the last page
            pages.push('...')
        }

        // Always show the last page
        pages.push(total)
    }

    return pages
})

const changePage = (page) => {
    if (page !== '...') {

        props.pagination.currentPage = page;
        emits('pageChanged', page);

    }
}

function formatHeader(name) {
    return name
        .replace(/_/g, ' ')
        .replace(/\b\w/g, char => char.toUpperCase());
}

</script>
