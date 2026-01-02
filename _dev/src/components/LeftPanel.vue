<script setup>
import { ref, onMounted, defineComponent,onBeforeUnmount, computed, watchEffect, watch } from "vue";
import SortableList from "./SortableList.vue";
import MenuGroup from "./MenuGroup.vue";
import MenuItem from "./MenuItem.vue";
import ButtonLight from "./ButtonLight.vue";
import Button from "./Button.vue";
import { HttpClient } from "../services/HttpClient";
import Block from "../scripts/block";
import ZoneSelect from "./form/ZoneSelect.vue";
import Input from "./form/Input.vue";
import { storeToRefs } from 'pinia'
/* Demo data */
// import { v4 as uuidv4 } from 'uuid'
import emitter from "tiny-emitter/instance";
import { useStore, useCurrentZone, contextShop, storedBlocks, usePrettyBlocksContext } from "../store/pinia";
import { trans } from "../scripts/trans";

import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({
  position: "top",
});

const prettyblocks_env = ref(window.prettyblocks_env.PRETTYBLOCKS_REMOVE_ADS);

defineComponent({
  SortableList,
  MenuGroup,
  MenuItem,
  ButtonLight,
  Button,
  ZoneSelect,
  Input,
});
 let prettyBlocksContext = usePrettyBlocksContext();
watch(() => prettyBlocksContext.currentZone, (currentZone) => {
  displayZoneName.value = currentZone.name
  initStates()
}, { deep: true })


const prettyblocks_version = ref(security_app.prettyblocks_version);
/**
 * Load block config
 */
const loadStateConfig = async (e) => {
  prettyBlocksContext.$patch({
    currentBlock: {
      id_prettyblocks: e.id_prettyblocks,
      instance_id: e.instance_id,
      code: e.code,
      subSelected: null,
      need_reload: e.need_reload,
      states: e.children
    }
  })
};


let displayZoneName = ref();
const loadSubState = async (e) => {
  // set store cuurent block name
  prettyBlocksContext.$patch({
    currentBlock: {
      need_reload: e.need_reload,
      id_prettyblocks: e.id_prettyblocks,
      subSelected: e.id,
    }
  });
};


const { blocksFormatted } = storeToRefs(prettyBlocksContext);
let groups = blocksFormatted; 

const initStates = async () => {
  prettyBlocksContext.initStates()
  
};
/**
 * Push an empty State (repeater)
 */
const loadEmptyState = async (e) => {
  let element = {
    id_prettyblocks: e.id_prettyblocks,
  };
  let context = prettyBlocksContext.psContext
  loadSubState(element);
  const params = {
    id_prettyblocks: e.id_prettyblocks,
    action: "getEmptyState",
    ajax: true,
    ctx_id_lang: context.id_lang,
    ctx_id_shop: context.id_shop,
    ajax_token: security_app.ajax_token,
  };
  HttpClient.get(ajax_urls.state, params)
    .then((data) => {
      initStates();
      if (e.need_reload) {
        // emitter.emit("reloadIframe", e.id_prettyblocks);
        prettyBlocksContext.reloadIframe()
      }
      // get html block data
        prettyBlocksContext.sendPrettyBlocksEvents('reloadBlock', {id_prettyblocks: e.id_prettyblocks})
      
    })
    .catch((error) => console.error(error));
};

let currentBlock = useStore();
const state = ref({
  name: "displayHome",
});


onMounted(() => {
  document.addEventListener('DOMContentLoaded', async () => {
    setTimeout(async () => {
      checkClipboardContent();
    }, 500);
  });
})

/**
  * Copy current zone
  */
const copyZone = async () => {
  let context = await prettyBlocksContext.psContext;
  let current_zone = prettyBlocksContext.currentZone.name;
  const params = {
    action: "CopyZone",
    zone: current_zone,
    ctx_id_lang: context.id_lang,
    ctx_id_shop: context.id_shop,
  };
  localStorage.setItem('prettyblocks_clipboard', JSON.stringify(params));
  checkClipboardContent();
}

