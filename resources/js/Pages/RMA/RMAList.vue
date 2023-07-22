<script setup>
import AppLayout from "../../Layouts/AppLayout.vue";
import Card from "../../Components/Card.vue";
import { Link } from '@inertiajs/vue3';
import PrimaryButtonLink from "../../Components/PrimaryButtonLink.vue";

defineProps({
    data: Array
});

const getRmaItemString = (rma) => {
    if (!rma.item_identifiers) {
        return '';
    }

    return rma.item_identifiers.map(item => {
        return item.type;
    }).join(', ');
}
</script>

<template>
    <AppLayout>
        <Card class="py-12">
            <div class="flex flex-row items-center mb-8">
                <h1 class="font-bold mr-auto text-lg">
                    RMAs
                </h1>
                <PrimaryButtonLink
                    :href="route('rma.create')"
                >
                    Create RMA
                </PrimaryButtonLink>
            </div>

            <p v-if="!data || !data.length">
                There are currently no RMAs
            </p>

            <div
                v-else
                class="border overflow-x-auto rounded"
            >
                <table class="table-auto w-full">
                    <thead class="text-left">
                        <tr class="border-b-2">
                            <th class="px-6 py-4">Created By</th>
                            <th class="px-6 py-4">Created At</th>
                            <th class="px-6 py-4">Items</th>
                            <th class="px-6 py-4"></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="(rma, row) in data"
                            :key="rma.id"
                            class="border-b transition duration-300 ease-in-out hover:bg-neutral-200"
                            :class="{
                                'bg-neutral-100': row % 2 === 1
                            }"
                        >
                            <td class="px-6 py-4">{{ rma.created_by }}</td>
                            <td class="px-6 py-4">{{ rma.created_at }}</td>
                            <td class="px-6 py-4">{{ getRmaItemString(rma) }}</td>
                            <td class="px-6 py-4 text-right">
                                <Link :href="route('rma.show', rma.id)">
                                    View
                                </Link>
                            </td>
                        </tr>
                    </tbody>

                    <tfoot>
                        <tr class="border-t-2">
                            <td
                                class="font-semibold px-6 py-2"
                                colspan="4"
                            >
                                Total: {{ data.length}}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </Card>
    </AppLayout>
</template>


