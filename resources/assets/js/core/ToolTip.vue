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
        :aria-label="label"
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
            label: {
                type: String,
                default: ''
            },
            placement: {
                type: String,
                default: 'bottom'
            }
        }
    }
</script>