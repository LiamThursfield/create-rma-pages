<script setup>
import AppLayout from "../../Layouts/AppLayout.vue";
import Card from "../../Components/Card.vue";
import {Link} from "@inertiajs/vue3";

defineProps({
    created_at: String,
    created_by: String,
    items: Array
});
</script>

<template>
    <AppLayout>
        <Card class="py-12">
            <div
                class="flex flex-col mb-8 md:flex-row md:items-end md:justify-between"
            >
                <h1 class="font-bold mb-2 text-lg md:mb-0">
                    RMA
                </h1>
                <h2 class="font-semibold text-md text-gray-400 md:text-right">
                    {{ created_by }}
                    <br>
                    {{ created_at }}
                </h2>
            </div>

            <p v-if="!items || !items.length">
                There are no items on this RMA
            </p>

            <div
                v-else
                class="border overflow-x-auto rounded"
            >
                <table class="table-auto w-full">
                    <thead class="text-left">
                    <tr class="border-b-2">
                        <th class="px-6 py-4">Identifier</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Value</th>
                        <th class="px-6 py-4">Reason</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr
                        v-for="(item, row) in items"
                        :key="item.identifier"
                        class="border-b transition duration-300 ease-in-out hover:bg-neutral-200"
                        :class="{
                            'bg-neutral-100': row % 2 === 1
                        }"
                    >
                        <td class="px-6 py-4">{{ item.identifier }}</td>
                        <td class="px-6 py-4">{{ item.type }}</td>
                        <td class="px-6 py-4">{{ item.value }}</td>
                        <td class="px-6 py-4">{{ item.reason }}</td>
                    </tr>
                    </tbody>

                    <tfoot>
                    <tr class="border-t-2">
                        <td
                            class="font-semibold px-6 py-2"
                            colspan="4"
                        >
                            Total: {{ items.length}}
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </Card>
    </AppLayout>
</template>
