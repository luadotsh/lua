<script setup>
import { ref, onMounted } from "vue";
import { useForm, usePage, Head } from "@inertiajs/vue3";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import date from "@/date";
import debounce from "@/debounce";

import AppLayout from "@/Layouts/Master.vue";
import Input from "@/Components/Input.vue";
import Button from "@/Components/Button.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import InviteIndex from "./Invite/Index.vue";

const user = usePage().props.auth.user;

import { PhMagnifyingGlass, PhDotsThreeOutline } from "@phosphor-icons/vue";

defineProps({
    users: Array,
    invites: Array,
});

const searchForm = useForm({
    q: "",
});

const updateUserForm = useForm({
    role: null,
});

const removeFromTeamForm = useForm({});
const beingRemoveFromTeam = ref(null);

const leaveTeamForm = useForm({});
const beingLeaving = ref(null);

const confirmRemoveFromTeam = (id) => {
    beingRemoveFromTeam.value = id;
};

const removeFromTeam = () => {
    removeFromTeamForm.delete(
        route("team-members.destroy", beingRemoveFromTeam.value),
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => (beingRemoveFromTeam.value = null),
        }
    );
};

const confirmLeaveTeam = () => {
    beingLeaving.value = user;
};

const leaveTeam = () => {
    leaveTeamForm.delete(route("setting.team-members.leave"), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (beingLeaving.value = null),
    });
};

const changeUserRole = (user, role) => {
    updateUserForm.role = role;

    updateUserForm.put(route("team-members.role", user.id), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => updateUserForm.reset(),
    });
};

const searchDebounce = debounce(function () {
    searchForm.get(route("setting.team-members.index"), {
        preserveScroll: true,
        preserveState: true,
    });
}, 300);

onMounted(() => {
    if (route().params.q) {
        searchForm.q = route().params.q;
    }
});
</script>

