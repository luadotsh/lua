<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { google } from '@/routes/auth';
</script>

<template>
    <!--
        Button and divider are siblings on purpose: they inherit the auth
        layout's gap-6, so the divider sits the same distance from the button
        above it as from the form below. Wrapping them would make it hug the
        button.
    -->
    <Button variant="outline" as-child class="w-full">
        <a :href="google().url">
            <img src="/images/oauth/google.svg" class="h-4 w-4 mr-2" alt="Google" />
            Continue with Google
        </a>
    </Button>

    <div
        class="relative text-center text-sm after:absolute after:inset-0 after:top-1/2 after:z-0 after:flex after:items-center after:border-t after:border-border"
    >
        <span class="relative z-10 bg-background px-2 text-muted-foreground">
            Or continue with
        </span>
    </div>
</template>
