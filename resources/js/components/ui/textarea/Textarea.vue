<script setup lang="ts">
import { cn } from '@/lib/utils'

interface TextareaProps {
  class?: string
  placeholder?: string
  disabled?: boolean
  readonly?: boolean
  rows?: number
  cols?: number
  modelValue?: string
}

defineProps<TextareaProps>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

const handleInput = (event: Event) => {
  const target = event.target as HTMLTextAreaElement
  emit('update:modelValue', target.value)
}
</script>

<template>
  <textarea
    :class="cn(
      'flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
      $props.class
    )"
    :placeholder="placeholder"
    :disabled="disabled"
    :readonly="readonly"
    :rows="rows"
    :cols="cols"
    :value="modelValue"
    @input="handleInput"
  />
</template> 