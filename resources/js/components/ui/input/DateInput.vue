<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { useVModel } from '@vueuse/core'
import { ref, onMounted } from 'vue'

const props = defineProps<{
  defaultValue?: string
  modelValue?: string
  class?: HTMLAttributes['class']
  min?: string
  max?: string
  placeholder?: string
  id?: string
}>()

const emits = defineEmits<{
  (e: 'update:modelValue', payload: string): void
}>()

const modelValue = useVModel(props, 'modelValue', emits, {
  passive: true,
  defaultValue: props.defaultValue,
})

const inputRef = ref<HTMLInputElement>()

// Make entire date input clickable
onMounted(() => {
  if (inputRef.value) {
    inputRef.value.addEventListener('click', (e: Event) => {
      // Always trigger the date picker when clicking anywhere on the input
      inputRef.value?.showPicker?.()
    })
  }
})
</script>

<template>
  <input
    ref="inputRef"
    v-model="modelValue"
    type="date"
    :id="id"
    :min="min"
    :max="max"
    :placeholder="placeholder"
    :class="cn(
      'placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
      'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
      'aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive',
      props.class,
    )"
  >
</template>

<style scoped>
input[type="date"] {
  cursor: pointer;
}

input[type="date"]::-webkit-calendar-picker-indicator {
  margin-left: auto;
  cursor: pointer;
  opacity: 1;
}

input[type="date"]::-webkit-datetime-edit {
  cursor: pointer;
}

/* For Firefox */
input[type="date"]::-moz-placeholder {
  opacity: 1;
}
</style> 