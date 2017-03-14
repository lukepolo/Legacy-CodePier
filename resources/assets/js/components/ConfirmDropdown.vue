<template>
    <div @click.stop @keyup.32.prevent @keyup.esc="close()">
        <div @click="open()">
            <slot></slot>
        </div>
        <transition name="confirm">
            <div v-show="confirm" class="confirm">
                <template v-if="confirm_with_text">
                    <h4 class="confirm--header">Are you sure?</h4>
                    <div class="confirm--content">
                        <p>Please confirm by typing in: {{ confirm_with_text }}</p>
                        <div class="jcf-input-group">
                            <form @submit.prevent.stop="confirmMethod">
                                <input ref="confirm_input" v-model="confirmedText" type="text" name="confirm-name">
                                <label for="confirm-name"><span class="float-label"></span></label>
                            </form>
                        </div>
                    </div>
                </template>
                <div class="confirm--btns">
                    <button class="btn btn-small" @click.stop="close()">{{ cancelText }}</button>
                    <button class="btn btn-small btn-danger" :class="{ 'btn-disabled' : !textConfirmed }" @click.stop="confirmMethod">{{ confirmText }}</button>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
    export default {
        props: [
            'params',
            'dispatch',
            'cancel_text',
            'confirm_text',
            'confirm_with_text'
        ],
        data() {
            return {
                confirm: false,
                confirmedText : '',
            }
        },
        watch : {
            'confirm'() {
                Vue.nextTick(() => {
                    if( this.$refs.confirm_input) {
                        this.$refs.confirm_input.focus()
                    }
                })
            },

        },
        computed: {
            cancelText() {
                return 'Cancel'
            },
            confirmText() {
                return this.confirm_text ? this.confirm_text : 'Confirm'
            },
            textConfirmed() {
                if(this.confirm_with_text) {
                    if(_.lowerCase(this.confirmedText) != _.lowerCase(this.confirm_with_text)) {
                        return false
                    }
                }
                return true
            }
        },
        methods: {
            open() {
                app.$emit('close-confirms')
                this.confirm = true
            },
            close() {
                $(this.$el).closest('.dropdown').find('.dropdown-toggle').dropdown('toggle')
                this.confirm = false
            },
            confirmMethod() {
                if(this.textConfirmed) {
                    this.confirmedText = ''
                    this.$store.dispatch(this.dispatch, this.params)
                    this.close()
                }
            }
        },
        created() {
            app.$on('close-confirms', () => {
                this.confirm = false
            })
        }
    }
</script>