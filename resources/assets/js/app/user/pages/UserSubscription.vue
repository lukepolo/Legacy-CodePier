<template>
    <section>
        <template v-if="userSubscription">

            <div class="alert alert-warning" v-if="userSubscription.trial_ends_at">
                You're currently on your free trial (ends {{ parseDate(userSubscription.trial_ends_at).format('l') }})
            </div>

            <div class="alert alert-info">
                You're currently subscribed to the <strong>{{ userSubscriptionData.subscriptionName }}</strong> plan, your next billing date is {{ parseDate(userSubscriptionData.subscriptionEnds.date).format('l') }}
                @ {{ userSubscriptionData.subscriptionPrice }} per {{ userSubscriptionData.subscriptionInterval }}
            </div>

            <p v-if="isCanceled">
                Your subscription has been canceled and will end on {{ parseDate(userSubscription.ends_at).format('l') }}
            </p>
        </template>

        <form @submit.prevent="createSubscription" method="post">

            <template v-if="!userSubscription">
                Start your 5 day free trial!
            </template>

            <plans :selectedPlan.sync="form.plan"></plans>

            <card v-if="!userSubscription" cardType="createCardForm" :card.sync="createCardForm.card" :error.sync="createCardForm.error" :instance.sync="createCardForm.instance"></card>

            <div class="flyform--footer">
                <div class="flyform--footer-btns">
                    <button class="btn btn-primary" :class="{ 'btn-disabled' : processing }">
                        <template v-if="userSubscription">
                            Update
                        </template>
                        <template v-else>
                            Create
                        </template>
                        Subscription
                    </button>
                </div>
            </div>
        </form>

        <form @submit.prevent="updateCard" method="post" v-if="currentCard">

            Current Card : {{ currentCard.brand }} {{ currentCard.last4 }}

            <card cardType="updateCardForm" :card.sync="updateCardForm.card" :error.sync="updateCardForm.error" :instance.sync="updateCardForm.instance"></card>

            <div class="flyform--footer">
                <div class="flyform--footer-btns">
                    <button class="btn btn-primary" :class="{ 'btn-disabled' : processing }">Update Card</button>
                </div>
            </div>
        </form>

        <a @click="cancelSubscription" v-if="userSubscription && !isCanceled">Cancel Subscription</a>

        <template v-if="invoices.length">
            <table>
                <tr v-for="invoice in invoices">
                    <td> {{ parseDate(invoice.date.date).format('l') }}</td>
                    <td> ${{ invoice.total }}</td>
                    <td><a :href="downloadLink(invoice.id)">Download</a></td>
                </tr>
            </table>
        </template>
    </section>
</template>

<script>

    import Card from './../components/subscriptions/Card.vue'
    import Plans from './../components/subscriptions/Plans.vue'
    export default {
        components : {
            Card,
            Plans,
        },
        data() {
            return {
                card : null,
                processing : false,
                form: this.createForm({
                    plan: null,
                    token : null,
                    subscription : null,
                }),
                createCardForm : {
                    card : null,
                    error : null,
                    instance : null,
                },
                updateCardForm : {
                    card : null,
                    error : null,
                    instance : null,
                }
            }
        },
        watch : {
            userSubscription : function() {
                if(this.userSubscription) {
                    this.form.plan = this.userSubscription.stripe_plan
                    this.form.subscription = this.userSubscription.id
                }
            }
        },
        created() {
            this.$store.dispatch('subscriptions/plans');
            this.$store.dispatch('user_subscription/get');
            this.$store.dispatch('user_subscription/getInvoices');
        },
        methods: {
            updateCard() {
                this.processing = true;
                this.createToken(this.updateCardForm).then(() => {
                    if(!this.updateCardForm.error && this.form.token) {
                        this.$store.dispatch('user_subscription/updateCard', this.form).then(() => {
                            this.processing = false;
                        }).catch(() => {
                            this.processing = false;
                        })
                    } else {
                        this.processing = false;
                    }
                });
            },
            createSubscription() {

                this.processing = true;

                if(this.userSubscription) {
                    return this.updateSubscription()
                }

                this.createToken(this.createCardForm).then(() => {
                    if(!this.createCardForm.error && this.form.token) {
                        this.$store.dispatch('user_subscription/store', this.form).then(() => {
                            this.$store.dispatch('user/get').then(() => {
                                this.$router.push('/')
                            })
                        }).catch(() => {
                            this.processing = false;
                        })
                    } else {
                        this.processing = false;
                    }
                });
            },
            updateSubscription() {
                this.$store.dispatch('user_subscription/patch', this.form).then(() => {
                    this.$store.dispatch('user_subscription/getInvoices');
                    this.processing = false;
                }).catch(() => {
                    this.processing = false;
                })
            },
            createToken(cardForm) {
                return new Promise((resolve) => {
                    if(!this.form.token) {
                        cardForm.instance.createToken(cardForm.card).then((result) => {
                            if (result.error) {
                                cardForm.error = result.error.message;
                            }
                            this.form.token = result.token.id
                            resolve();
                        }).catch(() => {
                            resolve();
                        });
                    } else {
                        resolve();
                    }
                })
            },
            cancelSubscription() {
                this.$store.dispatch('user_subscription/cancel', this.userSubscription.id);
            },
            downloadLink: function (invoice) {
                return '/subscription/invoices/'+invoice;
            }
        },
        computed: {
            invoices() {
                return this.$store.state.user_subscription.invoices;
            },
            isCanceled() {
                if(this.userSubscription) {
                    return this.userSubscription.ends_at !== null;
                }
                return false
            },
            currentCard() {
                if(this.userSubscriptionData) {
                    return this.userSubscriptionData.card
                }
            },
            userSubscription() {
                if(this.userSubscriptionData) {
                    return this.userSubscriptionData.subscription
                }
            },
            userSubscriptionData() {
                return this.$store.state.user_subscription.subscription
            },
        },
    }
</script>