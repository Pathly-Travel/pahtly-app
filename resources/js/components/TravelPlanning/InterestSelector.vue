<script setup lang="ts">
import { ref } from 'vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import { Plus, X } from 'lucide-vue-next';

interface Props {
    interests: string[];
    popularInterests?: string[];
    error?: string;
}

interface Emits {
    (e: 'update:interests', value: string[]): void;
}

const props = withDefaults(defineProps<Props>(), {
    popularInterests: () => [
        'Museums', 'Architecture', 'Food & Cuisine', 'Nightlife', 'Shopping',
        'History', 'Art', 'Nature', 'Beaches', 'Mountains', 'Culture',
        'Photography', 'Adventure Sports', 'Music', 'Festivals'
    ]
});

const emit = defineEmits<Emits>();

const newInterest = ref('');

// Add interest
const addInterest = () => {
    if (newInterest.value.trim() && !props.interests.includes(newInterest.value.trim())) {
        const updatedInterests = [...props.interests, newInterest.value.trim()];
        emit('update:interests', updatedInterests);
        newInterest.value = '';
    }
};

// Add popular interest
const addPopularInterest = (interest: string) => {
    if (!props.interests.includes(interest)) {
        const updatedInterests = [...props.interests, interest];
        emit('update:interests', updatedInterests);
    }
};

// Remove interest
const removeInterest = (index: number) => {
    const updatedInterests = props.interests.filter((_, i) => i !== index);
    emit('update:interests', updatedInterests);
};
</script>

<template>
    <div class="space-y-2">
        <Label class="text-[#212121] font-medium">Interests</Label>
        <div class="flex gap-2">
            <Input 
                v-model="newInterest" 
                placeholder="e.g., Museums, Food, Nightlife"
                @keyup.enter="addInterest"
                class="flex-1 bg-white/80 border-primary/30 focus:border-primary"
            />
            <Button @click="addInterest" :disabled="!newInterest.trim()" size="sm" class="bg-primary hover:bg-primary/90">
                <Plus class="h-4 w-4" />
            </Button>
        </div>
        
        <!-- Popular Interests -->
        <div class="mt-3">
            <Label class="text-sm font-medium text-[#212121]">Popular Interests</Label>
            <div class="flex flex-wrap gap-2 mt-2">
                <button 
                    v-for="interest in popularInterests" 
                    :key="interest"
                    @click="addPopularInterest(interest)"
                    :disabled="interests.includes(interest)"
                    class="rounded-md border border-[#FFCA28]/40 bg-[#FFCA28]/10 px-2 py-1 text-sm text-[#212121] hover:bg-[#FFCA28]/20 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {{ interest }}
                </button>
            </div>
        </div>
        
        <div v-if="interests.length > 0" class="flex flex-wrap gap-2 mt-2">
            <span 
                v-for="(interest, index) in interests" 
                :key="index"
                class="inline-flex items-center gap-1 rounded-md bg-primary text-primary-foreground px-2 py-1 text-sm"
            >
                {{ interest }}
                <button @click="removeInterest(index)" class="text-primary-foreground/80 hover:text-primary-foreground">
                    <X class="h-3 w-3" />
                </button>
            </span>
        </div>
        <InputError :message="error" />
    </div>
</template> 