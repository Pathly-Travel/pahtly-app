<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Train, Map, List, Route } from 'lucide-vue-next';

interface Props {
    results: { cities: string[] } | null;
}

interface Emits {
    (e: 'update:viewMode', value: string): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const viewMode = ref('map');

const updateViewMode = (mode: string) => {
    viewMode.value = mode;
    emit('update:viewMode', mode);
};
</script>

<template>
    <Card class=" border-primary/30">
        <CardHeader class="rounded-t-lg">
            <div class="flex items-center justify-between">
                <CardTitle class="flex items-center gap-2 text-primary">
                    <Map class="w-5 h-5 text-primary" />
                    Your Interrail Route
                </CardTitle>
                <div class="flex gap-2">
                    <Button
                        :variant="viewMode === 'list' ? 'default' : 'outline'"
                        size="sm"
                        @click="updateViewMode('list')"
                    >
                        <List class="w-4 h-4 mr-1" />
                        List View
                    </Button>
                    <Button
                        :variant="viewMode === 'map' ? 'default' : 'outline'"
                        size="sm"
                        @click="updateViewMode('map')"
                    >
                        <Map class="w-4 h-4 mr-1" />
                        Map View
                    </Button>
                </div>
            </div>
        </CardHeader>
        <CardContent>
            <div v-if="results && results.cities && results.cities.length > 0" class="space-y-3">
                <div 
                    v-for="(city, index) in results.cities" 
                    :key="index"
                    class="flex items-center gap-3 p-3 rounded-md bg-white/60 border border-primary/20 hover:bg-[#FFCA28]/20 transition-colors"
                >
                    <div class="flex-shrink-0 w-8 h-8 bg-primary text-primary-foreground rounded-full flex items-center justify-center text-sm font-medium">
                        {{ index + 1 }}
                    </div>
                    <div>
                        <h4 class="font-medium">{{ city }}</h4>
                    </div>
                </div>
            </div>
            
            <div v-else class="flex flex-col items-center justify-center py-12 text-center">
                <Train class="w-16 h-16 text-muted mb-4" />
                <h3 class="font-medium text-foreground mb-2">Generate a route to see your journey</h3>
                <p class="text-sm text-muted-foreground">Fill out the form and click "Generate Route"</p>
            </div>

            <div class="mt-8">
                <h4 class="font-medium text-foreground mb-4">Route Overview</h4>
                <div v-if="!results" class="flex flex-col items-center justify-center py-8 text-center">
                    <Route class="w-12 h-12 text-muted mb-3" />
                    <p class="text-sm text-muted-foreground">Your route details will appear here once generated</p>
                </div>
            </div>
        </CardContent>
    </Card>
</template> 