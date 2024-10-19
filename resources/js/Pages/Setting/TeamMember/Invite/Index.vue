<script setup>
import { ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import dayjs from "@/dayjs";
import Button from "@/Components/Button.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";

const { invites } = defineProps({
    invites: {
        type: Array,
        required: true,
    },
});

const deleteForm = useForm({});
const beingDeleted = ref(null);

const formatDate = (date) => {
    return dayjs(date).format("MMMM D, YYYY h:mm A");
};
const confirmDeletion = (invite) => {
    beingDeleted.value = invite;
};

const deleteInvite = () => {
    deleteForm.delete(route("setting.invites.destroy", beingDeleted.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (beingDeleted.value = null),
    });
};
</script>

<template>
    <div v-if="invites.length >= 1" class="mt-8">
        <div class="sm:flex sm:items-center mb-4">
            <div class="sm:flex-auto">
                <h1 class="page-title">Pending Invites</h1>
            </div>
        </div>

        <div class="flex flex-col">
            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div
                    class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8"
                >
                    <div class="table-wrapper">
                        <table class="table">
                            <thead class="table-thead">
                                <tr class="table-tr">
                                    <th scope="col" class="table-th">Name</th>
                                    <th scope="col" class="table-th">E-mail</th>
                                    <th scope="col" class="table-th">Role</th>

                                    <th scope="col" class="table-th">
                                        Invited At
                                    </th>
                                    <th scope="col" class="table-th">
                                        <span class="sr-only">Edit</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="table-tbody">
                                <tr v-for="invite in invites" :key="invite.id">
                                    <td class="table-td">
                                        {{ invite.name }}
                                    </td>
                                    <td class="table-td">
                                        {{ invite.email }}
                                    </td>
                                    <td class="table-td">
                                        {{ invite.role }}
                                    </td>
                                    <td class="table-td">
                                        {{ formatDate(invite.created_at) }}
                                    </td>

                                    <td class="table-td">
                                        <a
                                            v-if="
                                                $page.props.auth.user.role !=
                                                'user'
                                            "
                                            href="#"
                                            @click="confirmDeletion(invite.id)"
                                            class="text-red-600 hover:underline font-medium"
                                            >Cancel</a
                                        >
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <ConfirmationModal
        :show="beingDeleted != null"
        @close="beingDeleted = null"
        maxWidth="sm"
    >
        <template #title>Delete Invite</template>

        <template #content>
            Are you sure you would like to cancel this invite?
        </template>

        <template #footer>
            <Button @click="beingDeleted = null" class="btn-secondary">
                Cancel
            </Button>

            <Button
                class="ml-3"
                :class="{
                    'opacity-25': deleteForm.processing,
                    'btn-danger': true,
                }"
                :disabled="deleteForm.processing"
                @click="deleteInvite"
            >
                Delete
            </Button>
        </template>
    </ConfirmationModal>
</template>
