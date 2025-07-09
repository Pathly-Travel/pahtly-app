<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { BreadcrumbItem } from '@/types';

// Import all the new components
import TravelPlanningHeader from '@/components/TravelPlanning/TravelPlanningHeader.vue';
import TravelPlanningHero from '@/components/TravelPlanning/TravelPlanningHero.vue';
import TripPlanningForm from '@/components/TravelPlanning/TripPlanningForm.vue';
import RouteDisplay from '@/components/TravelPlanning/RouteDisplay.vue';
import AITravelAssistant from '@/components/TravelPlanning/AITravelAssistant.vue';
import CostBreakdown from '@/components/TravelPlanning/CostBreakdown.vue';

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
    numberOfTravelers: 1,
    maxTravelTimePerDay: 6,
    transportType: 'Interrail-Global Pass',
    budget: 1000,
    tripType: '',
});

// UI state
const results = ref<{ cities: string[] } | null>(null);
const isSubmitting = ref(false);
const viewMode = ref('map');
const chatMessage = ref('');

// Submit form
const submit = async () => {
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

// Handle form updates
const updateForm = (newForm: any) => {
    Object.keys(newForm).forEach(key => {
        if (key !== 'errors') {
            form[key] = newForm[key];
        }
    });
};

// Handle view mode changes
const updateViewMode = (mode: string) => viewMode.value = mode;

// Handle chat message updates
const updateChatMessage = (message: string) => chatMessage.value = message;

// Handle sending chat messages
const sendChatMessage = () => {
    // TODO: Implement chat functionality
    console.log('Sending message:', chatMessage.value);
    chatMessage.value = '';
};
</script>

<template>
    <Head title="Travel Planning" />

    <div class="min-h-screen bg-background">
        <!-- Header -->
        <TravelPlanningHeader />

        <div class="max-w-7xl mx-auto px-6 py-8">
            <!-- Hero Section -->
            <TravelPlanningHero />

            <div class="grid lg:grid-cols-2 gap-8">
                <!-- Left Column - Plan Your Trip -->
                <TripPlanningForm 
                    :form="form"
                    :is-submitting="isSubmitting"
                    @submit="submit"
                    @update:form="updateForm"
                />

                <!-- Right Column - Your Interrail Route -->
                <RouteDisplay 
                    :results="results"
                    @update:view-mode="updateViewMode"
                />
            </div>

            <!-- <div class="grid lg:grid-cols-2 gap-8 mt-8">
                <CostBreakdown 
                    :start-date="form.startDate"
                    :end-date="form.endDate"
                />
            </div> -->
        </div>
    </div>
</template> 