<template>
  <form @submit.prevent="updateCompanyData">
    <BaseSettingCard
      :title="$t('settings.company_info.company_info')"
      :description="$t('settings.company_info.section_description')"
    >
      <div>
        <h4 class="text-sm font-semibold text-gray-900 mb-4">{{ $t('settings.company_info.branding') }}</h4>
        <BaseInputGrid>
        <BaseInputGroup :label="$t('settings.company_info.company_logo')">
          <BaseFileUploader
            v-model="previewLogo"
            base64
            @change="onFileInputChange"
            @remove="onFileInputRemove"
          />
        </BaseInputGroup>

        <BaseInputGroup :label="$t('settings.company_info.company_stamp')">
          <BaseFileUploader
            v-model="previewStamp"
            base64
            @change="onStampInputChange"
            @remove="onStampInputRemove"
          />
        </BaseInputGroup>
      </BaseInputGrid>
      </div>

      <div class="mt-8">
        <h4 class="text-sm font-semibold text-gray-900 mb-4">{{ $t('settings.company_info.company_info') }}</h4>
        <BaseInputGrid>
          <BaseInputGroup
            :label="$t('settings.company_info.company_name')"
            :error="v$.name.$error && v$.name.$errors[0].$message"
            required
          >
            <BaseInput
              v-model="companyForm.name"
              :invalid="v$.name.$error"
              @blur="v$.name.$touch()"
            />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.company_info.email')">
            <BaseInput v-model="companyForm.address.email" type="email" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.company_info.website')">
            <BaseInput v-model="companyForm.address.website" />
          </BaseInputGroup>
        </BaseInputGrid>
      </div>

      <BaseDivider class="my-8" />

      <div>
        <h4 class="text-sm font-semibold text-gray-900 mb-4">{{ $t('settings.company_info.address') }}</h4>
        <BaseInputGrid>
          <BaseInputGroup
            :label="$t('settings.company_info.country')"
            :error="
              v$.address.country_id.$error &&
              v$.address.country_id.$errors[0].$message
            "
            required
          >
            <BaseMultiselect
              v-model="companyForm.address.country_id"
              label="name"
              :invalid="v$.address.country_id.$error"
              :options="globalStore.countries"
              value-prop="id"
              :can-deselect="true"
              :can-clear="false"
              searchable
              track-by="name"
            />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.company_info.state')">
            <BaseInput
              v-model="companyForm.address.state"
              name="state"
              type="text"
            />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.company_info.city')">
            <BaseInput v-model="companyForm.address.city" type="text" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.company_info.zip')">
            <BaseInput v-model="companyForm.address.zip" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.company_info.phone')">
            <BaseInput v-model="companyForm.address.phone" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.company_info.fax')">
            <BaseInput v-model="companyForm.address.fax" />
          </BaseInputGroup>

          <div class="col-span-full">
            <BaseInputGroup :label="$t('settings.company_info.address')">
              <BaseTextarea
                v-model="companyForm.address.address_street_1"
                rows="2"
                class="mb-2"
              />
              <BaseTextarea
                v-model="companyForm.address.address_street_2"
                rows="2"
              />
            </BaseInputGroup>
          </div>
        </BaseInputGrid>
      </div>

      <BaseDivider class="my-8" />

      <div>
        <h4 class="text-sm font-semibold text-gray-900 mb-4">{{ $t('settings.company_info.legal_identifiers') }}</h4>
        <BaseInputGrid>
          <BaseInputGroup :label="$t('settings.company_info.tax_id')">
            <BaseInput v-model="companyForm.tax_id" type="text" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.company_info.vat_id')">
            <BaseInput v-model="companyForm.vat_id" type="text" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.company_info.patente')">
            <BaseInput v-model="companyForm.patente" type="text" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.company_info.cnss')">
            <BaseInput v-model="companyForm.cnss" type="text" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.company_info.rc')">
            <BaseInput v-model="companyForm.rc" type="text" />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.company_info.ice')">
            <BaseInput v-model="companyForm.ice" type="text" />
          </BaseInputGroup>
        </BaseInputGrid>
      </div>

      <BaseDivider class="my-8" />

      <div>
        <h4 class="text-sm font-semibold text-gray-900 mb-4">{{ $t('settings.company_info.pdf_appearance') }}</h4>
        <BaseInputGrid>
          <BaseInputGroup :label="$t('settings.company_info.pdf_main_color')">
            <div class="flex items-center space-x-3">
              <input
                v-model="companyForm.pdf_main_color"
                type="color"
                class="h-10 w-20 cursor-pointer rounded-md border border-gray-300 p-1 focus:outline-none focus:ring-2 focus:ring-primary-500"
              />
              <span class="text-sm text-gray-500 uppercase">{{ companyForm.pdf_main_color }}</span>
            </div>
          </BaseInputGroup>

          <BaseInputGroup :label="$t('settings.company_info.pdf_secondary_color')">
            <div class="flex items-center space-x-3">
              <input
                v-model="companyForm.pdf_secondary_color"
                type="color"
                class="h-10 w-20 cursor-pointer rounded-md border border-gray-300 p-1 focus:outline-none focus:ring-2 focus:ring-primary-500"
              />
              <span class="text-sm text-gray-500 uppercase">{{ companyForm.pdf_secondary_color }}</span>
            </div>
          </BaseInputGroup>
        </BaseInputGrid>
      </div>

      <BaseButton
        :loading="isSaving"
        :disabled="isSaving"
        type="submit"
        class="mt-6"
      >
        <template #left="slotProps">
          <BaseIcon v-if="!isSaving" :class="slotProps.class" name="ArrowDownOnSquareIcon" />
        </template>
        {{ $t('settings.company_info.save') }}
      </BaseButton>

      <div v-if="companyStore.companies.length !== 1" class="py-5">
        <BaseDivider class="my-4" />
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          {{ $t('settings.company_info.delete_company') }}
        </h3>
        <div class="mt-2 max-w-xl text-sm text-gray-500">
          <p>
            {{ $t('settings.company_info.delete_company_description') }}
          </p>
        </div>
        <div class="mt-5">
          <button
            type="button"
            class="inline-flex items-center justify-center px-4 py-2 border border-transparent font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm"
            @click="removeCompany"
          >
            {{ $t('general.delete') }}
          </button>
        </div>
      </div>
    </BaseSettingCard>
  </form>
  <DeleteCompanyModal />
