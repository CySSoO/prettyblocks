<script setup>
import Icon from '../Icon.vue'
import { defineProps, defineEmits } from 'vue'

const props = defineProps({
  title: String,
  icon: String,
  placeholder: String,
  type: {
    type: String,
    default: 'text'
  },
  name: {
    type: String,
    required: false
  },
  required: {
    type: Boolean,
    default: false
  },
  modelValue: {
    type: [String, Number],
    default: ''
  },
  inputProps: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['update:modelValue'])

function onInput(event) {
  emit('update:modelValue', event.target.value)
}

</script>

<template>
  <div>
    <label :for="name" class="block text-sm font-medium text-gray-700">{{ title }}</label>
    <div class="mt-1 relative rounded-md shadow-sm">
      <div v-if="icon" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <Icon :name="icon" class="h-5 w-5 text-gray-400" aria-hidden="true" />
      </div>
      <input @input="onInput" :value="props.modelValue"
        :name="name"
        :id="name"
        :type="props.type"
        ref="input"
        v-bind="props.inputProps"
        :class="['focus:ring-indigo focus:border-indigo block w-full sm:text-sm border-gray-300 rounded-md', { 'pl-10': icon }]"
        :placeholder="placeholder" :required="required" />
    </div>
  </div>
</template>