const pasteZone = async () => {
  let current_zone = prettyBlocksContext.currentZone.name;
  const storedData = localStorage.getItem('prettyblocks_clipboard');
  if (storedData) {
    const data = JSON.parse(storedData);
    if (data.hasOwnProperty('zone')) {
      let params = {
        ...data,
        zone_name_to_paste: current_zone,
        ajax_token: security_app.ajax_token,
        ajax: true,
      };
      HttpClient.post(ajax_urls.state, params).then((response) => {
        if (response.success) {
          toaster.show(response.message);
          localStorage.removeItem('prettyblocks_clipboard');
          checkClipboardContent();
          prettyBlocksContext.reloadIframe();
          prettyBlocksContext.initStates();
        }
      }).catch(error => console.error(error));
    }
  }
}

let showCopyZone = ref(false);
const checkClipboardContent = async () => {
  try {
    const storedData = localStorage.getItem('prettyblocks_clipboard');
    if (storedData) {
      const data = JSON.parse(storedData);
      showCopyZone.value = data.hasOwnProperty('zone');
    } else {
      showCopyZone.value = false;
    }
  } catch (error) {
    showCopyZone.value = false;
  }
};

const templateName = ref('');
const selectedTemplate = ref(null);
const layoutTemplates = ref([]);
const loadingTemplates = ref(false);
const layoutManagerOpen = ref(false);
const selectedTemplateTargetZone = ref('');

const availableZones = computed(() => prettyBlocksContext.zones || []);

const getDefaultZoneName = () => {
  if (prettyBlocksContext.currentZone?.name) {
    return prettyBlocksContext.currentZone.name;
  }

  if (availableZones.value.length) {
    return availableZones.value[0].name;
  }

  return '';
};

const fetchLayoutTemplates = async () => {
  loadingTemplates.value = true;
  const context = prettyBlocksContext.psContext;
  const params = {
    action: "GetLayoutTemplates",
    ajax_token: security_app.ajax_token,
    ctx_id_lang: context.id_lang,
    ctx_id_shop: context.id_shop,
    ajax: true,
  };

  try {
    const response = await HttpClient.get(ajax_urls.state, params);
    layoutTemplates.value = (response.templates || []).map((template) => ({
      ...template,
      editingName: template.name,
      targetZone: template.zone_name || getDefaultZoneName(),
    }));
    if (layoutTemplates.value.length > 0 && !selectedTemplate.value) {
      selectedTemplate.value = layoutTemplates.value[0].id_prettyblocks_layout_template;
    }
    if (!selectedTemplateTargetZone.value) {
      selectedTemplateTargetZone.value = getDefaultZoneName();
    }
  } catch (error) {
    console.error(error);
  } finally {
    loadingTemplates.value = false;
  }
};

const saveLayoutTemplate = async () => {
  if (loadingTemplates.value) {
    return;
  }

  const name = templateName.value.trim();
  if (!name) {
    toaster.error(trans('template_name_required'));
    return;
  }

  const context = prettyBlocksContext.psContext;
  const params = {
    action: "SaveLayoutTemplate",
    zone: prettyBlocksContext.currentZone.name,
    template_name: name,
    ajax_token: security_app.ajax_token,
    ctx_id_lang: context.id_lang,
    ctx_id_shop: context.id_shop,
    ajax: true,
  };

  try {
    const response = await HttpClient.post(ajax_urls.state, params);
    if (response.success) {
      toaster.show(response.message);
      templateName.value = '';
      const templateId = response.template?.id_prettyblocks_layout_template || response.template?.id;
      if (templateId) {
        selectedTemplate.value = templateId;
      }
      await fetchLayoutTemplates();
    } else {
      toaster.error(response.message);
    }
  } catch (error) {
    toaster.error(trans('template_save_error'));
    console.error(error);
  }
};

const insertLayoutTemplate = async () => {
  if (loadingTemplates.value || !selectedTemplate.value) {
    return;
  }

  const context = prettyBlocksContext.psContext;
  const params = {
    action: "InsertLayoutTemplate",
    template_id: selectedTemplate.value,
    zone: selectedTemplateTargetZone.value || prettyBlocksContext.currentZone.name,
    ajax_token: security_app.ajax_token,
    ctx_id_lang: context.id_lang,
    ctx_id_shop: context.id_shop,
    ajax: true,
  };

  try {
    const response = await HttpClient.post(ajax_urls.state, params);
    if (response.success) {
      toaster.show(response.message);
      prettyBlocksContext.reloadIframe();
      prettyBlocksContext.initStates();
    } else {
      toaster.error(response.message);
    }
  } catch (error) {
    toaster.error(trans('template_insert_error'));
    console.error(error);
  }
};

