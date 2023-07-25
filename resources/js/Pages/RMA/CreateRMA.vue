<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto py-4 sm:px-6 lg:px-8">
            <div class="text-right">
                <PrimaryButton
                    :disabled="!form.items || !form.items.length || form.processing"
                    type="button"
                    @click="createRma"
                >
                    Create
                </PrimaryButton>
            </div>
        </div>

        <Card>
            <div class="flex flex-col md:flex-row md:justify-between">
                <h1 class="font-bold mb-4 text-lg md:mb-0">
                    Create RMA
                </h1>
                <div>
                    <SecondaryButton
                        type="button"
                        @click="addItem"
                    >
                        Add RMA Item
                    </SecondaryButton>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table
                    v-if="form.items && form.items.length"
                    class="mt-4 table-auto w-full"
                >
                    <thead class="text-left">
                        <tr class="border-b-2">
                            <th class="px-6 py-4">Type</th>
                            <th class="px-6 py-4">Value</th>
                            <th class="px-6 py-4">Identifier</th>
                            <th class="px-6 py-4">Reason</th>
                            <th class="py-4"></th>
                            <th class="py-4"></th>
                        </tr>
                    </thead>

                    <tbody>
                        <template
                            v-for="(item, index) in form.items"
                            :key="index"
                        >
                            <tr
                                class="border-b transition duration-300 ease-in-out hover:bg-neutral-200"
                                :class="{
                                    'bg-neutral-100': index % 2 === 1
                                }"
                            >
                                <td class="px-6 py-4">{{ getRmaTypeText(item.type) }}</td>
                                <td class="px-6 py-4">{{ getRmaValueText(item.type, item.value) }}</td>
                                <td class="px-6 py-4">{{ item.identifier }}</td>
                                <td class="px-6 py-4">{{ item.reason }}</td>
                                <td class="py-4 text-right">
                                    <SecondaryButton
                                        class="mr-2"
                                        type="button"
                                        @click="deleteRMAItem(index)"
                                    >
                                        Delete
                                    </SecondaryButton>
                                </td>
                                <td class="py-4 text-right">
                                    <SecondaryButton
                                        type="button"
                                        @click="editItem(index, item)"
                                    >
                                        Edit
                                    </SecondaryButton>
                                </td>
                            </tr>
                            <tr
                                v-if="form.error && form.error.items && form.error.items[index]"
                                class="bg-red-100"
                            >
                                <td
                                    class="px-6 py-4"
                                    colspan="6"
                                >
                                    <div
                                        v-for="(message, field) in form.error.items[index]"
                                        :key="`${index}-${field}`"
                                    >
                                        {{  message }}
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </Card>

        <DialogModal
            :show="showDeleteModal"
            @close="showDeleteModal = false"
        >
            <template v-slot:title>
                Delete RMA Item
            </template>

            <template v-slot:content>
                Are you sure you want to delete this RMA item?
            </template>

            <template v-slot:footer>
                <SecondaryButton
                    class="mr-2"
                    @click="cancelRmaItemDelete"
                >
                    Cancel
                </SecondaryButton>

                <DangerButton
                    @click="confirmRmaItemDelete"
                >
                    Delete
                </DangerButton>
            </template>
        </DialogModal>

        <DialogModal
            :show="showAddModal || showEditModal"
            @close="closeAddEditModal"
        >
            <template v-slot:title>
                RMA Item
            </template>

            <template v-slot:content>
                <form
                    id="rma-item-create-edit-form"
                    @submit.prevent="submitAddEditModal"
                >
                    <label for="rma-item-type">
                        Type:
                    </label>
                    <select
                        id="rma-item-type"
                        class="mt-2 mb-4 w-full"
                        name="rma-item-type"
                        required
                        v-model="rmaItemToAddEdit.type"
                        @input="rmaItemToAddEdit.value = ''"
                    >
                        <option value="" disabled>Select a type</option>
                        <option
                            v-for="item in types"
                            :key="item.value"
                            :value="item.value"
                        >
                            {{ item.text }}
                        </option>
                    </select>

                    <label
                        for="rma-item-value"
                    >
                        Value:
                    </label>

                    <select
                        id="rma-item-value"
                        class="mt-2 mb-4 w-full"
                        :disabled="valueOptions.length === 0"
                        name="rma-item-value"
                        required
                        v-model="rmaItemToAddEdit.value"
                    >
                        <option value="" disabled>Select a value</option>
                        <option
                            v-for="item in valueOptions"
                            :key="item.value"
                            :value="item.value"
                        >
                            {{ item.text }}
                        </option>
                    </select>

                    <label for="rma-item-identifier">
                        Identifier:
                    </label>
                    <input
                        id="rma-item-identifier"
                        class="mt-2 mb-4 w-full"
                        name="rma-item-identifier"
                        placeholder="Enter the identifier"
                        required
                        type="text"
                        v-model="rmaItemToAddEdit.identifier"
                    />

                    <label for="rma-item-reason">
                        Reason:
                    </label>
                    <textarea
                        id="rma-item-reason"
                        class="mt-2 mb-4 w-full"
                        name="rma-item-reason"
                        placeholder="Enter the reason"
                        rows="3"
                        required
                        v-model="rmaItemToAddEdit.reason"
                    />
                </form>
            </template>

            <template v-slot:footer>
                <SecondaryButton
                    class="mr-2"
                    @click="closeAddEditModal"
                >
                    Cancel
                </SecondaryButton>

                <button
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                    form="rma-item-create-edit-form"
                >
                    <template v-if="showAddModal">
                        Add
                    </template>
                    <template v-else>
                        Edit
                    </template>
                    Item
                </button>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script setup>
