<template>
    <button :class="classes" @click="subscribe()">Subscribe</button>
</template>

<script>
    export default {
        props: ['active'],
        data() {
            return {
                isSubscribed: this.active,
            }
        },
        computed: {
            classes() {
                return ['btn', this.isSubscribed ? 'btn-primary' : 'btn-outline-primary'];
            }
        },
        methods: {
            subscribe() {
                axios[this.isSubscribed ? 'delete' : 'post'](location.pathname + '/subscriptions');
                this.isSubscribed = !this.isSubscribed;
                flash('Subscribed');
            }
        }
    }
</script>

<style lang="scss" scoped>

</style>
