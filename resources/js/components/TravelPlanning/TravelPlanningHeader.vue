<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Train, Bell, User } from 'lucide-vue-next';
</script>

<template>
    <header class="bg-card border-b border-border px-6 py-4">
        <div class="flex items-center justify-between max-w-7xl mx-auto">
            <div class="flex items-center gap-2">
                <Train class="w-6 h-6 text-primary" />
                <span class="text-xl font-semibold text-foreground">InterrailPlanner</span>
            </div>

            <nav class="hidden md:flex items-center gap-8">
                <a href="#" class="text-muted-foreground hover:text-foreground">Routes</a>
                <a href="#" class="text-muted-foreground hover:text-foreground">Destinations</a>
                <a href="#" class="text-muted-foreground hover:text-foreground">Passes</a>
                <a href="#" class="text-muted-foreground hover:text-foreground">Help</a>
            </nav>

            <div class="flex items-center gap-3">
                <Button variant="ghost" size="icon">
                    <Bell class="w-5 h-5" />
                </Button>
                <Avatar class="w-8 h-8">
                    <AvatarImage src="/placeholder-user.jpg" />
                    <AvatarFallback>
                        <User class="w-4 h-4" />
                    </AvatarFallback>
                </Avatar>
            </div>
        </div>
    </header>
</template> 