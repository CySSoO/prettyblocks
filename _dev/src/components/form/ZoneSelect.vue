<script setup>
import { defineComponent, ref, watch, computed, nextTick } from 'vue'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { ChevronUpDownIcon } from '@heroicons/vue/24/solid'
import { usePrettyBlocksContext } from '../../store/pinia'
import { storeToRefs } from 'pinia'
import { trans } from '../../scripts/trans'

defineComponent({
  Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions,
  ChevronUpDownIcon
})
const props = defineProps({
  title: String,
  modelValue: {
    type: Object,
    default: () => ({
      name: 'displayHome',
      alias: '',
      priority: false
    })
  }
})
const searchQuery = ref('')
const searchInput = ref(null)
const optionsPanel = ref(null)
const items = ref([])

const filteredItems = computed(() => {
  if (!searchQuery.value.trim()) {
    return items.value
  }

  const query = searchQuery.value.toLowerCase()

  return items.value.filter((item) => {
    const alias = item.alias || ''
    const name = item.name || ''

    return alias.toLowerCase().includes(query) || name.toLowerCase().includes(query)
  })
})

const focusSearchInput = () => {
  nextTick(() => {
    if (searchInput.value) {
      searchInput.value.focus()
      searchInput.value.select()
    }
  })
}

const focusFirstOption = () => {
  nextTick(() => {
    const firstOption = optionsPanel.value?.querySelector('[role="option"]')

    if (firstOption) {
      firstOption.focus()
    }
  })
}

const resetSearch = () => {
  searchQuery.value = ''
}

const changetItem = (item) => {
  props.modelValue.name = item.name
  props.modelValue.alias = item.alias
  props.modelValue.priority = true

}


const emit = defineEmits(['update:modelValue'])
let prettyBlocksContext = usePrettyBlocksContext()
const idLang = computed(() => prettyBlocksContext.psContext.id_lang)
let currentZone = false
let priorityZone = ref(null)
let presentZone = false



watch(() => prettyBlocksContext.zones, (zonesState) => {
  items.value = zonesState
  presentZone = zonesState.find(zone => zone.name === prettyBlocksContext.currentZone.zoneToFocus) || false
  if(presentZone) {
    currentZone = presentZone
  } else {
    currentZone = zonesState[0]
  }
  onInput(currentZone)
})





function onInput(zone) {
  prettyBlocksContext.$patch({
    currentZone: {
      name: zone.name,
      alias: zone.alias,
      priority: zone.priority,
      zoneToFocus: zone.name
    }
  })

  prettyBlocksContext.sendPrettyBlocksEvents('focusOnZone', zone.name)

  emit('update:modelValue', {
    name: zone.name,
    alias: zone.alias,
    priority: zone.priority,
    zoneToFocus: zone.name

  })

  resetSearch()
}

// watch(() => props.modelValue, onInput)
</script>

<template>
  <Listbox as="div" @update:model-value="onInput" :value="props.modelValue">
    <ListboxLabel class="block text-sm font-medium text-gray-700">{{ title }}</ListboxLabel>
    <div class="mt-1 relative">
      <ListboxButton
        class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo focus:border-indigo sm:text-sm">
        <span class="flex items-center break-all">
          <span class="block line-clamp-1 truncate max-w-48" v-if="typeof props.modelValue.alias === 'undefined'">
            {{ trans('search_zone') }}
          </span>
          <span class="block line-clamp-1 truncate max-w-48" v-else>
            {{ props.modelValue.alias || props.modelValue.name }}
          </span>
        </span>
        <span class="ml-3 absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
          <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
        </span>
      </ListboxButton>
      <transition
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
        @after-enter="focusSearchInput"
        @after-leave="resetSearch"
      >
        <ListboxOptions
          ref="optionsPanel"
          class="absolute z-10 mt-1 w-[200%] bg-white shadow-lg max-h-56 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
        >
          <div class="px-3 pb-2">
            <input
              ref="searchInput"
              v-model="searchQuery"
              type="search"
              class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo focus:outline-none focus:ring-1 focus:ring-indigo"
              :placeholder="trans('search_zone')"
              @keydown.down.prevent="focusFirstOption"
            >
          </div>
          <template v-if="filteredItems.length">
            <ListboxOption
              as="template"
              v-for="(item, index) in filteredItems"
              :key="index"
              :value="item"
              v-slot="{ active, selected }"
            >
              <li
                @click="changetItem(item)"
                :class="[active ? 'text-white bg-indigo' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']"
              >
                <div class="flex items-center">
                  <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                    {{ item.alias !== '' ? item.alias : item.name }}
                  </span>
                </div>
              </li>
            </ListboxOption>
          </template>
          <div v-else class="px-3 py-2 text-sm text-gray-500">
            {{ trans('no_matching_zones') }}
          </div>
        </ListboxOptions>
      </transition>
    </div>
  </Listbox>
</template>
