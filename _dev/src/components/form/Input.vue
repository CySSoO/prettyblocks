<script setup>
import Icon from '../Icon.vue'
import { defineProps, defineEmits, computed } from 'vue'

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
  },
  min: {
    type: [Number, String],
    default: undefined
  },
  max: {
    type: [Number, String],
    default: undefined
  },
  step: {
    type: [Number, String],
    default: undefined
  }
})

const emit = defineEmits(['update:modelValue'])

const resolvedType = computed(() => props.type ?? 'text')
const resolvedMin = computed(() => (props.min === undefined ? (resolvedType.value === 'number' ? 0 : undefined) : props.min))
const resolvedMax = computed(() => (props.max === undefined ? (resolvedType.value === 'number' ? 12 : undefined) : props.max))
const resolvedStep = computed(() => (props.step === undefined ? (resolvedType.value === 'number' ? 1 : undefined) : props.step))

function onInput(event) {
  let value = event.target.value

  if (resolvedType.value === 'number' && value !== '') {
    let numericValue = Number(value)

    if (!Number.isNaN(numericValue)) {
      if (resolvedMin.value !== undefined && resolvedMin.value !== null) {
        numericValue = Math.max(numericValue, Number(resolvedMin.value))
      }

      if (resolvedMax.value !== undefined && resolvedMax.value !== null) {
        numericValue = Math.min(numericValue, Number(resolvedMax.value))
      }

      if (numericValue !== Number(value)) {
        event.target.value = String(numericValue)
        value = event.target.value
      }
    }
  }

  emit('update:modelValue', value)
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
        :type="resolvedType"
        :min="resolvedMin"
        :max="resolvedMax"
        :step="resolvedStep"
        ref="input"
        v-bind="props.inputProps"
        :class="['focus:ring-indigo focus:border-indigo block w-full sm:text-sm border-gray-300 rounded-md', { 'pl-10': icon }]"
        :placeholder="placeholder" :required="required" />
    </div>
  </div>
</template>
