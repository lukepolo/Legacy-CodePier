<style>
    .confirm-enter-active, .confirm-leave-active {
    }
    .confirm-enter .confirm-leave-active {
    }
</style>
<template>
    <span class="confirm-container" @click.stop @keyup.32.prevent @keyup.esc="confirm=false">
        <button class="btn" @click.stop="confirm=true">
            <slot></slot>
        </button>
        <transition name="confirm">
            <span v-show="confirm">
                <div class="confirm-dialog">
                    <template v-if="confirm_with_text">
                        <h4 class="confirm-header">{{ confirmText }}</h4>
                        <div class="confirm-content">
                            <p>Please confirm by typing in: {{ confirm_with_text }}</p>
                            <div class="jcf-input-group">
                                <form @submit.prevent.stop="confirmMethod">
                                    <input v-model="confirmedText" type="text" name="confirm-name" @click.stop>
                                    <label for="confirm-name"><span class="float-label"></span></label>
                                </form>
                            </div>
                        </div>
                    </template>
                    <div class="btn-footer">
                        <button class="btn" @click.stop="confirm=false">Cancel</button>
                        <button class="btn btn-danger" @click.stop="confirmMethod">Delete</button>
                    </div>
                </div>
            </span>
        </transition>
    </span>
</template>

<script>
    export default {
        props: [
            'params',
            'dispatch',
            'confirm_text',
            'confirm_with_text'
        ],
        data() {
            return {
                confirm: false,
                confirmedText : '',
            };
        },
        computed: {
            confirmText() {
                return this.confirm_text ? this.confirm_text : 'Are you sure?';
            }
        },
        methods: {
            confirmMethod() {
                if(this.confirm_with_text) {
                    if(_.lowerCase(this.confirmedText) != _.lowerCase(this.confirm_with_text)) {
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