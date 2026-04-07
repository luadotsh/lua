<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { google } from '@/routes/auth';
</script>

<template>
    <div class="flex flex-col gap-4">
        <Button variant="outline" as-child class="w-full">
            <a :href="google().url">
                <img src="/images/oauth/google.svg" class="h-4 w-4 mr-2" alt="Google" />
                Continue with Google
            </a>
        </Button>

        <div class="relative">
            <Separator />
            <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-background px-2 text-xs text-muted-foreground">
                Or continue with
            </span>
        </div>
    </div>
</template>
