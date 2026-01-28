<script setup>
import Icon from './Icon.vue'

defineProps({
  icon: String,
  disabled: {
    type: Boolean,
    default: false,
  },
})
</script>

<template>
  <button
    type="button"
    :disabled="disabled"
    :class="[
      'inline-flex items-center gap-2 p-1 rounded',
      disabled ? 'opacity-40 cursor-not-allowed' : 'hover:bg-black hover:bg-opacity-10',
    ]"
  >
    <Icon :name="icon"></Icon>
    <slot></slot>
  </button>
</template>
