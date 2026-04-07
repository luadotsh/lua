import { ref } from 'vue';

const upgradeDialogOpen = ref(false);

export const useUpgradeDialog = () => ({
    upgradeDialogOpen,
    openUpgrade: () => {
        upgradeDialogOpen.value = true;
    },
    closeUpgrade: () => {
        upgradeDialogOpen.value = false;
    },
});
