<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Bot, Send } from 'lucide-vue-next';

interface Props {
    chatMessage: string;
}

interface Emits {
    (e: 'update:chatMessage', value: string): void;
    (e: 'sendMessage'): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const sendMessage = () => {
    if (props.chatMessage.trim()) {
        emit('sendMessage');
    }
};
</script>

<template>
    <Card class=" border-[#90A4AE]/30">
        <CardHeader class="bg-[#90A4AE]/20 rounded-t-lg">
            <CardTitle class="flex items-center gap-2 text-[#90A4AE]">
                <Bot class="w-5 h-5 text-[#90A4AE]" />
                AI Travel Assistant
            </CardTitle>
        </CardHeader>
        <CardContent>
            <div class="space-y-4">
                <div class="flex gap-3">
                    <Avatar class="w-8 h-8 bg-[#90A4AE]/20">
                        <AvatarFallback>
                            <Bot class="w-4 h-4 text-[#90A4AE]" />
                        </AvatarFallback>
                    </Avatar>
                    <div class="flex-1 bg-white/60 rounded-lg p-3 border border-[#90A4AE]/20">
                        <p class="text-sm text-[#616161]">
                            Hi! I can help you plan your Interrail journey. Ask me about routes, destinations, or travel tips!
                        </p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <Input
                        placeholder="Ask me anything..."
                        :model-value="chatMessage"
                        @update:model-value="emit('update:chatMessage', $event)"
                        @keyup.enter="sendMessage"
                        class="flex-1 bg-white/80 border-[#90A4AE]/30 focus:border-[#90A4AE]"
                    />
                    <Button @click="sendMessage" size="icon" class="bg-[#90A4AE] hover:bg-[#90A4AE]/90 text-white">
                        <Send class="w-4 h-4" />
                    </Button>
                </div>
            </div>
        </CardContent>
    </Card>
</template> 