import {computed, nextTick, reactive, ref} from 'vue';
import { router, useForm } from '@inertiajs/vue3';

import AppLayout from "../../Layouts/AppLayout.vue";
import Card from "../../Components/Card.vue";
import DialogModal from "../../Components/DialogModal.vue";
import DangerButton from "../../Components/DangerButton.vue";
import SecondaryButton from "../../Components/SecondaryButton.vue";
import PrimaryButton from "../../Components/PrimaryButton.vue";


const props = defineProps({
    types: Array
});


const form = useForm({
    'items': [],
});

const rmaItemToAddEdit = reactive({
    identifier: "",
    reason: "",
    type: "",
    value: "",
});
const rmaItemIndexToEdit = ref(null);
const rmaItemIndexToDelete = ref(null);

const showAddModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);


const valueOptions = computed(() => {
    if (!rmaItemToAddEdit.type) {
        return [];
    }

    return props.types.find(type => type.value === rmaItemToAddEdit.type).items;
})


/**********************
 * RMA Item Add/Edit
 **********************/
const addItem = () => {
    showAddModal.value = true;
    clearFormErrors();
}

const editItem = (index, item) => {
    rmaItemToAddEdit.identifier = item.identifier;
    rmaItemToAddEdit.reason = item.reason;
    rmaItemToAddEdit.type = item.type;
    rmaItemToAddEdit.value = item.value;

    clearFormErrors();

    rmaItemIndexToEdit.value = index;
    showEditModal.value = true;
}
const closeAddEditModal = () => {
    showAddModal.value = false;
    showEditModal.value = false;

    resetRmaItemToAddEdit();
    rmaItemIndexToEdit.value = null;
};

const submitAddEditModal = () => {
    if (showAddModal.value) {
        submitAddModal();
    } else {
        submitEditModal();
    }
}

const submitAddModal = () => {
    form.items.push({...rmaItemToAddEdit});

    showAddModal.value = false;
    resetRmaItemToAddEdit();
}

const submitEditModal = () => {
    form.items[rmaItemIndexToEdit.value] = {...rmaItemToAddEdit};

    showEditModal.value = false;
    rmaItemIndexToEdit.value = null;
    resetRmaItemToAddEdit()
}

const resetRmaItemToAddEdit = () => {
    rmaItemToAddEdit.identifier = "";
    rmaItemToAddEdit.reason = "";
    rmaItemToAddEdit.type = "";
    rmaItemToAddEdit.value = "";
};


/**********************
 * RMA Item Deletion
**********************/
const deleteRMAItem = (index) => {
    rmaItemIndexToDelete.value = index;
    showDeleteModal.value = true;
};

const cancelRmaItemDelete = () => {
    rmaItemIndexToDelete.value = null;
    showDeleteModal.value = false;
};

const confirmRmaItemDelete = () => {
    form.items.splice(rmaItemIndexToDelete.value, 1);
    clearFormErrors();

    rmaItemIndexToDelete.value = null;
    showDeleteModal.value = false;
};


/**********************
 * RMA Create
 **********************/
const createRma = () => {
    form.processing = true;
    form.error = null;

    router.post(
        route('rma.store'),
        {
            items: form.items,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                console.log('success');
                form.processing = false;
                form.reset();
            },
            onError: errors => {
                form.processing = false;
                form.error = parseFormErrors(errors);
            }
        }
    );
};

const parseFormErrors = (errors) => {
    let parsedErrors = {
        items: {},
    };

    let keys = Object.keys(errors);

    keys.forEach(key => {
        if (key.includes('items.')) {
            let itemIndex = key.split('.')[1];
            let itemField = key.split('.')[2];

            if (!parsedErrors.items.hasOwnProperty(itemIndex)) {
                parsedErrors.items[itemIndex] = {};
            };

            parsedErrors.items[itemIndex][itemField] = errors[key].replace('items.' + itemIndex + '.', '');
        }
    });

    return parsedErrors;
};

const clearFormErrors = () => {
    form.error = null;
};


/**********************
 * Helpers
 **********************/
const getRmaTypeText = (type_value) => {
    let type = props.types.find(t => t.value === type_value);

    if (type && type.text) {
        return type.text;
    }

    return type_value;
}

const getRmaValueText = (type_value, value_value) => {
    let type = props.types.find(t => t.value === type_value);

    if (type && type.items) {
        let value = type.items.find(i => i.value === value_value);

        if (value && value.text) {
            return value.text;
        }
    }

    return value_value;
}
</script>
