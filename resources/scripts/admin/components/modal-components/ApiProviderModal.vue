<template>
  <BaseModal
    :show="modalActive"
    @close="closeModal"
  >
    <template #header>
      <div class="flex justify-between w-full">
        {{ modalStore.title }}

        <BaseIcon
          name="XMarkIcon"
          class="w-6 h-6 text-gray-500 cursor-pointer"
          @click="closeModal"
        />
      </div>
    </template>

    <form @submit.prevent="submit">
      <div class="px-4 md:px-8 py-8 overflow-y-auto sm:p-6">
        <BaseInputGrid layout="one-column">
          <BaseInputGroup
            :label="$t('general.name')"
            required
            :error="v$.provider.name.$error && v$.provider.name.$errors[0].$message"
          >
            <BaseInput
              v-model="apiProviderStore.provider.name"
              type="text"
              name="name"
              :invalid="v$.provider.name.$error"
            />
          </BaseInputGroup>

          <BaseInputGroup
            :label="$t('api_provider.driver')"
            required
            :error="v$.provider.driver.$error && v$.provider.driver.$errors[0].$message"
          >
            <BaseMultiselect
              v-model="apiProviderStore.provider.driver"
              :options="driverOptions"
              value-prop="value"
              label="label"
              track-by="value"
              :invalid="v$.provider.driver.$error"
            />
          </BaseInputGroup>

          <BaseInputGroup
            :label="$t('api_provider.key')"
            :error="v$.provider.key.$error && v$.provider.key.$errors[0].$message"
          >
            <BaseInput
              v-model="apiProviderStore.provider.key"
              type="text"
              name="key"
              :placeholder="$t('api_provider.primary_key_placeholder')"
              :invalid="v$.provider.key.$error"
            />
          </BaseInputGroup>

          <div v-if="apiProviderStore.provider.driver === 'number2words'" class="space-y-4">
            <div class="flex items-center justify-between">
              <label class="block text-sm font-medium text-gray-700">
                {{ $t('api_provider.backup_keys') }}
              </label>
              <BaseButton
                variant="primary-outline"
                size="sm"
                type="button"
                @click="addBackupKey"
              >
                <template #left="slotProps">
                  <BaseIcon name="PlusIcon" :class="slotProps.class" />
                </template>
                {{ $t('api_provider.add_key') }}
              </BaseButton>
            </div>

            <div
              v-for="(key, index) in apiProviderStore.provider.config.keys"
              :key="index"
              class="flex items-center space-x-2"
            >
              <div class="flex-1">
                <BaseInput
                  v-model="apiProviderStore.provider.config.keys[index]"
                  type="text"
                  :placeholder="$t('api_provider.backup_key_placeholder')"
                />
              </div>
              <BaseButton
                variant="danger"
                size="sm"
                type="button"
                @click="removeBackupKey(index)"
              >
                <BaseIcon name="TrashIcon" class="w-4 h-4" />
              </BaseButton>
            </div>
          </div>

          <BaseInputGroup
            :label="$t('api_provider.host')"
            :error="v$.provider.host.$error && v$.provider.host.$errors[0].$message"
          >
            <BaseInput
              v-model="apiProviderStore.provider.host"
              type="text"
              name="host"
              :invalid="v$.provider.host.$error"
            />
          </BaseInputGroup>

          <BaseSwitch
            v-model="apiProviderStore.provider.active"
            class="flex"
            :label-right="$t('api_provider.active')"
          />
        </BaseInputGrid>
      </div>
      <div class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid">
        <BaseButton
          class="mr-3"
          variant="primary-outline"
          type="button"
          :loading="isTesting"
          :disabled="isSaving || isTesting"
          @click="testConnection"
        >
          <template #left="slotProps">
            <BaseIcon
              v-if="!isTesting"
              name="BeakerIcon"
              :class="slotProps.class"
            />
          </template>
          {{ $t('api_provider.test') }}
        </BaseButton>
        <BaseButton
          class="mr-3"
          variant="primary-outline"
          type="button"
          :disabled="isSaving || isTesting"
          @click="closeModal"
        >
          {{ $t('general.cancel') }}
        </BaseButton>
        <BaseButton
          :loading="isSaving"
          :disabled="isSaving"
          variant="primary"
          type="submit"
        >
          <template #left="slotProps">
            <BaseIcon
              v-if="!isSaving"
              name="ArrowDownOnSquareIcon"
              :class="slotProps.class"
            />
          </template>
          {{ isEdit ? $t('general.update') : $t('general.save') }}
        </BaseButton>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useApiProviderStore } from '@/scripts/admin/stores/api-provider'
