<template>
  <form @submit.prevent="submitForm">
    <h6 class="text-gray-900 text-lg font-medium">
      {{ $t('settings.customization.estimates.default_formats') }}
    </h6>
    <p class="mt-1 text-sm text-gray-500 mb-2">
      {{ $t('settings.customization.estimates.default_formats_description') }}
    </p>

    <BaseInputGroup
      :label="
        $t('settings.customization.estimates.default_estimate_email_body')
      "
      class="mt-6 mb-4"
    >
      <BaseCustomInput
        v-model="formatSettings.estimate_mail_body"
        :fields="estimateMailFields"
      />
    </BaseInputGroup>

    <BaseInputGroup
      :label="$t('settings.customization.estimates.company_address_format')"
      class="mt-6 mb-4"
    >
      <BaseCustomInput
        v-model="formatSettings.estimate_company_address_format"
        :fields="companyFields"
      />
    </BaseInputGroup>

    <BaseInputGroup
      :label="$t('settings.customization.estimates.shipping_address_format')"
      class="mt-6 mb-4"
    >
      <BaseCustomInput
        v-model="formatSettings.estimate_shipping_address_format"
        :fields="shippingFields"
      />
    </BaseInputGroup>

    <BaseInputGroup
      :label="$t('settings.customization.estimates.billing_address_format')"
      class="mt-6 mb-4"
    >
      <BaseCustomInput
        v-model="formatSettings.estimate_billing_address_format"
        :fields="billingFields"
      />
    </BaseInputGroup>

    <h6 class="text-gray-900 text-lg font-medium mt-10">
      {{ $t('settings.customization.estimates.pdf_label_customization') }}
    </h6>
    <p class="mt-1 text-sm text-gray-500 mb-6">
      {{ $t('settings.customization.estimates.pdf_label_customization_description') }}
    </p>

    <div class="space-y-8">
      <!-- General Labels -->
      <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 border-b pb-2">
          General Labels
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <BaseInputGroup :label="$t('settings.customization.estimates.estimate_label')">
            <BaseInput v-model="formatSettings.estimate_pdf_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.estimates.estimate_number_label')">
            <BaseInput v-model="formatSettings.estimate_pdf_number_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.estimates.estimate_date_label')">
            <BaseInput v-model="formatSettings.estimate_pdf_date_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.estimates.estimate_expiry_date_label')">
            <BaseInput v-model="formatSettings.estimate_pdf_expiry_date_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.estimates.bill_to_label')">
            <BaseInput v-model="formatSettings.estimate_pdf_bill_to_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.estimates.ship_to_label')">
            <BaseInput v-model="formatSettings.estimate_pdf_ship_to_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.estimates.notes_label')">
            <BaseInput v-model="formatSettings.estimate_pdf_notes_label" />
          </BaseInputGroup>
        </div>
      </div>

      <!-- Table Header Labels -->
      <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 border-b pb-2">
          Table Header Labels
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <BaseInputGroup :label="$t('settings.customization.estimates.items_label')">
            <BaseInput v-model="formatSettings.estimate_pdf_items_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.estimates.quantity_label')">
            <BaseInput v-model="formatSettings.estimate_pdf_quantity_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.estimates.price_label')">
            <BaseInput v-model="formatSettings.estimate_pdf_price_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.estimates.amount_label')">
            <BaseInput v-model="formatSettings.estimate_pdf_amount_label" />
          </BaseInputGroup>
        </div>
      </div>

      <!-- Summary & Total Labels -->
      <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 border-b pb-2">
          Summary & Total Labels
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <BaseInputGroup :label="$t('settings.customization.estimates.subtotal_label')">
            <BaseInput v-model="formatSettings.estimate_pdf_subtotal_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.estimates.discount_label')">
            <BaseInput v-model="formatSettings.estimate_pdf_discount_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.estimates.tax_label')">
            <BaseInput v-model="formatSettings.estimate_pdf_tax_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.estimates.total_label')">
            <BaseInput v-model="formatSettings.estimate_pdf_total_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.estimates.net_total_label')">
            <BaseInput v-model="formatSettings.estimate_pdf_net_total_label" />
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

const estimateMailFields = ref([
  'customer',
  'customerCustom',
  'estimate',
  'estimateCustom',
  'company',
])

const billingFields = ref([
  'billing',
  'customer',
  'customerCustom',
  'estimateCustom',
])

const shippingFields = ref([
  'shipping',
  'customer',
  'customerCustom',
  'estimateCustom',
])

const companyFields = ref(['company', 'estimateCustom'])

let isSaving = ref(false)

const formatSettings = reactive({
  estimate_mail_body: null,
  estimate_company_address_format: null,
  estimate_shipping_address_format: null,
  estimate_billing_address_format: null,
  estimate_pdf_label: t('pdf_estimate_label'),
  estimate_pdf_number_label: t('pdf_estimate_number'),
  estimate_pdf_date_label: t('pdf_estimate_date'),
  estimate_pdf_expiry_date_label: t('pdf_estimate_expire_date'),
  estimate_pdf_bill_to_label: t('pdf_bill_to'),
  estimate_pdf_ship_to_label: t('pdf_ship_to'),
  estimate_pdf_subtotal_label: t('pdf_subtotal'),
  estimate_pdf_discount_label: t('pdf_discount_label'),
  estimate_pdf_tax_label: t('pdf_tax_label'),
  estimate_pdf_total_label: t('pdf_total'),
  estimate_pdf_items_label: t('pdf_items_label'),
  estimate_pdf_quantity_label: t('pdf_quantity_label'),
  estimate_pdf_price_label: t('pdf_price_label'),
  estimate_pdf_amount_label: t('pdf_amount_label'),
  estimate_pdf_notes_label: t('pdf_notes'),
  estimate_pdf_net_total_label: t('pdf_net_total'),
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
    message: 'settings.customization.estimates.estimate_settings_updated',
  })

  isSaving.value = false

  return true
}
</script>
