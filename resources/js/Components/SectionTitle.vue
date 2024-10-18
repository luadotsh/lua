<template>
    <div
        v-if="$slots.title || $slots.description || $slots.titleActions"
        class="md:col-span-1 flex justify-between"
    >
        <div class="flex flex-1 flex-col">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-normal text-zinc-900">
                    <slot name="title" />
                </h3>

                <slot name="titleActions" />
            </div>

            <p class="mt-1 text-sm font-light text-zinc-400">
                <slot name="description" />
            </p>
        </div>
    </div>
</template>
