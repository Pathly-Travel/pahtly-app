<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { LoaderCircle, MapPin, Calendar, Users, Clock, Train, Heart, Plus, X } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Travel Planning',
        href: '/travel-planning',
    },
];

// Form state
const form = useForm({
    startDate: '',
    endDate: '',
    preferredLocations: [] as string[],
    interests: [] as string[],
    startLocation: 'Amsterdam Centraal',
    numberOfTravelers: 4,
    maxTravelTimePerDay: 6,
    transportType: 'Interrail-Global Pass',
});

// UI state
const newLocation = ref('');
const newInterest = ref('');
const results = ref<{ cities: string[] } | null>(null);
const isSubmitting = ref(false);

// Common transport types
const transportTypes = [
    'Interrail-Global Pass',
    'Eurail Pass',
    'Flight',
    'Train',
    'Bus',
    'Car',
    'Mixed Transport'
];

// Common interests
const popularInterests = [
    'Museums', 'Architecture', 'Food & Cuisine', 'Nightlife', 'Shopping',
    'History', 'Art', 'Nature', 'Beaches', 'Mountains', 'Culture',
    'Photography', 'Adventure Sports', 'Music', 'Festivals'
];

// Add location
const addLocation = () => {
    if (newLocation.value.trim() && !form.preferredLocations.includes(newLocation.value.trim())) {
        form.preferredLocations.push(newLocation.value.trim());
        newLocation.value = '';
    }
};

// Remove location
const removeLocation = (index: number) => {
    form.preferredLocations.splice(index, 1);
};

// Add interest
const addInterest = () => {
    if (newInterest.value.trim() && !form.interests.includes(newInterest.value.trim())) {
        form.interests.push(newInterest.value.trim());
        newInterest.value = '';
    }
};

// Add popular interest
const addPopularInterest = (interest: string) => {
    if (!form.interests.includes(interest)) {
        form.interests.push(interest);
    }
};

// Remove interest
const removeInterest = (index: number) => {
    form.interests.splice(index, 1);
};

// Form validation
const isFormValid = computed(() => {
    return form.startDate && 
           form.endDate && 
           form.preferredLocations.length > 0 && 
           form.interests.length > 0;
});

// Submit form
const submit = async () => {
    if (!isFormValid.value) return;
    
    isSubmitting.value = true;
    results.value = null;
    
    try {
        const response = await fetch('/travel-planning/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify(form.data()),
        });
        
        const data = await response.json();
        
        if (data.success) {
            results.value = data.data;
        } else {
            console.error('Error:', data.error);
            alert('Failed to generate travel plan. Please try again.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to generate travel plan. Please try again.');
    } finally {
        isSubmitting.value = false;
    }
};

// Today's date for min date validation
const today = new Date().toISOString().split('T')[0];
</script>

