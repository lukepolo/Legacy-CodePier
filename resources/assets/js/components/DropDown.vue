<template>
    <span :is="tag" class="dropdown" :class="{ open : open }" @click="show($event.target)">

        <slot name="header">
            <a href="#" class="dropdown-toggle">
                <span :class="icon"></span>
                <span class="muted" v-if="muted">{{ muted }} :</span>
                {{ name }}
            </a>
        </slot>

        <slot name="content" @click.stop="done">
            <ul class="dropdown-menu">
                <slot></slot>
            </ul>
        </slot>
    </span>
</template>

<script>
    export default {
        props : {
            'tag' : {
                default : 'li'
            },
            'name': {
                default : null
            },
            'muted': {
                default : null
            },
            'icon': {
                default : null
            }
        },
        data() {
            return {
                'open' : false
            }
        },
        methods: {
            show(target) {
                if(
                    this.open &&
                    !app.isTag(target, 'textarea') &&
                    !app.isTag(target, 'input') &&
                    !app.isTag(target, 'select') &&
                    !app.isTag(target, 'option')
                ) {
                    return this.open = false
                }

                app.$emit('close-dropdowns')
                this.open = true
            },
        },
        created() {
            app.$on('close-dropdowns', () => {
                this.open = false
            })
        },
        slots() {
            return this.$slots
        },
    }
</script>