const insertLayoutTemplateInto = async (template) => {
  if (loadingTemplates.value || !template?.id_prettyblocks_layout_template) {
    return;
  }

  const context = prettyBlocksContext.psContext;
  const targetZone = template.targetZone || getDefaultZoneName();
  const params = {
    action: "InsertLayoutTemplate",
    template_id: template.id_prettyblocks_layout_template,
    zone: targetZone,
    ajax_token: security_app.ajax_token,
    ctx_id_lang: context.id_lang,
    ctx_id_shop: context.id_shop,
    ajax: true,
  };

  try {
    const response = await HttpClient.post(ajax_urls.state, params);
    if (response.success) {
      toaster.show(response.message);
      prettyBlocksContext.reloadIframe();
      prettyBlocksContext.initStates();
    } else {
      toaster.error(response.message);
    }
  } catch (error) {
    toaster.error(trans('template_insert_error'));
    console.error(error);
  }
};

const updateLayoutTemplate = async (template) => {
  if (loadingTemplates.value || !template?.id_prettyblocks_layout_template) {
    return;
  }

  const name = (template.editingName || '').trim();
  if (!name) {
    toaster.error(trans('template_name_required'));
    return;
  }

  const context = prettyBlocksContext.psContext;
  const params = {
    action: "UpdateLayoutTemplate",
    template_id: template.id_prettyblocks_layout_template,
    template_name: name,
    zone_name: template.zone_name || '',
    ajax_token: security_app.ajax_token,
    ctx_id_lang: context.id_lang,
    ctx_id_shop: context.id_shop,
    ajax: true,
  };

  try {
    const response = await HttpClient.post(ajax_urls.state, params);
    if (response.success) {
      toaster.show(response.message || trans('template_updated'));
      await fetchLayoutTemplates();
    } else {
      toaster.error(response.message || trans('template_update_error'));
    }
  } catch (error) {
    toaster.error(trans('template_update_error'));
    console.error(error);
  }
};

const deleteLayoutTemplate = async (template) => {
  if (!template?.id_prettyblocks_layout_template) {
    return;
  }

  if (!confirm(trans('confirm_delete_template'))) {
    return;
  }

  const context = prettyBlocksContext.psContext;
  const params = {
    action: "DeleteLayoutTemplate",
    template_id: template.id_prettyblocks_layout_template,
    ajax_token: security_app.ajax_token,
    ctx_id_lang: context.id_lang,
    ctx_id_shop: context.id_shop,
    ajax: true,
  };

  try {
    const response = await HttpClient.post(ajax_urls.state, params);
    if (response.success) {
      toaster.show(response.message || trans('template_deleted'));
      await fetchLayoutTemplates();
    } else {
      toaster.error(response.message || trans('template_delete_error'));
    }
  } catch (error) {
    toaster.error(trans('template_delete_error'));
    console.error(error);
  }
};

watch(availableZones, () => {
  if (!selectedTemplateTargetZone.value) {
    selectedTemplateTargetZone.value = getDefaultZoneName();
  }

  layoutTemplates.value = layoutTemplates.value.map((template) => ({
    ...template,
    targetZone: template.targetZone || getDefaultZoneName(),
  }));
});

watch(() => prettyBlocksContext.currentZone?.name, (zoneName) => {
  if (zoneName) {
    selectedTemplateTargetZone.value = zoneName;
  }
});
/**
 * Delete all blocks in current zone
 */