import { useModalStore } from '@/scripts/stores/modal'
import useVuelidate from '@vuelidate/core'
import { required, helpers } from '@vuelidate/validators'
import { useI18n } from 'vue-i18n'
import { useNotificationStore } from '@/scripts/stores/notification'

const { t } = useI18n()
const apiProviderStore = useApiProviderStore()
const modalStore = useModalStore()
const notificationStore = useNotificationStore()

const isSaving = ref(false)
const isTesting = ref(false)

const driverOptions = [
  { value: 'number2words', label: 'Number to Words (RapidAPI)' },
]

const isEdit = computed(() => !!apiProviderStore.provider.id)

const rules = {
  provider: {
    name: { required: helpers.withMessage(t('validation.required'), required) },
    driver: { required: helpers.withMessage(t('validation.required'), required) },
    key: {},
    host: {},
    active: {},
  },
}

const v$ = useVuelidate(rules, apiProviderStore)

const modalActive = computed(() => {
  return modalStore.active && modalStore.componentName === 'ApiProviderModal'
})

async function submit() {
  v$.value.$touch()
  if (v$.value.$invalid) return

  isSaving.value = true

  // Ensure config.keys is initialized
  if (!apiProviderStore.provider.config || typeof apiProviderStore.provider.config !== 'object') {
    apiProviderStore.provider.config = { keys: [] }
  }
  if (!Array.isArray(apiProviderStore.provider.config.keys)) {
    apiProviderStore.provider.config.keys = []
  }

  const action = isEdit.value ? apiProviderStore.updateProvider : apiProviderStore.addProvider

  // Clean up keys before saving: remove empty strings and duplicates
  if (apiProviderStore.provider.config && Array.isArray(apiProviderStore.provider.config.keys)) {
    apiProviderStore.provider.config.keys = apiProviderStore.provider.config.keys
      .map((k) => k.trim())
      .filter((k) => k !== '')
    apiProviderStore.provider.config.keys = [...new Set(apiProviderStore.provider.config.keys)]
  }

  try {
    // Use a clean object to avoid any reactivity-related serialization issues
    const payload = JSON.parse(JSON.stringify(apiProviderStore.provider))
    await action(payload)
    modalStore.refreshData ? modalStore.refreshData() : ''
    closeModal()
  } catch (error) {
    console.error(error)
  } finally {
    isSaving.value = false
  }
}

async function testConnection() {
  v$.value.$touch()
  // We only need driver, key, host for testing
  if (v$.value.provider.driver.$error || v$.value.provider.key.$error || v$.value.provider.host.$error) {
    return
  }

  isTesting.value = true

  try {
    const response = await apiProviderStore.testProvider(apiProviderStore.provider)
    if (response.data.success) {
      notificationStore.showNotification({
        type: 'success',
        message: t('api_provider.test_success') + ': ' + response.data.data,
      })
    } else {
      notificationStore.showNotification({
        type: 'error',
        message: t('api_provider.test_fail') + ': ' + response.data.message,
      })
    }
  } catch (error) {
    notificationStore.showNotification({
      type: 'error',
      message: t('api_provider.test_fail'),
    })
  } finally {
    isTesting.value = false
  }
}

function addBackupKey() {
  if (!apiProviderStore.provider.config || typeof apiProviderStore.provider.config !== 'object') {
    apiProviderStore.provider.config = { keys: [] }
  }

  if (!Array.isArray(apiProviderStore.provider.config.keys)) {
    apiProviderStore.provider.config.keys = []
  }

  apiProviderStore.provider.config.keys.push('')
}

function removeBackupKey(index) {
  if (Array.isArray(apiProviderStore.provider.config.keys)) {
    apiProviderStore.provider.config.keys.splice(index, 1)
  }
}

function closeModal() {
  modalStore.closeModal()
  setTimeout(() => {
    apiProviderStore.provider = {
      name: '',
      driver: '',
      key: '',
      host: '',
      active: true,
      config: {
        keys: [],
      },
    }
    v$.value.$reset()
  }, 300)
}
</script>
