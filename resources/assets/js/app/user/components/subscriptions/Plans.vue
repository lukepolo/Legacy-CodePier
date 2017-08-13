<template>
    <div>
        <div class="flyform--group-radio" v-for="plan in plans">
            <label>
                <input v-model="form.plan" type="radio" name="plan" :value="plan.id">
                <span class="icon"></span>
                <p>
                    {{ plan.name }} (${{ (plan.amount/ 100) }} / {{ plan.interval }})
                </p>

                <b v-if="plan.metadata.save">
                    SAVE ${{ plan.metadata.save }}.00 per {{ plan.interval }}
                </b>
            </label>
        </div>
    </div>
</template>

<script>
    export default {
        props : ['selectedPlan'],
        data() {
            return {
                form : {
                    plan : null
                }
            }
        },
        watch : {
            'selectedPlan' : function() {
                Vue.set(this.form, 'plan', this.selectedPlan)
            },
            'form.plan' : function() {
                this.$emit('update:selectedPlan', this.form.plan)
            }
        },
        computed: {
            plans() {
                return this.$store.state.subscriptions.plans;
            },
        }
    }
</script>