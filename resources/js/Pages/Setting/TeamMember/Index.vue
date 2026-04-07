<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useForm, usePage, Head } from "@inertiajs/vue3";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from "@/components/ui/alert-dialog";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { IconSearch, IconDots } from "@tabler/icons-vue";
import date from "@/date";
import debounce from "@/debounce";
import AppLayout from "@/layouts/AppLayout.vue";
import SettingsLayout from "@/layouts/settings/Layout.vue";
import InviteIndex from "./Invite/Index.vue";
import InviteCreate from "./Invite/Create.vue";
import * as teamMembersRoutes from "@/routes/setting/team-members";

const user = usePage().props.auth.user;
const inviteCreateDialog = ref<InstanceType<typeof InviteCreate> | null>(null);

defineProps<{
    users: Array<{
        id: string | number;
        name: string;
        email: string;
        created_at: string;
        membership: { role: string };
    }>;
    invites: Array<{
        id: string | number;
        name: string;
        email: string;
        role: string;
        created_at: string;
    }>;
}>();

const searchForm = useForm({
    q: "",
});

const updateUserForm = useForm({
    role: null as string | null,
});

const removeFromTeamForm = useForm({});
const beingRemoveFromTeam = ref<string | number | null>(null);

const leaveTeamForm = useForm({});
const beingLeaving = ref<boolean>(false);

const confirmRemoveFromTeam = (id: string | number) => {
    beingRemoveFromTeam.value = id;
};

const removeFromTeam = () => {
    if (!beingRemoveFromTeam.value) {
        return;
    }

    removeFromTeamForm.delete(
        teamMembersRoutes.destroy.url(beingRemoveFromTeam.value),
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => (beingRemoveFromTeam.value = null),
        }
    );
};

const confirmLeaveTeam = () => {
    beingLeaving.value = true;
};

const leaveTeam = () => {
    leaveTeamForm.delete(teamMembersRoutes.leave.url(), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (beingLeaving.value = false),
    });
};

const changeUserRole = (member: { id: string | number }, role: string) => {
    updateUserForm.role = role;

    updateUserForm.put(teamMembersRoutes.role.url(member.id), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => updateUserForm.reset(),
    });
};

const searchDebounce = debounce(function () {
    searchForm.get(teamMembersRoutes.index.url(), {
        preserveScroll: true,
        preserveState: true,
    });
}, 300);

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    if (params.get('q')) {
        searchForm.q = params.get('q') ?? '';
    }
});
</script>

<template>
    <Head title="Team Members" />
    <AppLayout>
        <SettingsLayout>
            <AlertDialog :open="beingLeaving" @update:open="(val) => (beingLeaving = val)">
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Leave Team</AlertDialogTitle>
                        <AlertDialogDescription>
                            Are you sure you want to leave from the team?
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel @click="beingLeaving = false">Cancel</AlertDialogCancel>
                        <AlertDialogAction
                            @click="leaveTeam"
                            class="bg-red-600 hover:bg-red-700"
                            :class="{ 'opacity-25': leaveTeamForm.processing }"
                        >
                            Leave
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>

            <AlertDialog :open="beingRemoveFromTeam != null" @update:open="(val) => !val && (beingRemoveFromTeam = null)">
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Remove From Team</AlertDialogTitle>
                        <AlertDialogDescription>
                            Are you sure you want to remove this user from the team?
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel @click="beingRemoveFromTeam = null">Cancel</AlertDialogCancel>
                        <AlertDialogAction
                            @click="removeFromTeam"
                            class="bg-red-600 hover:bg-red-700"
                            :class="{ 'opacity-25': removeFromTeamForm.processing }"
                        >
                            Remove
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>

            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold">Users</h2>
                <div class="flex space-x-2">
                    <div class="relative flex-1">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                        >
                            <IconSearch class="h-5 w-5 text-zinc-400" />
                        </div>
                        <Input
                            class="w-full pl-10"
                            id="search-field"
                            autocomplete="off"
                            type="text"
                            placeholder="Search..."
                            v-model="searchForm.q"
                            @keyup="searchDebounce"
                        />
                    </div>

                    <Button @click="inviteCreateDialog?.open()">Invite</Button>
                </div>
            </div>

            <div class="flex flex-col">
                <div class="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Name</TableHead>
                                <TableHead>E-mail</TableHead>
                                <TableHead>Role</TableHead>
                                <TableHead>Sign up</TableHead>
                                <TableHead><span class="sr-only">Actions</span></TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="member in users" :key="member.id">
                                <TableCell>{{ member.name }}</TableCell>
                                <TableCell>{{ member.email }}</TableCell>
                                <TableCell>{{ member.membership.role }}</TableCell>
                                <TableCell>{{ date.formatDateTime(member.created_at) }}</TableCell>
                                <TableCell>
                                    <DropdownMenu
                                        v-if="
                                            member.id === user.id ||
                                            user.current_workspace.role !== 'USER'
                                        "
                                    >
                                        <DropdownMenuTrigger as-child>
                                            <Button variant="ghost" size="icon">
                                                <IconDots class="h-4 w-4 text-zinc-500" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuItem
                                                v-if="
                                                    member.id !== user.id &&
                                                    user.current_workspace.role !== 'USER'
                                                "
                                                @click="changeUserRole(member, member.membership.role == 'USER' ? 'ADMIN' : 'USER')"
                                            >
                                                {{
                                                    member.membership.role == "USER"
                                                        ? "Change Role to Admin"
                                                        : "Change Role to User"
                                                }}
                                            </DropdownMenuItem>
                                            <DropdownMenuSeparator
                                                v-if="
                                                    member.id !== user.id &&
                                                    user.current_workspace.role !== 'USER'
                                                "
                                            />
                                            <DropdownMenuItem
                                                v-if="
                                                    user.current_workspace.role !== 'USER' &&
                                                    member.id !== user.id
                                                "
                                                class="text-red-600 focus:text-red-600"
                                                @click="confirmRemoveFromTeam(member.id)"
                                            >
                                                Remove From Team
                                            </DropdownMenuItem>
                                            <DropdownMenuItem
                                                v-if="member.id === user.id"
                                                class="text-red-600 focus:text-red-600"
                                                @click="confirmLeaveTeam"
                                            >
                                                Leave Team
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>

            <InviteIndex
                v-if="user.current_workspace.role !== 'USER'"
                :invites="invites"
            />

            <InviteCreate ref="inviteCreateDialog" />
        </SettingsLayout>
    </AppLayout>
</template>