const deleteAllBlocks = async () => {
  if(confirm('Warning: This will delete all blocks in this zone. Are you sure?') == false) {
    return;
  }
  let current_zone = prettyBlocksContext.currentZone.name;  
  let context = await prettyBlocksContext.psContext;
  const params = {
    action: "DeleteAllBlocks",
    zone: current_zone,
    ajax_token: security_app.ajax_token,
    ctx_id_lang: context.id_lang,
    ctx_id_shop: context.id_shop,
    ajax: true,
  };
  HttpClient.post(ajax_urls.state, params).then((response) => {

          if (response.success) {
            toaster.show(response.message)
            window.location.reload()
          }
    })
    .catch(error => console.error(error));
}

prettyBlocksContext.on('iframeLoaded', () => {
  setTimeout(() => {
    checkClipboardContent();
    fetchLayoutTemplates();
  }, 1000);

});
</script>

<template>
  <div id="leftPanel" class="border-r border-gray-200">
    <div class="flex flex-col h-full">
      <div class="p-2 border-b border-gray-200">
        <div class="flex items-center space-around">
          <div>
            <ZoneSelect v-model="state" />
          </div>
          <div class="pl-2 mt-[6px]">
            <ButtonLight
              type="secondary"
              icon="TrashIcon"
              @click="deleteAllBlocks"
              size="6"
              :title="trans('delete_zone_blocks')"
            />
          </div>
          <div class="mt-[6px]">
            <ButtonLight
              type="secondary"
              icon="Square2StackIcon"
              @click="copyZone"
              size="6"
              :title="trans('copy_zone')"
            />
          </div>
          <div class="mt-[6px]" v-if="showCopyZone">
            <ButtonLight
              type="secondary"
              icon="ArrowDownOnSquareStackIcon"
              @click="pasteZone"
              size="6"
              :title="trans('paste_zone')"
            />
          </div>
        </div>
      </div>

      <div class="p-2 border-b border-gray-200 space-y-2">
        <div class="flex items-end gap-2">
          <div class="flex-1">
            <Input :title="trans('template_name_label')" :placeholder="trans('template_name_placeholder')" v-model="templateName" />
          </div>
          <div class="pb-1">
            <ButtonLight type="secondary" icon="BookmarkSquareIcon" @click="saveLayoutTemplate" size="6" />
          </div>
        </div>
        <div class="flex items-end gap-2">
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700">{{ trans('saved_layout_templates') }}</label>
            <select
              v-model="selectedTemplate"
              class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:outline-none focus:ring-1 focus:ring-indigo focus:border-indigo sm:text-sm"
              :disabled="loadingTemplates || layoutTemplates.length === 0"
            >
              <option v-if="layoutTemplates.length === 0" value="">{{ trans('no_saved_template') }}</option>
              <option
                v-for="template in layoutTemplates"
                :key="template.id_prettyblocks_layout_template"
                :value="template.id_prettyblocks_layout_template"
              >
                {{ template.name }}
              </option>
            </select>
          </div>
          <div class="pb-1">
            <ButtonLight type="secondary" icon="ArrowDownTrayIcon" @click="insertLayoutTemplate" size="6" />
          </div>
        </div>
        <div class="flex items-end gap-2">
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700">{{ trans('target_hook_label') }}</label>
            <select
              v-model="selectedTemplateTargetZone"
              class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:outline-none focus:ring-1 focus:ring-indigo focus:border-indigo sm:text-sm"
            >
              <option v-for="zone in availableZones" :key="zone.name" :value="zone.name">
                {{ zone.alias || zone.name }}
              </option>
            </select>
          </div>
          <div class="pb-1 flex gap-1">
            <ButtonLight type="secondary" icon="ArrowPathIcon" @click="fetchLayoutTemplates" size="6" :title="trans('refresh_templates')" />
            <ButtonLight type="secondary" :icon="layoutManagerOpen ? 'ChevronUpIcon' : 'ChevronDownIcon'" @click="layoutManagerOpen = !layoutManagerOpen" size="6" :title="trans('layout_manager')" />
          </div>
        </div>
      </div>

      <div class="p-2 border-b border-gray-200" v-if="layoutManagerOpen">
        <div class="flex items-center justify-between pb-2">
          <h3 class="text-sm font-semibold text-gray-800">{{ trans('layout_manager') }}</h3>
        </div>
        <div v-if="loadingTemplates" class="text-sm text-gray-500">{{ trans('loading') }}...</div>
        <div v-else-if="layoutTemplates.length === 0" class="text-sm text-gray-500">{{ trans('no_saved_template') }}</div>
        <div v-else class="space-y-3">
          <div
            v-for="template in layoutTemplates"
            :key="template.id_prettyblocks_layout_template"
            class="rounded-md border border-gray-200 bg-white p-3 shadow-sm"
          >
            <div class="grid grid-cols-1 gap-2">
              <Input :title="trans('template_name_label')" v-model="template.editingName" />
              <div>
                <label class="block text-sm font-medium text-gray-700">{{ trans('saved_zone_label') }}</label>
                <select
                  v-model="template.zone_name"
                  class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:outline-none focus:ring-1 focus:ring-indigo focus:border-indigo sm:text-sm"
                >
                  <option value=""></option>
                  <option v-for="zone in availableZones" :key="zone.name" :value="zone.name">
                    {{ zone.alias || zone.name }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">{{ trans('target_hook_label') }}</label>
                <select
                  v-model="template.targetZone"
                  class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:outline-none focus:ring-1 focus:ring-indigo focus:border-indigo sm:text-sm"
                >
                  <option v-for="zone in availableZones" :key="zone.name" :value="zone.name">
                    {{ zone.alias || zone.name }}
                  </option>
                </select>
              </div>
            </div>
            <div class="flex flex-wrap gap-2 pt-2">
              <ButtonLight type="secondary" icon="CheckCircleIcon" @click="updateLayoutTemplate(template)" :title="trans('rename_template')" />
              <ButtonLight type="secondary" icon="ArrowDownTrayIcon" @click="insertLayoutTemplateInto(template)" :title="trans('inject_template')" />
              <ButtonLight type="secondary" icon="TrashIcon" @click="deleteLayoutTemplate(template)" :title="trans('delete_template')" />
            </div>
          </div>
        </div>
      </div>

      <div class="overflow-y-auto flex-grow p-2 border-b border-gray-200">
        <!-- sortable component is used to sort by drag and drop -->
        <SortableList :items="groups" group="menu-group">
          
          <template v-slot="{ element }">
            <!-- group of element (collapsable) -->
            <MenuGroup
              @changeState="loadStateConfig"
              @pushEmptyState="loadEmptyState(element)"
              :id="element.id"
              :id_prettyblocks="element.id_prettyblocks"
              :title="element.title"
              :icon="element.icon"
              :icon_path="element.icon_path"
              :config="true"
              :element="element"
              :is_parent="true"
            >
              <SortableList
                :items="element.children"
                :group="'menu-group-' + element.id_prettyblocks"
                action="updateStatePosition"
              >
                <template v-slot="{ element }">
                  <!-- items of the group -->
                  <MenuItem
                    :id="element.id.toString()"
                    :title="element.title"
                    :icon="element.icon"
                    :id_prettyblocks="element.id_prettyblocks"
                    :element="element"
                    :is_child="true"
                    @click="loadSubState(element)"
                  >
                  </MenuItem>
                </template>
              </SortableList>
            </MenuGroup>
          </template>
        </SortableList>
        <ButtonLight
          icon="ArrowDownOnSquareStackIcon"
          @click="prettyBlocksContext.emit('toggleModal')"
          class="bg-slate-200 p-2 text-center hover:bg-indigo hover:bg-opacity-10 w-full text-indigo"
        >
          {{ trans("add_new_element") }}
        </ButtonLight>
      </div>
      <div class="p-2 text-sm text-center">
        <a class="text-indigo" href="https://prettyblocks.io/" target="_blank"
          >PrettyBlocks (v{{ prettyblocks_version }}) </a
        ><br />
        Made with ❤️ by
        <a class="text-indigo" href="https://www.prestasafe.com" target="_blank"
          >PrestaSafe</a
        ><br />
        <a v-if="!prettyblocks_env" href="https://prettyblocks.io/pro" class="text-red-500" target="_blank">{{ trans('get_pro') }}</a>
      </div>
    </div>
  </div>
</template>

<style scoped>
#leftPanel {
  transition: all 0.5s ease;
}
</style>
