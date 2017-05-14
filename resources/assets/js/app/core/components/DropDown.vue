<template>
    <li class="dropdown" :class="{ open : open }" @click="show">

        <a href="#" class="dropdown-toggle" @click.stop="show">
            <span :class="icon"></span>
            <span class="muted" v-if="muted">{{ muted }} :</span>
            {{ name }}
        </a>

        <ul class="dropdown-menu">
            <slot></slot>
        </ul>

    </li>
</template>

<script>
    export default {
        props : {
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
            show() {
                if(this.open) {
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
        }
    }
</script>