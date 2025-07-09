 <script setup lang="ts">
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
import DateRangeInput from './DateRangeInput.vue';
import LocationInput from './LocationInput.vue';
import InterestSelector from './InterestSelector.vue';
import TravelerBudgetInput from './TravelerBudgetInput.vue';
import { Route, Train, LoaderCircle } from 'lucide-vue-next';

interface Props {
    form: {
        startDate: string;
        endDate: string;
        preferredLocations: string[];
        interests: string[];
        startLocation: string;
        numberOfTravelers: number;
        maxTravelTimePerDay: number;
        transportType: string;
        budget: number;
        tripType: string;
        errors: Record<string, string>;
    };
    isSubmitting: boolean;
}

interface Emits {
    (e: 'submit'): void;
    (e: 'update:form', value: any): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

// Form validation
const isFormValid = computed(() => {
    return props.form.startDate && 
           props.form.endDate && 
           props.form.preferredLocations.length > 0 && 
           props.form.interests.length > 0;
});

// Update form helper
const updateForm = (key: string, value: any) => {
    emit('update:form', { ...props.form, [key]: value });
};
</script>

<template>
    <Card class=" border-primary/20">
        <CardHeader class="rounded-t-lg">
            <CardTitle class="flex items-center gap-2 text-primary">
                <Route class="w-5 h-5 text-primary" />
                Plan Your Trip
            </CardTitle>
        </CardHeader>
        <CardContent class="space-y-6">
            <!-- Date Inputs -->
            <DateRangeInput
                :start-date="form.startDate"
                :end-date="form.endDate"
                :start-date-error="form.errors.startDate"
                :end-date-error="form.errors.endDate"
                @update:start-date="updateForm('startDate', $event)"
                @update:end-date="updateForm('endDate', $event)"
            />

            <!-- Starting Location -->
            <div class="space-y-2">
                <Label for="location">Starting Location</Label>
                <Input
                    id="location"
                    placeholder="Enter city or country"
                    :model-value="form.startLocation"
                    @update:model-value="updateForm('startLocation', $event)"
                />
                <InputError :message="form.errors.startLocation" />
            </div>

            <!-- Travelers and Budget -->
            <!-- <TravelerBudgetInput
                :number-of-travelers="form.numberOfTravelers"
                :budget="form.budget"
                :number-of-travelers-error="form.errors.numberOfTravelers"
                :budget-error="form.errors.budget"
                @update:number-of-travelers="updateForm('numberOfTravelers', $event)"
                @update:budget="updateForm('budget', $event)"
            /> -->

            <!-- Transport -->
            <div class="space-y-2">
                <Label class="text-[#212121] font-medium">Transport</Label>
                <div class="p-3 border border-primary/20 rounded-lg">
                    <div class="flex items-center gap-2">
                        <Train class="w-4 h-4 text-primary" />
                        <span class="text-sm font-medium text-primary">Public Transport / Interrail</span>
                    </div>
                </div>
            </div>

            <!-- Preferred Locations -->
            <LocationInput
                :locations="form.preferredLocations"
                :error="form.errors.preferredLocations"
                @update:locations="updateForm('preferredLocations', $event)"
            />

            <!-- Interests -->
            <!-- <InterestSelector
                :interests="form.interests"
                :error="form.errors.interests"
                @update:interests="updateForm('interests', $event)"
            /> -->

            <!-- Trip Type -->
            <!-- <div class="space-y-2">
                <Label for="trip-type" class="text-[#212121] font-medium">Trip Type</Label>
                <Textarea
                    id="trip-type"
                    placeholder="Describe your ideal trip (culture, nature, cities, nightlife...)"
                    :model-value="form.tripType"
                    @update:model-value="updateForm('tripType', $event)"
                    :rows="3"
                    class="bg-white/80 border-primary/30 focus:border-primary"
                />
                <InputError :message="form.errors.tripType" />
            </div> -->

            <Button 
                @click="emit('submit')" 
                :disabled="!isFormValid || isSubmitting"
                class="w-full bg-primary hover:bg-primary/90"
            >
                <LoaderCircle v-if="isSubmitting" class="w-4 h-4 mr-2 animate-spin" />
                <Route v-else class="w-4 h-4 mr-2" />
                {{ isSubmitting ? 'Generating Route...' : 'Generate Route' }}
            </Button>
        </CardContent>
    </Card>
</template> 