</template>

<script setup>
import { reactive, ref, inject, computed } from 'vue'
import { useGlobalStore } from '@/scripts/admin/stores/global'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useI18n } from 'vue-i18n'
import { required, minLength, helpers } from '@vuelidate/validators'
import { useVuelidate } from '@vuelidate/core'
import { useModalStore } from '@/scripts/stores/modal'
import DeleteCompanyModal from '@/scripts/admin/components/modal-components/DeleteCompanyModal.vue'

const companyStore = useCompanyStore()
const globalStore = useGlobalStore()
const modalStore = useModalStore()
const { t } = useI18n()
const utils = inject('utils')

let isSaving = ref(false)

const companyForm = reactive({
  name: null,
  logo: null,
  stamp: null,
  tax_id: null,
  patente: null,
  cnss: null,
  rc: null,
  ice: null,
  vat_id: null,
  pdf_main_color: '#000000',
  pdf_secondary_color: '#000000',
  address: {
    address_street_1: '',
    address_street_2: '',
    email: '',
    website: '',
    country_id: null,
    state: '',
    city: '',
    phone: '',
    fax: '',
    zip: '',
  },
})

utils.mergeSettings(companyForm, {
  ...companyStore.selectedCompany,
})

let previewLogo = ref([])
let logoFileBlob = ref(null)
let logoFileName = ref(null)
const isCompanyLogoRemoved = ref(false)

let previewStamp = ref([])
let stampFileBlob = ref(null)
let stampFileName = ref(null)
const isCompanyStampRemoved = ref(false)

if (companyForm.logo) {
  previewLogo.value.push({
    image: companyForm.logo,
  })
}

if (companyForm.stamp) {
  previewStamp.value.push({
    image: companyForm.stamp,
  })
}

const rules = computed(() => {
  return {
    name: {
      required: helpers.withMessage(t('validation.required'), required),
      minLength: helpers.withMessage(
        t('validation.name_min_length'),
        minLength(3),
      ),
    },
    address: {
      country_id: {
        required: helpers.withMessage(t('validation.required'), required),
      },
    },
  }
})

const v$ = useVuelidate(
  rules,
  computed(() => companyForm),
)

globalStore.fetchCountries()

function onFileInputChange(fileName, file, fileCount, fileList) {
  logoFileName.value = fileList.name
  logoFileBlob.value = file
}

function onFileInputRemove() {
  logoFileBlob.value = null
  isCompanyLogoRemoved.value = true
}

function onStampInputChange(fileName, file, fileCount, fileList) {
  stampFileName.value = fileList.name
  stampFileBlob.value = file
}

function onStampInputRemove() {
  stampFileBlob.value = null
  isCompanyStampRemoved.value = true
}

async function updateCompanyData() {
  v$.value.$touch()

  if (v$.value.$invalid) {
    return true
  }

  isSaving.value = true

  const res = await companyStore.updateCompany(companyForm)

  if (res.data.data) {
    if (logoFileBlob.value || isCompanyLogoRemoved.value) {
      let logoData = new FormData()

      if (logoFileBlob.value) {
        logoData.append(
          'company_logo',
          JSON.stringify({
            name: logoFileName.value,
            data: logoFileBlob.value,
          }),
        )
      }
      logoData.append('is_company_logo_removed', isCompanyLogoRemoved.value)

      await companyStore.updateCompanyLogo(logoData)
      logoFileBlob.value = null
      isCompanyLogoRemoved.value = false
    }

    if (stampFileBlob.value || isCompanyStampRemoved.value) {
      let stampData = new FormData()

      if (stampFileBlob.value) {
        stampData.append(
          'company_stamp',
          JSON.stringify({
            name: stampFileName.value,
            data: stampFileBlob.value,
          }),
        )
      }
      stampData.append('is_company_stamp_removed', isCompanyStampRemoved.value)

      await companyStore.updateCompanyStamp(stampData)
      stampFileBlob.value = null
      isCompanyStampRemoved.value = false
    }

    isSaving.value = false
  }
  isSaving.value = false
}
function removeCompany(id) {
  modalStore.openModal({
    title: t('settings.company_info.are_you_absolutely_sure'),
    componentName: 'DeleteCompanyModal',
    size: 'sm',
  })
}
</script>
