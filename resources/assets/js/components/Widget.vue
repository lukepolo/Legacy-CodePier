<template>
    <div class="providers--item" :class="{grow : show}">
        <div class="providers--item-header" @click="show = !show">
            <div class="providers--item-icon">
                <span :class="icon"></span>
            </div>
        </div>
        <div class="providers--item-footer">
            <div class="providers--item-footer-connect">
                <h4>{{ title }}</h4>
            </div>
        </div>

        <div class="providers--expand">
            <slot v-if="show"></slot>
        </div>
    </div>
</template>

<script>
    export default {
        props : {
            icon : {
                default : '',
            },
            title : {
                default : '',
            }
        },
        data() {
            return {
                show : false,
            }
        },
        methods : {

        },
        computed: {

        }
    }
</script>