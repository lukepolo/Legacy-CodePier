<template>
    <button @click.prevent="confirm=!confirm">
        <span
            v-show="!confirm"
            :class="btnClass"

        >
            <slot></slot>
        </span>
        <span
            v-show="confirm"
            class=''
        >
            {{ confirmText }}
		</span>
        <button
            v-show="confirm"
            class="btn btn-danger"
            @click.prevent="confirmMethod"
        >
            Yes
        </button>

        <button
            v-show="confirm"
            class="btn btn-primary"
        >
            No
        </button>
    </button>
</template>

<script>
    export default {
        props: ['dispatch', 'params', 'class', 'confirm_text', 'close'],
        data() {
            return {
                confirm: false
            };
        },
        computed: {
            btnClass() {
                return this.class;
            },
            confirmText() {
                return this.confirm_text ? this.confirm_text : 'Are you sure?';
            }
        },
        methods: {
            confirmMethod() {
                this.confirm = close ? close : true;
                this.$store.dispatch(this.dispatch, this.params);
            }
        }
    }
</script>