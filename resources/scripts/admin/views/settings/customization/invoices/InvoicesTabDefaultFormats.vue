<template>
  <form @submit.prevent="submitForm">
    <h6 class="text-gray-900 text-lg font-medium">
      {{ $t('settings.customization.invoices.default_formats') }}
    </h6>
    <p class="mt-1 text-sm text-gray-500 mb-2">
      {{ $t('settings.customization.invoices.default_formats_description') }}
    </p>

    <BaseInputGroup
      :label="$t('settings.customization.invoices.default_invoice_email_body')"
      class="mt-6 mb-4"
    >
      <BaseCustomInput
        v-model="formatSettings.invoice_mail_body"
        :fields="invoiceMailFields"
      />
    </BaseInputGroup>

    <BaseInputGroup
      :label="$t('settings.customization.invoices.company_address_format')"
      class="mt-6 mb-4"
    >
      <BaseCustomInput
        v-model="formatSettings.invoice_company_address_format"
        :fields="companyFields"
      />
    </BaseInputGroup>

    <BaseInputGroup
      :label="$t('settings.customization.invoices.shipping_address_format')"
      class="mt-6 mb-4"
    >
      <BaseCustomInput
        v-model="formatSettings.invoice_shipping_address_format"
        :fields="shippingFields"
      />
    </BaseInputGroup>

    <BaseInputGroup
      :label="$t('settings.customization.invoices.billing_address_format')"
      class="mt-6 mb-4"
    >
      <BaseCustomInput
        v-model="formatSettings.invoice_billing_address_format"
        :fields="billingFields"
      />
    </BaseInputGroup>

    <h6 class="text-gray-900 text-lg font-medium mt-10">
      {{ $t('settings.customization.invoices.pdf_label_customization') }}
    </h6>
    <p class="mt-1 text-sm text-gray-500 mb-6">
      {{ $t('settings.customization.invoices.pdf_label_customization_description') }}
    </p>

    <div class="space-y-8">
      <!-- General Labels -->
      <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 border-b pb-2">
          General Labels
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <BaseInputGroup :label="$t('settings.customization.invoices.invoice_label')">
            <BaseInput v-model="formatSettings.invoice_pdf_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.invoices.invoice_number_label')">
            <BaseInput v-model="formatSettings.invoice_pdf_number_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.invoices.invoice_status_label')">
            <BaseInput v-model="formatSettings.invoice_pdf_invoice_status_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.invoices.net_total_label')">
            <BaseInput v-model="formatSettings.invoice_pdf_net_total_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.invoices.amount_paid_label')">
            <BaseInput v-model="formatSettings.invoice_pdf_amount_paid_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.invoices.amount_due_label')">
            <BaseInput v-model="formatSettings.invoice_pdf_amount_due_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.invoices.invoice_date_label')">
            <BaseInput v-model="formatSettings.invoice_pdf_date_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.invoices.invoice_due_date_label')">
            <BaseInput v-model="formatSettings.invoice_pdf_due_date_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.invoices.bill_to_label')">
            <BaseInput v-model="formatSettings.invoice_pdf_bill_to_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.invoices.ship_to_label')">
            <BaseInput v-model="formatSettings.invoice_pdf_ship_to_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.invoices.notes_label')">
            <BaseInput v-model="formatSettings.invoice_pdf_notes_label" />
          </BaseInputGroup>
        </div>
      </div>

      <!-- Table Header Labels -->
      <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 border-b pb-2">
          Table Header Labels
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <BaseInputGroup :label="$t('settings.customization.invoices.items_label')">
            <BaseInput v-model="formatSettings.invoice_pdf_items_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.invoices.quantity_label')">
            <BaseInput v-model="formatSettings.invoice_pdf_quantity_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.invoices.price_label')">
            <BaseInput v-model="formatSettings.invoice_pdf_price_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.invoices.amount_label')">
            <BaseInput v-model="formatSettings.invoice_pdf_amount_label" />
          </BaseInputGroup>
        </div>
      </div>

      <!-- Summary & Total Labels -->
      <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 border-b pb-2">
          Summary & Total Labels
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <BaseInputGroup :label="$t('settings.customization.invoices.subtotal_label')">
            <BaseInput v-model="formatSettings.invoice_pdf_subtotal_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.invoices.discount_label')">
            <BaseInput v-model="formatSettings.invoice_pdf_discount_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.invoices.tax_label')">
            <BaseInput v-model="formatSettings.invoice_pdf_tax_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.invoices.total_label')">
            <BaseInput v-model="formatSettings.invoice_pdf_total_label" />
          </BaseInputGroup>
        </div>
      </div>
    </div>

    <BaseButton
      :loading="isSaving"
      :disabled="isSaving"
      variant="primary"
      type="submit"
      class="mt-4"
    >
      <template #left="slotProps">
        <BaseIcon v-if="!isSaving" :class="slotProps.class" name="ArrowDownOnSquareIcon" />
      </template>
      {{ $t('settings.customization.save') }}
    </BaseButton>
  </form>
</template>

<script setup>
import { ref, reactive, inject } from 'vue'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useI18n } from 'vue-i18n'

const companyStore = useCompanyStore()
const { t } = useI18n()
const utils = inject('utils')

const invoiceMailFields = ref([
  'customer',
  'customerCustom',
  'invoice',
  'invoiceCustom',
  'company',
])

const billingFields = ref([
  'billing',
  'customer',
  'customerCustom',
  'invoiceCustom',
])

const shippingFields = ref([
  'shipping',
  'customer',
  'customerCustom',
  'invoiceCustom',
])

const companyFields = ref(['company', 'invoiceCustom'])

let isSaving = ref(false)

const formatSettings = reactive({
  invoice_mail_body: null,
  invoice_company_address_format: null,
  invoice_shipping_address_format: null,
  invoice_billing_address_format: null,
  invoice_pdf_label: t('pdf_invoice_label'),
  invoice_pdf_number_label: t('pdf_invoice_number'),
  invoice_pdf_invoice_status_label: t('pdf_invoice_status'),
  invoice_pdf_net_total_label: t('pdf_net_total'),
  invoice_pdf_date_label: t('pdf_invoice_date'),
  invoice_pdf_due_date_label: t('pdf_invoice_due_date'),
  invoice_pdf_bill_to_label: t('pdf_bill_to'),
  invoice_pdf_ship_to_label: t('pdf_ship_to'),
  invoice_pdf_subtotal_label: t('pdf_subtotal'),
  invoice_pdf_discount_label: t('pdf_discount_label'),
  invoice_pdf_tax_label: t('pdf_tax_label'),
  invoice_pdf_total_label: t('pdf_total'),
  invoice_pdf_amount_paid_label: t('pdf_amount_paid'),
  invoice_pdf_amount_due_label: t('pdf_amount_due'),
  invoice_pdf_items_label: t('pdf_items_label'),
  invoice_pdf_quantity_label: t('pdf_quantity_label'),
  invoice_pdf_price_label: t('pdf_price_label'),
  invoice_pdf_amount_label: t('pdf_amount_label'),
  invoice_pdf_notes_label: t('pdf_notes'),
})

utils.mergeSettings(formatSettings, {
  ...companyStore.selectedCompanySettings,
})

async function submitForm() {
  isSaving.value = true

  let data = {
    settings: {
      ...formatSettings,
    },
  }

  await companyStore.updateCompanySettings({
    data,
    message: 'settings.customization.invoices.invoice_settings_updated',
  })

  isSaving.value = false

  return true
}
</script>
