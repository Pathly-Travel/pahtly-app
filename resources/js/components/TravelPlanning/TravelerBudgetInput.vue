<script setup lang="ts">
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';

interface Props {
    numberOfTravelers: number;
    budget: number;
    numberOfTravelersError?: string;
    budgetError?: string;
    travelerOptions?: Array<{ value: number; label: string }>;
}

interface Emits {
    (e: 'update:numberOfTravelers', value: number): void;
    (e: 'update:budget', value: number): void;
}

const props = withDefaults(defineProps<Props>(), {
    travelerOptions: () => [
        { value: 1, label: '1 Person' },
        { value: 2, label: '2 People' },
        { value: 3, label: '3 People' },
        { value: 4, label: '4 People' },
        { value: 5, label: '5+ People' }
    ]
});

const emit = defineEmits<Emits>();
</script>

<template>
    <div class="grid grid-cols-2 gap-4">
        <div class="space-y-2">
            <Label class="text-[#212121] font-medium">Travelers</Label>
            <select 
                :value="numberOfTravelers"
                @input="emit('update:numberOfTravelers', parseInt(($event.target as HTMLSelectElement).value))"
                class="w-full rounded-md border border-primary/30 bg-white/80 px-3 py-2 text-sm text-[#212121] focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
            >
                <option v-for="option in travelerOptions" :key="option.value" :value="option.value">
                    {{ option.label }}
                </option>
            </select>
            <InputError :message="numberOfTravelersError" />
        </div>
        <div class="space-y-2">
            <Label for="budget" class="text-[#212121] font-medium">Budget (€)</Label>
            <Input 
                id="budget" 
                type="number" 
                :model-value="budget"
                @update:model-value="emit('update:budget', parseInt($event) || 0)"
                class="bg-white/80 border-primary/30 focus:border-primary"
            />
            <InputError :message="budgetError" />
        </div>
    </div>
</template> 