<template>
    <Head title="Team Members" />
    <AppLayout>
        <template #header>
            <div class="sm:flex sm:items-center flex-1">
                <div class="sm:flex-auto">
                    <h1 class="page-title">Users</h1>
                </div>
                <div class="mt-4 sm:mt-0 sm:ml-16 flex space-x-2 sm:flex-none">
                    <div class="relative flex-1">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                        >
                            <PhMagnifyingGlass class="h-5 w-5 text-zinc-400" />
                        </div>
                        <Input
                            class="w-full !pl-10"
                            id="search-field"
                            autocomplete="off"
                            type="text"
                            placeholder="Search..."
                            v-model="searchForm.q"
                            @keyup="searchDebounce"
                        />
                    </div>

                    <Button
                        :href="route('setting.invites.create')"
                        class="btn-primary"
                    >
                        Invite
                    </Button>
                </div>
            </div>
        </template>

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
                                        Sign up
                                    </th>
                                    <th scope="col" class="table-th">
                                        <span class="sr-only">Edit</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="table-tbody">
                                <tr v-for="member in users" :key="member.id">
                                    <td class="table-td">
                                        {{ member.name }}
                                    </td>
                                    <td class="table-td">
                                        {{ member.email }}
                                    </td>
                                    <td class="table-td">
                                        {{ member.membership.role }}
                                    </td>
                                    <td class="table-td">
                                        {{
                                            date.formatDateTime(
                                                member.created_at
                                            )
                                        }}
                                    </td>

                                    <td class="table-td">
                                        <Menu
                                            as="div"
                                            class="relative inline-block text-left"
                                            v-if="
                                                member.id === user.id ||
                                                user.current_workspace.role !==
                                                    'USER'
                                            "
                                        >
                                            <MenuButton class="">
                                                <PhDotsThreeOutline
                                                    class="h-4 w-4 stroke-2 text-black"
                                                />
                                            </MenuButton>

                                            <transition
                                                enter-active-class="transition ease-out duration-100"
                                                enter-from-class="transform opacity-0 scale-95"
                                                enter-to-class="transform opacity-100 scale-100"
                                                leave-active-class="transition ease-in duration-75"
                                                leave-from-class="transform opacity-100 scale-100"
                                                leave-to-class="transform opacity-0 scale-95"
                                            >
                                                <MenuItems
                                                    class="absolute right-0 top-0 z-10 mt-2 w-56 origin-top-right divide-y divide-zinc-100 font-medium rounded bg-white border border-zinc-200 shadow-2xl focus:outline-none"
                                                >
                                                    <div
                                                        class="py-1 px-1"
                                                        v-if="
                                                            member.id !==
                                                                user.id &&
                                                            user
                                                                .current_workspace
                                                                .role !== 'USER'
                                                        "
                                                    >
                                                        <MenuItem
                                                            v-slot="{ active }"
                                                        >
                                                            <div
                                                                @click="
                                                                    changeUserRole(
                                                                        member,
                                                                        member
                                                                            .membership
                                                                            .role ==
                                                                            'USER'
                                                                            ? 'ADMIN'
                                                                            : 'USER'
                                                                    )
                                                                "
                                                                class="flex items-center text-zinc-800 hover:bg-zinc-100 rounded space-x-2 px-4 py-2 text-sm cursor-pointer"
                                                            >
                                                                <div>
                                                                    {{
                                                                        member
                                                                            .membership
                                                                            .role ==
                                                                        "USER"
                                                                            ? "Change Role to Admin"
                                                                            : "Change Role to User"
                                                                    }}
                                                                </div>
                                                            </div>
                                                        </MenuItem>
                                                    </div>
                                                    <div
                                                        class="py-1 px-1"
                                                        v-if="
                                                            user
                                                                .current_workspace
                                                                .role !==
                                                                'USER' &&
                                                            member.id !==
                                                                user.id
                                                        "
                                                    >
                                                        <MenuItem
                                                            v-slot="{ active }"
                                                        >
                                                            <div
                                                                @click="
                                                                    confirmRemoveFromTeam(
                                                                        member.id
                                                                    )
                                                                "
                                                                class="flex items-center text-zinc-800 hover:bg-zinc-100 rounded space-x-2 px-4 py-2 text-sm cursor-pointer"
                                                            >
                                                                Remove From Team
                                                            </div>
                                                        </MenuItem>
                                                    </div>
                                                    <div
                                                        class="py-1 px-1"
                                                        v-if="
                                                            member.id ===
                                                            user.id
                                                        "
                                                    >
                                                        <MenuItem
                                                            v-slot="{ active }"
                                                        >
                                                            <div
                                                                @click="
                                                                    confirmLeaveTeam
                                                                "
                                                                class="flex items-center text-zinc-800 hover:bg-zinc-100 rounded space-x-2 px-4 py-2 text-sm cursor-pointer"
                                                            >
                                                                Leave Team
                                                            </div>
                                                        </MenuItem>
                                                    </div>
                                                </MenuItems>
                                            </transition>
                                        </Menu>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <InviteIndex
            v-if="user.current_workspace.role !== 'USER'"
            :invites="invites"
        />

        <ConfirmationModal
            :show="beingLeaving"
            @close="beingLeaving = null"
            max-width="sm"
        >
            <template #title> Leave Team </template>

            <template #content>
                Are you sure you want to leave from the team?
            </template>

            <template #footer>
                <Button @click="beingLeaving = null" class="btn-secondary">
                    Cancel
                </Button>

                <Button
                    @click="leaveTeam"
                    :class="{
                        'ml-2 btn-danger': true,
                        'opacity-25': leaveTeamForm.processing,
                    }"
                    :disabled="leaveTeamForm.processing"
                >
                    Leave
                </Button>
            </template>
        </ConfirmationModal>

        <ConfirmationModal
            :show="beingRemoveFromTeam"
            @close="beingRemoveFromTeam = null"
            max-width="sm"
        >
            <template #title> Remove From Team </template>

            <template #content>
                Are you sure you want to remove this user from the team?
            </template>

            <template #footer>
                <Button
                    @click="beingRemoveFromTeam = null"
                    class="btn-secondary"
                >
                    Cancel
                </Button>

                <Button
                    @click="removeFromTeam"
                    :class="{
                        'ml-2 btn-danger': true,
                        'opacity-25': removeFromTeamForm.processing,
                    }"
                    :disabled="removeFromTeamForm.processing"
                >
                    Leave
                </Button>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>
