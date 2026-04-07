export type UseInitialsReturn = {
    getInitials: (fullName?: string) => string;
};

export const getInitials = (fullName?: string): string => {
    if (!fullName) return '';

    const names = fullName.trim().split(' ');

    if (names.length === 0) return '';
    if (names.length === 1) return names[0].charAt(0).toUpperCase();

    return `${names[0].charAt(0)}${names[names.length - 1].charAt(0)}`.toUpperCase();
};

export const useInitials = (): UseInitialsReturn => {
    return { getInitials };
};