<template>
    <Head title="Travel Planning" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <div class="max-w-4xl mx-auto w-full">
                <HeadingSmall 
                    title="Travel Planning" 
                    description="Plan your perfect trip by telling us your preferences and let us suggest amazing cities to visit"
                />
                
                <div class="grid gap-6 mt-8 lg:grid-cols-2">
                    <!-- Form Section -->
                    <div class="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2">
                                    <Calendar class="h-5 w-5" />
                                    Trip Details
                                </CardTitle>
                                <CardDescription>
                                    When would you like to travel?
                                </CardDescription>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <Label for="startDate">Start Date</Label>
                                        <Input 
                                            id="startDate" 
                                            type="date" 
                                            :min="today"
                                            v-model="form.startDate"
                                            class="mt-1"
                                        />
                                        <InputError :message="form.errors.startDate" />
                                    </div>
                                    <div>
                                        <Label for="endDate">End Date</Label>
                                        <Input 
                                            id="endDate" 
                                            type="date" 
                                            :min="form.startDate || today"
                                            v-model="form.endDate"
                                            class="mt-1"
                                        />
                                        <InputError :message="form.errors.endDate" />
                                    </div>
                                </div>
                                
                                <div>
                                    <Label for="startLocation">Starting Location</Label>
                                    <Input 
                                        id="startLocation" 
                                        v-model="form.startLocation"
                                        placeholder="e.g., Amsterdam Centraal"
                                        class="mt-1"
                                    />
                                    <InputError :message="form.errors.startLocation" />
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <Label for="numberOfTravelers">Number of Travelers</Label>
                                        <Input 
                                            id="numberOfTravelers" 
                                            type="number" 
                                            min="1"
                                            max="20"
                                            v-model.number="form.numberOfTravelers"
                                            class="mt-1"
                                        />
                                        <InputError :message="form.errors.numberOfTravelers" />
                                    </div>
                                    <div>
                                        <Label for="maxTravelTimePerDay">Max Travel Time/Day (hours)</Label>
                                        <Input 
                                            id="maxTravelTimePerDay" 
                                            type="number" 
                                            min="1"
                                            max="24"
                                            v-model.number="form.maxTravelTimePerDay"
                                            class="mt-1"
                                        />
                                        <InputError :message="form.errors.maxTravelTimePerDay" />
                                    </div>
                                </div>
                                
                                <div>
                                    <Label for="transportType">Transport Type</Label>
                                    <select 
                                        id="transportType" 
                                        v-model="form.transportType"
                                        class="mt-1 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                    >
                                        <option v-for="type in transportTypes" :key="type" :value="type">
                                            {{ type }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.transportType" />
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2">
                                    <MapPin class="h-5 w-5" />
                                    Preferred Locations
                                </CardTitle>
                                <CardDescription>
                                    Add cities or regions you'd like to visit
                                </CardDescription>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div class="flex gap-2">
                                    <Input 
                                        v-model="newLocation" 
                                        placeholder="e.g., Paris, Rome, Barcelona"
                                        @keyup.enter="addLocation"
                                        class="flex-1"
                                    />
                                    <Button @click="addLocation" :disabled="!newLocation.trim()" size="sm">
                                        <Plus class="h-4 w-4" />
                                    </Button>
                                </div>
                                
                                <div v-if="form.preferredLocations.length > 0" class="flex flex-wrap gap-2">
                                    <span 
                                        v-for="(location, index) in form.preferredLocations" 
                                        :key="index"
                                        class="inline-flex items-center gap-1 rounded-md bg-secondary px-2 py-1 text-sm"
                                    >
                                        {{ location }}
                                        <button @click="removeLocation(index)" class="text-muted-foreground hover:text-foreground">
                                            <X class="h-3 w-3" />
                                        </button>
                                    </span>
                                </div>
                                
                                <InputError :message="form.errors.preferredLocations" />
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2">
                                    <Heart class="h-5 w-5" />
                                    Interests
                                </CardTitle>
                                <CardDescription>
                                    What are you interested in during your trip?
                                </CardDescription>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div class="flex gap-2">
                                    <Input 
                                        v-model="newInterest" 
                                        placeholder="e.g., Museums, Food, Nightlife"
                                        @keyup.enter="addInterest"
                                        class="flex-1"
                                    />
                                    <Button @click="addInterest" :disabled="!newInterest.trim()" size="sm">
                                        <Plus class="h-4 w-4" />
                                    </Button>
                                </div>
                                
                                <div>
                                    <Label class="text-sm font-medium">Popular Interests</Label>
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        <button 
                                            v-for="interest in popularInterests" 
                                            :key="interest"
                                            @click="addPopularInterest(interest)"
                                            :disabled="form.interests.includes(interest)"
                                            class="rounded-md border border-input px-2 py-1 text-sm hover:bg-accent disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            {{ interest }}
                                        </button>
                                    </div>
                                </div>
                                
                                <div v-if="form.interests.length > 0" class="flex flex-wrap gap-2">
                                    <span 
                                        v-for="(interest, index) in form.interests" 
                                        :key="index"
                                        class="inline-flex items-center gap-1 rounded-md bg-primary text-primary-foreground px-2 py-1 text-sm"
                                    >
                                        {{ interest }}
                                        <button @click="removeInterest(index)" class="text-primary-foreground/80 hover:text-primary-foreground">
                                            <X class="h-3 w-3" />
                                        </button>
                                    </span>
                                </div>
                                
                                <InputError :message="form.errors.interests" />
                            </CardContent>
                        </Card>

                        <Button 
                            @click="submit" 
                            :disabled="!isFormValid || isSubmitting"
                            class="w-full"
                            size="lg"
                        >
                            <LoaderCircle v-if="isSubmitting" class="h-4 w-4 animate-spin mr-2" />
                            <MapPin v-else class="h-4 w-4 mr-2" />
                            {{ isSubmitting ? 'Generating Travel Plan...' : 'Generate Travel Plan' }}
                        </Button>
                    </div>

                    <!-- Results Section -->
                    <div class="space-y-6">
                        <Card v-if="results">
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2">
                                    <MapPin class="h-5 w-5" />
                                    Suggested Cities
                                </CardTitle>
                                <CardDescription>
                                    Based on your preferences, here are our city recommendations
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div v-if="results.cities && results.cities.length > 0" class="space-y-3">
                                    <div 
                                        v-for="(city, index) in results.cities" 
                                        :key="index"
                                        class="flex items-center gap-3 p-3 rounded-md border border-border hover:bg-accent/50 transition-colors"
                                    >
                                        <div class="flex-shrink-0 w-8 h-8 bg-primary text-primary-foreground rounded-full flex items-center justify-center text-sm font-medium">
                                            {{ index + 1 }}
                                        </div>
                                        <div>
                                            <h4 class="font-medium">{{ city }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-center py-8 text-muted-foreground">
                                    No cities found. Please try adjusting your preferences.
                                </div>
                            </CardContent>
                        </Card>

                        <Card v-if="!results && !isSubmitting">
                            <CardContent class="py-12">
                                <div class="text-center text-muted-foreground">
                                    <MapPin class="h-12 w-12 mx-auto mb-4 opacity-50" />
                                    <p>Fill out the form and click "Generate Travel Plan" to see city suggestions</p>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template> 