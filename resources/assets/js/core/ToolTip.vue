<template>
    <span
        :class="[
        'hint--'+type,
        'hint--'+size,
        'hint--'+placement,
        {
            'hint--rounded': this.rounded,
            'hint--always': this.always,
            'hint--no-animate': this.noAnimate
        }]"
        :aria-label="message"
    >
        {{ type }}
        <slot></slot>
    </span>
</template>
<script>
    export default {
        props: {
            type: String,
            size: String,
            always: Boolean,
            rounded: Boolean,
            noAnimate: Boolean,
            message: {
                type: String,
                default: ''
            },
            placement: {
                type: String,
                default: 'top'
            }
        }
    }
</script>