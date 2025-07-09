<script setup lang="ts">
import { Label } from '@/components/ui/label';
import DateInput from '@/components/ui/input/DateInput.vue';
import InputError from '@/components/InputError.vue';

interface Props {
    startDate: string;
    endDate: string;
    startDateError?: string;
    endDateError?: string;
}

interface Emits {
    (e: 'update:startDate', value: string): void;
    (e: 'update:endDate', value: string): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

// Today's date for min date validation
const today = new Date().toISOString().split('T')[0];
</script>

<template>
    <div class="grid grid-cols-2 gap-4">
        <div class="space-y-2">
            <Label for="start-date" class="text-[#212121] font-medium">Start Date</Label>
            <DateInput
                id="start-date"
                :min="today"
                :model-value="startDate"
                @update:model-value="emit('update:startDate', $event)"
                placeholder="dd/mm/yyyy"
                class="bg-white/80 border-primary/30 focus:border-primary w-full h-10 px-3 py-2"
            />
            <InputError :message="startDateError" />
        </div>
        <div class="space-y-2">
            <Label for="end-date" class="text-[#212121] font-medium">End Date</Label>
            <DateInput
                id="end-date"
                :min="startDate || today"
                :model-value="endDate"
                @update:model-value="emit('update:endDate', $event)"
                placeholder="dd/mm/yyyy"
                class="bg-white/80 border-primary/30 focus:border-primary w-full h-10 px-3 py-2"
            />
            <InputError :message="endDateError" />
        </div>
    </div>
</template> 