<template>
    <div id="message">
        <div v-for="(msg, index) in messages" :key="msg.id" class="message" :class="`message--${msg.type}`" :style="{
            animation: `slideInLeft ease .3s, fadeOut linear 1s ${msg.delay}s forwards`
        }" @click="removeMessage(index)">
            <div class="message__icon">
                <i :class="icons[msg.type]"></i>
            </div>
            <div class="message_content">
                <h3 class="message__title">{{ msg.title }}</h3>
                <p class="message__msg">{{ msg.text }}</p>
            </div>
            <div class="message__close">
                <i class="fas fa-times"></i>
            </div>
        </div>
    </div>
</template>

<script>
import message from '@/utils/messageState';
export default {
    name: 'message',
    data() {
        return {
            messages: [],
            icons: {
                success: 'fas fa-check-circle',
                info: 'fas fa-info-circle',
                warning: 'fas fa-exclamation-circle',
                error: 'fas fa-exclamation-circle',
            },
        };
    },
    methods: {
        show(title = '', text = '', type = 'info', duration = 2000) {
            const id = Date.now();
            const delay = (duration / 1000).toFixed(2);
            const message = { id, title, text, type, delay };

            this.messages.push(message);

            setTimeout(() => {
                this.messages = this.messages.filter(msg => msg.id !== id);
            }, duration + 1000);
        },
        removeMessage(index) {
            this.messages.splice(index, 1);
        },
    },
    mounted() {
        message.on('show-message', ({ title, text, type = 'info', duration = 2000 }) => {
            this.show(title, text, type, duration);
        });
    },
    unmounted() {
        message.off('show-message');
    }
};
</script>