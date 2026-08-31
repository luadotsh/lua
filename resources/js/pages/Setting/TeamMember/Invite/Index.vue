<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import dayjs from '@/dayjs';
import * as invitesRoutes from '@/routes/setting/invites';

defineProps<{
    invites: Array<{
        id: string | number;
        name: string;
        email: string;
        role: string;
        created_at: string;
    }>;
}>();

const deleteForm = useForm({});
const beingDeleted = ref<string | number | null>(null);

const formatDate = (date: string) => {
    return dayjs(date).format('MMMM D, YYYY h:mm A');
};

const confirmDeletion = (invite: { id: string | number }) => {
    beingDeleted.value = invite.id;
};

const deleteInvite = () => {
    if (!beingDeleted.value) {
        return;
    }

    deleteForm.delete(invitesRoutes.destroy.url(beingDeleted.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (beingDeleted.value = null),
    });
};
</script>

<template>
    <div v-if="invites.length >= 1" class="mt-8">
        <div class="mb-4 sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h2 class="text-lg font-semibold">Pending Invites</h2>
            </div>
        </div>

        <AlertDialog
            :open="beingDeleted != null"
            @update:open="(val) => !val && (beingDeleted = null)"
        >
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Delete Invite</AlertDialogTitle>
                    <AlertDialogDescription>
                        Are you sure you would like to cancel this invite?
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel @click="beingDeleted = null"
                        >Cancel</AlertDialogCancel
                    >
                    <AlertDialogAction
                        @click="deleteInvite"
                        class="bg-red-600 hover:bg-red-700"
                        >Delete</AlertDialogAction
                    >
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>

        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Name</TableHead>
                        <TableHead>E-mail</TableHead>
                        <TableHead>Role</TableHead>
                        <TableHead>Invited At</TableHead>
                        <TableHead
                            ><span class="sr-only">Actions</span></TableHead
                        >
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="invite in invites" :key="invite.id">
                        <TableCell>{{ invite.name }}</TableCell>
                        <TableCell>{{ invite.email }}</TableCell>
                        <TableCell>{{ invite.role }}</TableCell>
                        <TableCell>{{
                            formatDate(invite.created_at)
                        }}</TableCell>
                        <TableCell>
                            <a
                                v-if="
                                    $page.props.auth.user.current_workspace
                                        ?.role !== 'USER'
                                "
                                href="#"
                                @click.prevent="confirmDeletion(invite)"
                                class="font-medium text-red-600 hover:underline"
                                >Cancel</a
                            >
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </div>
</template>
