<template>
  <EstimatesTabEstimateNumber />

  <BaseDivider class="my-8" />

  <EstimatesTabExpiryDate />

  <BaseDivider class="my-8" />

  <EstimatesTabConvertEstimate />

  <BaseDivider class="my-8" />

  <EstimatesTabDefaultFormats />

  <BaseDivider class="my-8" />

  <PDFFontCustomizer type="estimate" />

  <BaseDivider class="mt-6 mb-2" />

  <ul class="divide-y divide-gray-200">
    <BaseSwitchSection
      v-model="sendAsAttachmentField"
      :title="$t('settings.customization.estimates.estimate_email_attachment')"
      :description="
        $t(
          'settings.customization.estimates.estimate_email_attachment_setting_description'
        )
      "
    />
    <BaseSwitchSection
      v-model="estimateShowSignatureField"
      :title="$t('general.show_signature_and_stamp')"
      :description="
        $t(
          'settings.customization.estimates.estimate_show_signature_description'
        )
      "
    />
    <BaseSwitchSection
      v-model="estimateShowPageNumberField"
      :title="$t('general.show_page_number')"
      :description="
        $t(
          'settings.customization.estimates.estimate_show_page_number_description'
        )
      "
    />
  </ul>
</template>

<script setup>
import { computed, reactive, inject } from 'vue'
import { useCompanyStore } from '@/scripts/admin/stores/company'

import EstimatesTabEstimateNumber from './EstimatesTabEstimateNumber.vue'
import EstimatesTabExpiryDate from './EstimatesTabExpiryDate.vue'
import EstimatesTabDefaultFormats from './EstimatesTabDefaultFormats.vue'
import EstimatesTabConvertEstimate from './EstimatesTabConvertEstimate.vue'
import PDFFontCustomizer from '../PDFFontCustomizer.vue'

const utils = inject('utils')

const companyStore = useCompanyStore()

const estimateSettings = reactive({
  estimate_email_attachment: null,
  estimate_show_signature: null,
  estimate_show_page_number: null,
})

utils.mergeSettings(estimateSettings, {
  ...companyStore.selectedCompanySettings,
})

const sendAsAttachmentField = computed({
  get: () => {
    return estimateSettings.estimate_email_attachment === 'YES'
  },
  set: async (newValue) => {
    const value = newValue ? 'YES' : 'NO'

    let data = {
      settings: {
        estimate_email_attachment: value,
      },
    }

    estimateSettings.estimate_email_attachment = value

    await companyStore.updateCompanySettings({
      data,
      message: 'general.setting_updated',
    })
  },
})

const estimateShowSignatureField = computed({
  get: () => {
    return estimateSettings.estimate_show_signature === 'YES'
  },
  set: async (newValue) => {
    const value = newValue ? 'YES' : 'NO'

    let data = {
      settings: {
        estimate_show_signature: value,
      },
    }

    estimateSettings.estimate_show_signature = value

    await companyStore.updateCompanySettings({
      data,
      message: 'general.setting_updated',
    })
  },
})

const estimateShowPageNumberField = computed({
  get: () => {
    return estimateSettings.estimate_show_page_number === 'YES'
  },
  set: async (newValue) => {
    const value = newValue ? 'YES' : 'NO'

    let data = {
      settings: {
        estimate_show_page_number: value,
      },
    }

    estimateSettings.estimate_show_page_number = value

    await companyStore.updateCompanySettings({
      data,
      message: 'general.setting_updated',
    })
  },
})
</script>
