<template>
    <section>

        <template v-if="userSubscription">

            <div class="alert alert-warning" v-if="userSubscription.trial_ends_at">
                You're currently on your free trial (ends {{ userSubscription.trial_ends_at }})
            </div>

            <div class="alert alert-info">
                You're currently subscribed to the <strong>{{ userSubscriptionData.subscriptionName }}</strong> plan, your next billing date is {{ userSubscriptionData.subscriptionEnds }}
            </div>
            <p>
                Your currently are paying {{ userSubscriptionData.subscriptionPrice }} per {{ userSubscriptionData.subscriptionInterval }}
            </p>

            <p v-if="isCanceled">
                Your subscription has been canceled and will end on {{ userSubscription.ends_at }}
            </p>
            <p v-else>
                <template v-if="upcomingSubscription">
                    Your next billing is on {{ upcomingSubscription.date }}
                </template>
            </p>
        </template>

        <form @submit.prevent="updateSubscription" method="post" id="card-form">

            Start your 5 day free trial!

            <template v-for="plan in plans">
                <div class="flyform--group-radio">
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
            </template>

            <div class="flyform--group" v-show="!card">
                <div id="card-element" class="margin-top-1 margin-bottom-1"></div>
                <div id="card-errors" class="alert alert-danger" role="alert" v-if="stripe.error">
                    {{ stripe.error }}
                </div>
            </div>

            <div class="flyform--footer">
                <div class="flyform--footer-btns">
                    <template v-if="processing">
                        WOOO
                    </template>
                    <button class="btn btn-primary">Update Subscription</button>
                </div>
            </div>
        </form>

        <a @click="cancelSubscription" v-if="!isCanceled">Cancel Subscription</a>

        <template v-if="invoices.length">
            <table>
                <tr v-for="invoice in invoices">
                    <td> {{ invoice.date }}</td>
                    <td> {{ invoice.total }}</td>
                    <td><a :href="downloadLink(invoice.id)">Download</a></td>
                </tr>
            </table>
        </template>
    </section>
</template>
<script>
    export default {
        data() {
            return {
                card : null,
                processing : false,
                form: this.createForm({
                    plan: null,
                    token : null,
                }),
                stripe : {
                    card : null,
                    error : null,
                    instance : null,
                }
            }
        },
        created() {
            this.$store.dispatch('subscriptions/plans');
            this.$store.dispatch('user_subscription/get');
            this.$store.dispatch('user_subscription/getInvoices');
            this.$store.dispatch('user_subscription/getUpcomingSubscription');
        },
        mounted() {
            this.stripe.instance = Stripe(window.Laravel.stripeKey);

            this.stripe.card = this.stripe.elements().create('card', {
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

            this.stripe.card.mount('#card-element');

            this.stripe.card.addEventListener('change', (event) => {
                this.stripe.error = null
                if (event.error) {
                    this.stripe.error = event.error.message;
                }
            });
        },
        methods: {
            updateSubscription() {
                this.processing = true;
                this.createToken().then(() => {
                    if(!this.stripe.error && this.form.token) {
                        this.$store.dispatch('user_subscription/store', this.form).then(() => {
                            this.$store.dispatch('user_subscription/getInvoices');
                            this.$store.dispatch('user_subscription/getUpcomingSubscription');
                            this.processing = false;
                        }).catch(() => {
                            this.processing = false;
                        })
                    } else {
                        this.processing = false;
                    }
                });
            },
            createToken() {
                return new Promise((resolve) => {
                    if(!this.form.token) {
                        this.stripe.instance.createToken(this.stripe.card).then((result) => {
                            if (result.error) {
                                this.stripe.error = result.error.message;
                            }
                            this.form.token = result.token.id
                            resolve();
                        });
                    } else {
                        resolve();
                    }
                })

            },
            cancelSubscription() {
                this.$store.dispatch('cancelSubscription', this.userSubscription.id);
            },
            downloadLink: function (invoice) {
                return this.action('User\Subscription\UserSubscriptionInvoiceController@show', { invoice: invoice });
            }
        },
        computed: {
            plans() {
                let plans = this.$store.state.subscriptions.plans;
                if(this.userSubscription) {
                    let plan = _.find(plans, { id : this.userSubscription.stripe_plan});
                    if(plan) {
                        this.form.plan = plan.id
                    }
                }
                return plans;
            },
            invoices() {
                return this.$store.state.user_subscription.invoices;
            },
            userSubscriptionData() {
                return this.$store.state.user_subscription.subscription
            },
            userSubscription() {
                if(this.userSubscriptionData) {
                    return this.userSubscriptionData.subscription
                }
            },
            upcomingSubscription() {
                return this.$store.state.user_subscription.upcoming_subscription;
            },
            isCanceled() {
                if(this.userSubscription) {
                    return this.userSubscription.ends_at !== null;
                }
                return false
            }
        },
    }
</script>