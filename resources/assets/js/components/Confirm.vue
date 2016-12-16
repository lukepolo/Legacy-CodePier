<template>
    <button @click.stop="confirm=!confirm" @keyup.32.prevent>
        <span v-show="!confirm" :class="btnClass">
            <slot></slot>
        </span>
        <span v-show="confirm">
            <template v-if="confirm_with_text">
                Please confirm by typing in : {{ confirm_with_text }}
                <form @submit.stop="confirmMethod">
                    <input v-model="confirmedText" type="text" @click.stop>
                </form>
            </template>
            <span>{{ confirmText }}</span>
            <button class="btn btn-danger" @click.stop="confirmMethod">Yes</button>
            <button class="btn btn-primary">No</button>
        </span>
    </button>
</template>

<script>
    export default {
        props: ['dispatch', 'params', 'class', 'confirm_text', 'confirm_with_text'],
        data() {
            return {
                confirm: false,
                confirmedText : '',
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
                if(this.confirm_with_text) {
                    if(_.lowerCase(this.confirmedText) != _.lthowerCase(this.confirm_with_text)) {
                        return false;
                    }
                    this.confirmedText = '';
                }

                this.confirm = false;

                $(this.$el).find('button').dropdown('toggle');
                this.$store.dispatch(this.dispatch, this.params);
            }
        }
    }
</script>