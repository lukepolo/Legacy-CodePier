<template>
    <span class="confirm-container" @click.stop="confirm=!confirm" @keyup.32.prevent>
        <button class="btn" v-show="!confirm">
            <slot></slot>
        </button>
        <span v-show="confirm">
            <button class="btn">
                <slot></slot>

            </button>

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
                        <button class="btn">Cancel</button>
                        <button class="btn btn-danger" @click.stop="confirmMethod">Delete</button>
                    </div>
                </div>
        </span>
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