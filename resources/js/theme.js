import { ref, onMounted, watchEffect, computed, watch } from "vue";
import { usePage } from "@inertiajs/vue3";

export function useDarkTheme() {
    const isDarkTheme = ref(false);

    // Get the authenticated user from Inertia page props
    const user = computed(() => usePage().props.auth?.user);

    const updateTheme = () => {
        const prefersDarkScheme = window.matchMedia(
            "(prefers-color-scheme: dark)"
        );

        // If user is not authenticated, follow system theme
        if (!user.value) {
            isDarkTheme.value = prefersDarkScheme.matches;
        } else {
            // User is authenticated, check their theme preference
            if (user.value.theme === "DARK") {
                isDarkTheme.value = true; // Force dark theme
            } else if (user.value.theme === "LIGHT") {
                isDarkTheme.value = false; // Force light theme
            } else if (user.value.theme === "SYSTEM") {
                // Follow system preference
                isDarkTheme.value = prefersDarkScheme.matches;
            }
        }
    };

    onMounted(() => {
        updateTheme();

        // Watch for system changes in case "SYSTEM" or unauthenticated users follow system theme
        const mediaQuery = window.matchMedia("(prefers-color-scheme: dark)");
        mediaQuery.addEventListener("change", updateTheme);

        // Clean up the event listener when component unmounts
        return () => mediaQuery.removeEventListener("change", updateTheme);
    });

    // Automatically watch the `isDarkTheme` ref and update the HTML class
    watchEffect(() => {
        if (isDarkTheme.value) {
            document.documentElement.classList.add("dark");
        } else {
            document.documentElement.classList.remove("dark");
        }
    });

    // Watch for changes in user theme preference and update automatically
    watch(
        () => user.value?.theme,
        () => {
            updateTheme(); // Re-run the theme update when the user preference changes
        }
    );

    return {
        isDarkTheme,
    };
}
