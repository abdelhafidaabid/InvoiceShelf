<template>
  <ApiProviderModal />
  <BaseCard>
    <template #header>
      <div class="flex flex-wrap justify-between lg:flex-nowrap">
        <div>
          <h6 class="text-lg font-medium text-left">
            {{ $t('settings.menu_title.apis_providers') }}
          </h6>
          <p
            class="mt-2 text-sm leading-snug text-left text-gray-500"
            style="max-width: 680px"
          >
            {{ $t('api_provider.description') }}
          </p>
        </div>
        <div class="mt-4 lg:mt-0 lg:ml-2">
          <BaseButton
            variant="primary-outline"
            size="lg"
            @click="addProvider"
          >
            <template #left="slotProps">
              <PlusIcon :class="slotProps.class" />
            </template>
            {{ $t('api_provider.new_provider') }}
          </BaseButton>
        </div>
      </div>
    </template>

    <BaseTable ref="table" class="mt-16" :data="fetchData" :columns="columns">
      <template #cell-driver="{ row }">
        <span class="capitalize">{{ row.data.driver.replace('_', ' ') }}</span>
      </template>
      <template #cell-active="{ row }">
        <BaseBadge
          :bg-color="
            utils.getBadgeStatusColor(row.data.active ? 'YES' : 'NO').bgColor
          "
          :color="
            utils.getBadgeStatusColor(row.data.active ? 'YES' : 'NO').color
          "
        >
          {{ row.data.active ? 'YES' : 'NO' }}
        </BaseBadge>
      </template>
      <template #cell-actions="{ row }">
        <BaseDropdown>
          <template #activator>
            <div class="inline-block">
              <EllipsisHorizontalIcon class="w-5 text-gray-500" />
            </div>
          </template>

          <BaseDropdownItem @click="editProvider(row.data.id)">
            <PencilIcon class="h-5 mr-3 text-gray-600" />
            {{ $t('general.edit') }}
          </BaseDropdownItem>

          <BaseDropdownItem @click="removeProvider(row.data.id)">
            <TrashIcon class="h-5 mr-3 text-gray-600" />
            {{ $t('general.delete') }}
          </BaseDropdownItem>
        </BaseDropdown>
      </template>
    </BaseTable>
  </BaseCard>
</template>

<script setup>
import { useApiProviderStore } from '@/scripts/admin/stores/api-provider'
import { useModalStore } from '@/scripts/stores/modal'
import { useDialogStore } from '@/scripts/stores/dialog'
import { ref, computed, inject, reactive } from 'vue'
import ApiProviderModal from '@/scripts/admin/components/modal-components/ApiProviderModal.vue'
import { useI18n } from 'vue-i18n'
import {
  PlusIcon,
  EllipsisHorizontalIcon,
  PencilIcon,
  TrashIcon,
} from '@heroicons/vue/24/outline'
import BaseTable from '@/scripts/components/base/base-table/BaseTable.vue'

const { t } = useI18n()
const apiProviderStore = useApiProviderStore()
const modalStore = useModalStore()
const dialogStore = useDialogStore()

let table = ref('')
const utils = inject('utils')

const columns = computed(() => {
  return [
    {
      key: 'name',
      label: t('general.name'),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'driver',
      label: t('api_provider.driver'),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'active',
      label: t('api_provider.active'),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'actions',
      label: '',
      tdClass: 'text-right text-sm font-medium',
      sortable: false,
    },
  ]
})

async function fetchData({ page, sort }) {
  let data = reactive({
    orderByField: sort.fieldName || 'created_at',
    orderBy: sort.order || 'desc',
    page,
  })

  let response = await apiProviderStore.fetchProviders(data)

  return {
    data: response.data.data,
    pagination: {
      totalPages: response.data.meta.last_page,
      currentPage: page,
      totalCount: response.data.meta.total,
      limit: 10,
    },
  }
}

function addProvider() {
  modalStore.openModal({
    title: t('api_provider.new_provider'),
    componentName: 'ApiProviderModal',
    size: 'md',
    refreshData: table.value && table.value.refresh,
  })
}

function editProvider(id) {
  apiProviderStore.fetchProvider(id)
  modalStore.openModal({
    title: t('api_provider.edit_provider'),
    componentName: 'ApiProviderModal',
    size: 'md',
    data: id,
    refreshData: table.value && table.value.refresh,
  })
}

function removeProvider(id) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('api_provider.confirm_delete'),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'danger',
      hideNoButton: false,
      size: 'lg',
    })
    .then(async (res) => {
      if (res) {
        await apiProviderStore.deleteProvider(id)
        table.value && table.value.refresh()
      }
    })
}
</script>
