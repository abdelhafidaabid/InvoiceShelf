<template>
  <InvoicesTabInvoiceNumber />

  <BaseDivider class="my-8" />

  <InvoicesTabDueDate />

  <BaseDivider class="my-8" />

  <InvoicesTabRetrospective />

  <BaseDivider class="my-8" />

  <InvoicesTabDefaultFormats />

  <BaseDivider class="my-8" />

  <PDFFontCustomizer type="invoice" />

  <BaseDivider class="mt-6 mb-2" />

  <ul class="divide-y divide-gray-200">
    <BaseSwitchSection
      v-model="sendAsAttachmentField"
      :title="$t('settings.customization.invoices.invoice_email_attachment')"
      :description="
        $t(
          'settings.customization.invoices.invoice_email_attachment_setting_description'
        )
      "
    />
    <BaseSwitchSection
      v-model="showSignature"
      :title="$t('settings.customization.invoices.invoice_show_signature')"
      :description="
        $t('settings.customization.invoices.invoice_show_signature_description')
      "
      class="mt-6"
    />
    <BaseSwitchSection
      v-model="showPageNumber"
      :title="$t('settings.customization.invoices.invoice_show_page_number')"
      :description="
        $t('settings.customization.invoices.invoice_show_page_number_description')
      "
      class="mt-6"
    />
  </ul>
</template>

<script setup>
import { computed, reactive, inject, watch } from 'vue'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import InvoicesTabInvoiceNumber from './InvoicesTabInvoiceNumber.vue'
import InvoicesTabRetrospective from './InvoicesTabRetrospective.vue'
import InvoicesTabDueDate from './InvoicesTabDueDate.vue'
import InvoicesTabDefaultFormats from './InvoicesTabDefaultFormats.vue'
import PDFFontCustomizer from '../PDFFontCustomizer.vue'

const utils = inject('utils')
const companyStore = useCompanyStore()

const invoiceSettings = reactive({
  invoice_email_attachment: null,
  invoice_show_signature: null,
  invoice_show_page_number: null,
})

watch(
  () => companyStore.selectedCompanySettings,
  (newSettings) => {
    utils.mergeSettings(invoiceSettings, newSettings)
  },
  { immediate: true, deep: true }
)

const sendAsAttachmentField = computed({
  get: () => {
    return invoiceSettings.invoice_email_attachment !== 'NO'
  },
  set: async (newValue) => {
    const value = newValue ? 'YES' : 'NO'

    let data = {
      settings: {
        invoice_email_attachment: value,
      },
    }

    invoiceSettings.invoice_email_attachment = value

    await companyStore.updateCompanySettings({
      data,
      message: 'general.setting_updated',
    })
  },
})

const showSignature = computed({
  get: () => {
    return invoiceSettings.invoice_show_signature === 'YES'
  },
  set: async (newValue) => {
    const value = newValue ? 'YES' : 'NO'

    let data = {
      settings: {
        invoice_show_signature: value,
      },
    }

    invoiceSettings.invoice_show_signature = value

    await companyStore.updateCompanySettings({
      data,
      message: 'general.setting_updated',
    })
  },
})

const showPageNumber = computed({
  get: () => {
    return invoiceSettings.invoice_show_page_number === 'YES'
  },
  set: async (newValue) => {
    const value = newValue ? 'YES' : 'NO'

    let data = {
      settings: {
        invoice_show_page_number: value,
      },
    }

    invoiceSettings.invoice_show_page_number = value

    await companyStore.updateCompanySettings({
      data,
      message: 'general.setting_updated',
    })
  },
})
</script>
