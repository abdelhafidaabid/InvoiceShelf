<template>
  <form @submit.prevent="submitForm">
    <h6 class="text-gray-900 text-lg font-medium">
      {{ $t('settings.customization.payments.default_formats') }}
    </h6>
    <p class="mt-1 text-sm text-gray-500 mb-2">
      {{ $t('settings.customization.payments.default_formats_description') }}
    </p>

    <BaseInputGroup
      :label="$t('settings.customization.payments.default_payment_email_body')"
      class="mt-6 mb-4"
    >
      <BaseCustomInput
        v-model="formatSettings.payment_mail_body"
        :fields="mailFields"
      />
    </BaseInputGroup>

    <BaseInputGroup
      :label="$t('settings.customization.payments.company_address_format')"
      class="mt-6 mb-4"
    >
      <BaseCustomInput
        v-model="formatSettings.payment_company_address_format"
        :fields="companyFields"
      />
    </BaseInputGroup>

    <BaseInputGroup
      :label="
        $t('settings.customization.payments.from_customer_address_format')
      "
      class="mt-6 mb-4"
    >
      <BaseCustomInput
        v-model="formatSettings.payment_from_customer_address_format"
        :fields="customerAddressFields"
      />
    </BaseInputGroup>

    <h6 class="text-gray-900 text-lg font-medium mt-10">
      {{ $t('settings.customization.payments.pdf_label_customization') }}
    </h6>
    <p class="mt-1 text-sm text-gray-500 mb-6">
      {{ $t('settings.customization.payments.pdf_label_customization_description') }}
    </p>

    <div class="space-y-8">
      <!-- General Labels -->
      <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 border-b pb-2">
          General Labels
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <BaseInputGroup :label="$t('settings.customization.payments.payment_label')">
            <BaseInput v-model="formatSettings.payment_pdf_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.payments.payment_number_label')">
            <BaseInput v-model="formatSettings.payment_pdf_number_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.payments.payment_date_label')">
            <BaseInput v-model="formatSettings.payment_pdf_date_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.payments.payment_amount_label')">
            <BaseInput v-model="formatSettings.payment_pdf_amount_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.payments.payment_mode_label')">
            <BaseInput v-model="formatSettings.payment_pdf_mode_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.payments.received_from_label')">
            <BaseInput v-model="formatSettings.payment_pdf_received_from_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.payments.balance_due_label')">
            <BaseInput v-model="formatSettings.payment_pdf_balance_due_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.payments.invoice_status_label')">
            <BaseInput v-model="formatSettings.payment_pdf_invoice_status_label" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.customization.payments.notes_label')">
            <BaseInput v-model="formatSettings.payment_pdf_notes_label" />
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

const mailFields = ref([
  'customer',
  'customerCustom',
  'company',
  'payment',
  'paymentCustom',
])

const customerAddressFields = ref([
  'billing',
  'customer',
  'customerCustom',
  'paymentCustom',
])

const companyFields = ref(['company', 'paymentCustom'])

let isSaving = ref(false)

const formatSettings = reactive({
  payment_mail_body: null,
  payment_company_address_format: null,
  payment_from_customer_address_format: null,
  payment_pdf_label: t('pdf_payment_receipt_label'),
  payment_pdf_number_label: t('pdf_payment_number'),
  payment_pdf_date_label: t('pdf_payment_date'),
  payment_pdf_amount_label: t('pdf_payment_amount_received_label'),
  payment_pdf_mode_label: t('pdf_payment_mode'),
  payment_pdf_received_from_label: t('pdf_received_from'),
  payment_pdf_balance_due_label: 'Balance Due',
  payment_pdf_invoice_status_label: 'Invoice Status',
  payment_pdf_notes_label: t('pdf_notes'),
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
    message: 'settings.customization.payments.payment_settings_updated',
  })

  isSaving.value = false

  return true
}
</script>
