<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { computed, ref, watch } from "vue"
import { AvatarRoot } from "reka-ui"
import { cn } from "@/lib/utils"
import { getInitials } from "@/composables/useInitials"

const props = defineProps<{
  class?: HTMLAttributes["class"]
  src?: string | null
  name?: string
  fallbackClass?: HTMLAttributes["class"]
}>()

const hasProps = computed(() => props.src !== undefined || props.name !== undefined)

// An uploaded file that 404s would otherwise leave the browser's broken-image
// icon in place; falling back to the initials is what the viewer expects.
const failed = ref(false)

watch(() => props.src, () => { failed.value = false })

const showImage = computed(() => Boolean(props.src) && !failed.value)
</script>

<template>
  <AvatarRoot
    data-slot="avatar"
    :class="cn('relative flex size-8 shrink-0 overflow-hidden rounded-full', props.class)"
  >
    <template v-if="hasProps">
      <img
        v-if="showImage"
        :src="src!"
        :alt="name"
        class="aspect-square size-full object-cover"
        @error="failed = true"
      />
      <div
        v-else
        :class="cn('flex size-full items-center justify-center bg-muted font-medium', props.fallbackClass)"
      >
        {{ name ? getInitials(name) : '' }}
      </div>
    </template>
    <slot v-else />
  </AvatarRoot>
</template>
