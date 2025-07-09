<script setup lang="ts">
import { computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Euro } from 'lucide-vue-next';

interface Props {
    startDate: string;
    endDate: string;
}

const props = defineProps<Props>();

// Mock cost calculation
const calculateCosts = computed(() => {
    const basePassCost = 200;
    const accommodationPerDay = 50;
    const reservationCost = 30;
    
    if (!props.startDate || !props.endDate) {
        return { pass: 0, accommodation: 0, reservations: 0, total: 0 };
    }
    
    const days = Math.ceil((new Date(props.endDate).getTime() - new Date(props.startDate).getTime()) / (1000 * 60 * 60 * 24));
    const accommodation = days * accommodationPerDay;
    const reservations = Math.min(days * reservationCost, 150);
    const total = basePassCost + accommodation + reservations;
    
    return {
        pass: basePassCost,
        accommodation,
        reservations,
        total
    };
});
</script>

<template>
    <Card class="border-[#FFCA28]/30">
        <CardHeader class="rounded-t-lg">
            <CardTitle class="flex items-center gap-2 text-[#212121]">
                <Euro class="w-5 h-5 text-[#FFCA28]" />
                Cost Breakdown
            </CardTitle>
        </CardHeader>
        <CardContent>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-primary/10 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-primary">€{{ calculateCosts.pass }}</div>
                    <div class="text-sm text-primary/80">Interrail Pass</div>
                </div>
                <div class="bg-accent/10 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-accent-foreground">€{{ calculateCosts.accommodation }}</div>
                    <div class="text-sm text-accent-foreground/80">Accommodation</div>
                </div>
                <div class="bg-[#E8F5E9] rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-primary">€{{ calculateCosts.reservations }}</div>
                    <div class="text-sm text-primary/80">Reservations</div>
                </div>
                <div class="bg-muted/20 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-muted-foreground">€{{ calculateCosts.total }}</div>
                    <div class="text-sm text-muted-foreground/80">Total per Person</div>
                </div>
            </div>
        </CardContent>
    </Card>
</template> 