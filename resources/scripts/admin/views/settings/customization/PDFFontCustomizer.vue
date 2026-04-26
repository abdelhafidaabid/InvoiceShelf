<template>
  <div class="mt-6">
    <BaseInputGroup
      :label="$t('settings.customization.pdf_font')"
      :description="$t('settings.customization.pdf_font_description')"
    >
      <BaseSelectInput
        v-model="selectedFont"
        :options="fonts"
        :placeholder="$t('settings.customization.select_font')"
        label-key="label"
        value-prop="value"
      />
    </BaseInputGroup>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import http from '@/scripts/http'
import { useCompanyStore } from '@/scripts/admin/stores/company'

const props = defineProps({
  type: {
    type: String,
    required: true,
  },
})

const companyStore = useCompanyStore()
const fonts = ref([])
const settingKey = `${props.type}_pdf_font`
const selectedFont = ref(companyStore.selectedCompanySettings[settingKey] || 'DejaVu Sans')

onMounted(async () => {
  try {
    const response = await http.get('/api/v1/pdf/fonts')
    fonts.value = response.data.map((font) => ({
      id: font,
      label: font,
      value: font,
    }))

    // Re-assign to trigger resolution in BaseSelectInput now that fonts are loaded
    const currentSetting = companyStore.selectedCompanySettings[settingKey]
    if (currentSetting) {
      selectedFont.value = currentSetting
    }
  } catch (error) {
    console.error('Failed to fetch fonts', error)
  }
})

watch(selectedFont, async (newValue) => {
  if (!newValue) {
    return
  }

  if (companyStore.selectedCompanySettings[settingKey] === newValue) {
    return
  }

  let data = {
    settings: {},
  }
  data.settings[settingKey] = newValue

  try {
    await companyStore.updateCompanySettings({
      data,
      message: 'general.setting_updated',
    })
  } catch (error) {
    console.error('Failed to update font setting', error)
  }
})
</script>
