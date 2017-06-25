<template>
    <section v-if="adding && form.pile_id">
        <div class="jcf-form-wrap">
            <form @submit.prevent="saveSite" class="floating-labels">
                <div class="jcf-input-group">
                    <input name="domain" v-model="form.domain" type="text">
                    <label for="domain">
                        <span class="float-label">
                            Domain / Alias
                        </span>
                    </label>
                </div>

                <div class="jcf-input-group input-checkbox">

                    <label>

                        <tooltip :message="'If your site requires wildcard for sub domains'" size="medium">
                            <span class="fa fa-info-circle"></span>
                        </tooltip>

                        <input type="checkbox" v-model="form.wildcard_domain" name="wildcard_domain" value="1">
                        <span class="icon"></span>
                        Wildcard Domain

                    </label>

                </div>

                <button class="btn btn-primary">Save</button>
            </form>
        </div>

        <div class="btn-container text-center">
            <div class="btn btn-primary" @click.stop="cancel">
                Cancel
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        props: {
            'pile': {
                default: null
            },
            'adding': {
                default: false
            }
        },
        data () {
            return {
                form: this.createForm({
                    domain: null,
                    wildcard_domain : false,
                    pile_id: this.pile && this.pile.id,
                })
            }
        },
        methods: {
            saveSite () {
                this.$store.dispatch('user_sites/store', this.form)
            },
            cancel () {
                this.$emit('update:adding', false)
            }
        }
    }
</script>
