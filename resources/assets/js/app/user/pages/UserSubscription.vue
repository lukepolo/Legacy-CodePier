<template>
    <section>
        <form @submit.prevent="createToken" method="post" id="card-form">


            <template v-for="plan in plans">
                <div class="jcf-input-group input-radio">
                    <div class="input-question">Select your plan</div>
                    <input v-model="form.plan" type="radio" name="plan" :value="plan.id">
                    <span class="icon"></span>

                    <p>
                        {{ plan.name }} ({{ plan.amount }} / {{ plan.interval }}
                    </p>

                    <b v-if="plan.metadata.save">
                        SAVE {{ plan.metadata.save }} per {{ plan.interval }}
                    </b>
                </div>
            </template>

            <div class="flyform--group">
                <div id="card-element" class="margin-top-1 margin-bottom-1"></div>
                <div id="card-errors" class="alert alert-danger" role="alert" v-if="error">
                    {{ error }}
                </div>
            </div>

            <div class="flyform--footer">
                <div class="flyform--footer-btns">
                    <button class="btn btn-primary">Update Subscription</button>
                </div>
            </div>
        </form>
    </section>
</template>
<script>
    export default {
        data() {
            return {
                card : null,
                error : null,
                stripe : null,
                form: this.createForm({
                    plan: null,
                    token : null,
                })
            }
        },
        created() {
            this.$store.dispatch('subscriptions/plans');
            this.$store.dispatch('user_subscription/get');
            this.$store.dispatch('user_subscription/getInvoices');
            this.$store.dispatch('user_subscription/getUpcomingSubscription');
        },
        mounted() {
            this.stripe = Stripe('pk_test_qJVytxReNpHC00dFe8Eegy6Q');

            this.card = this.stripe.elements().create('card', {
                style : {
                    base: {
                        color: '#fff',
                        iconColor: '#fff',
                        lineHeight: '24px',
                        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                        fontSmoothing: 'antialiased',
                        fontSize: '16px',
                    },
                    invalid: {
                        color: '#F4645F',
                        iconColor: '#F4645F'
                    }
                }
            });

            this.card.mount('#card-element');

            this.card.addEventListener('change', (event) => {
                this.error = null
                if (event.error) {
                    this.error = event.error.message;
                }
            });
        },
        methods: {
            createToken() {
                this.stripe.createToken(this.card).then((result) => {
                    if (result.error) {
                        this.error = result.error.message;
                    } else {
                        this.updateSubscription(result.token);
                    }
                });
            },
            updateSubscription(token) {
                console.info(token)
                this.token = token
            },
        },
        computed: {
            plans() {
                let plans = this.$store.state.subscriptions.plans;
                if(this.validSubscription) {
                    let plan = _.find(plans, { id : this.user_subscription.stripe_plan});
                    if(plan) {
                        this.form.plan = plan.id
                    }
                }
                return plans;
            },
            invoices() {
                return this.$store.state.user_subscription.invoices;
            },
            user_subscription() {
                return this.$store.state.user_subscription.subscription
            },
            validSubscription() {
                return this.$store.state.user_subscription.valid_subscription;
            },
            upcomingSubscription() {
                return this.$store.state.user_subscription.upcoming_subscription;
            },
            isCanceled() {
                return this.user_subscription.ends_at !==  null;
            }
        },
    }
</script>

<!--return ;-->