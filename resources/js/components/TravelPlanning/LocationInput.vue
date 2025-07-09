<script setup lang="ts">
import { ref } from 'vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import { Plus, X } from 'lucide-vue-next';

interface Props {
    locations: string[];
    label?: string;
    placeholder?: string;
    error?: string;
}

interface Emits {
    (e: 'update:locations', value: string[]): void;
}

const props = withDefaults(defineProps<Props>(), {
    label: 'Preferred Locations',
    placeholder: 'e.g., Paris, Rome, Barcelona'
});

const emit = defineEmits<Emits>();

const newLocation = ref('');

// Add location
const addLocation = () => {
    if (newLocation.value.trim() && !props.locations.includes(newLocation.value.trim())) {
        const updatedLocations = [...props.locations, newLocation.value.trim()];
        emit('update:locations', updatedLocations);
        newLocation.value = '';
    }
};

// Remove location
const removeLocation = (index: number) => {
    const updatedLocations = props.locations.filter((_, i) => i !== index);
    emit('update:locations', updatedLocations);
};
</script>

<template>
    <div class="space-y-2">
        <Label class="text-[#212121] font-medium">{{ label }}</Label>
        <div class="flex gap-2">
            <Input 
                v-model="newLocation" 
                :placeholder="placeholder"
                @keyup.enter="addLocation"
                class="flex-1 bg-white/80 border-primary/30 focus:border-primary"
            />
            <Button @click="addLocation" :disabled="!newLocation.trim()" size="sm" class="bg-primary hover:bg-primary/90">
                <Plus class="h-4 w-4" />
            </Button>
        </div>
        
        <div v-if="locations.length > 0" class="flex flex-wrap gap-2 mt-2">
            <span 
                v-for="(location, index) in locations" 
                :key="index"
                class="inline-flex items-center gap-1 rounded-md bg-[#E8F5E9] border border-primary/20 px-2 py-1 text-sm text-[#212121]"
            >
                {{ location }}
                <button @click="removeLocation(index)" class="text-[#616161] hover:text-[#212121]">
                    <X class="h-3 w-3" />
                </button>
            </span>
        </div>
        <InputError :message="error" />
    </div>
</template> 