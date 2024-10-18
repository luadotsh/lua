<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import Accordion from "@/Components/Accordion.vue";
import Textarea from "@/Components/Textarea.vue";
import InputError from "@/Components/InputError.vue";
import Button from "@/Components/Button.vue";
import { PhStar } from "@phosphor-icons/vue";

const ratingCurrentHover = ref(null);

const { product } = defineProps({
    product: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    rating: 0,
    message: "",
});

const submit = () => {
    form.post(
        route("customer.product-reviews.store", {
            id: product.id,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {},
        }
    );
};
</script>

<template>
    <Accordion :is-open="true">
        <template #title> Product Review </template>
        <template #content>
            <div class="col-span-6">
                <div class="flex itens-center space-x-1">
                    <button
                        type="button"
                        :title="`${i}/5`"
                        v-for="i in 5"
                        :key="i"
                        :class="{ 'cursor-pointer': true }"
                        @click="form.rating = i"
                    >
                        <PhStar
                            @mouseover="ratingCurrentHover = i"
                            @mouseleave="ratingCurrentHover = null"
                            weight="fill"
                            :class="[
                                form.rating >= i
                                    ? 'text-yellow-500'
                                    : 'text-zinc-200',
                                ratingCurrentHover >= i
                                    ? 'text-yellow-500'
                                    : 'text-zinc-200',
                                'h-6 w-6',
                            ]"
                        />
                    </button>
                </div>
                <InputError :message="form.errors.rating" class="mt-2" />
            </div>

            <div class="col-span-6">
                <Textarea
                    id="message"
                    type="text"
                    class="w-full"
                    rows="3"
                    placeholder="Add a review..."
                    v-model="form.message"
                />

                <InputError :message="form.errors.message" class="mt-2" />
            </div>

            <div>
                <Button
                    @click="submit"
                    :class="{
                        'flex items-center space-x-1.5 btn-primary': true,
                        'cursor-not-allowed': form.processing,
                    }"
                    :disabled="form.processing"
                >
                    <span>Submit</span>
                </Button>
            </div>
        </template>
    </Accordion>
</template>
