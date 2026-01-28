<template>
  <div class="fixed top-0 left-0 z-50 flex h-full w-full items-center justify-center">
    <div class="fixed top-0 left-0 h-full w-full bg-black opacity-40" @click="closeModal"></div>
    <div class="relative z-50 w-full max-w-lg rounded-md bg-white p-6 shadow-lg">
      <button class="absolute right-4 top-4 text-2xl text-gray-600" @click="closeModal">&times;</button>
      <p class="text-sm text-gray-500">{{ trans('block_help_title') }}</p>
      <h2 class="mb-3 text-xl font-semibold text-gray-900">{{ title }}</h2>
      <p class="whitespace-pre-line text-sm text-gray-700">{{ description }}</p>
    </div>
  </div>
</template>

<script>
import { trans } from "../scripts/trans";

export default {
  props: {
    title: String,
    description: String,
  },
  methods: {
    closeModal() {
      this.$emit('closeModal');
    },
    trans,
  },
};
</script>
