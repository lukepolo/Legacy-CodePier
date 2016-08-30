<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <user-nav></user-nav>
            <template v-if="validSubscription">
                <p v-if="isCanceled">
                    Your subscription has been canceled and will end on {{ user_subscription.ends_at }}
                </p>
                <p v-else>
                    <template v-if="!_.isEmpty(upcomingSubscription)">
                        Your next billing is on {{ upcomingSubscription.date }}
                    </template>
                </p>
            </template>

            <form v-on:submit.prevent="createSubscription">
                <div class="radio" v-for="plan in plans">
                    <label>
                        <template v-if="subscribedToPlan(plan.id)">
                            Current Plan -
                        </template>
                        <template v-else>
                            <input v-model="form.plan" type="radio" name="plan" :value="plan.id">
                        </template>

                        <p>
                            {{ plan.name }} ({{ plan.amount }} / {{ plan.interval }}
                        </p>

                        <b v-if="plan.metadata.save">
                            SAVE {{ plan.metadata.save }} per {{ plan.interval }}
                        </b>
                    </label>
                </div>

                <template v-if="user.card_brand">
                    Use your {{ user.card_brand }} {{ user.card_last_four }}
                    <div v-on:click="showCardForm = !showCardForm" class="btn btn-link new-card">new card</div>
                </template>

                <div id="card-info" :class="{hide : !showCardForm}">
                    <label>Number</label>
                    <input v-model="form.number" type="text" name="number">

                    <label>Exp Month</label>
                    <input v-model="form.exp_month" type="text" name="exp_month">

                    <label>Exp Year</label>
                    <input v-model="form.exp_year" type="text" name="exp_year">

                    <label>CVC</label>
                    <input v-model="form.cvc" type="password" name="cvc">
                </div>

                <button type="submit">Save Subscription</button>
            </form>

            <a v-on:click="cancelSubscription" v-if="validSubscription && !isCanceled">Cancel Subscription</a>

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
    </section>
</template>
<script>
    import UserNav from './components/UserNav.vue';
    import LeftNav from './../../core/LeftNav.vue';
    export default {
        components : {
            LeftNav,
            UserNav
        },
        data() {
            return {
                plans: [],
                showCardForm: user.card_brand ? false : true,
                form : {
                    cvc : null,
                    plan : null,
                    number : null,
                    exp_year: null,
                    exp_month : null
                }
            }
        },
        created() {
            this.fetchData();
        },
        computed: {
            user: () => {
                return userStore.state.user;
            },
            plans : () => {
                return subscriptionStore.state.plans;
            },
            invoices : () => {
                return userSubscriptionStore.state.user_invoices;
            },
            user_subscription : () => {
                return userSubscriptionStore.state.user_subscription;
            },
            validSubscription: function () {
                return userSubscriptionStore.state.valid_subscription;
            },
            upcomingSubscription : function() {
                return userSubscriptionStore.state.user_upcoming_subscription;
            },
            isCanceled: function () {
                return this.user_subscription.ends_at != null;
            }
        },
        methods: {
            fetchData : function() {
                subscriptionStore.dispatch('getPlans');
                userSubscriptionStore.dispatch('getUserInvoices');
                userSubscriptionStore.dispatch('getUserSubscription');
            },
            subscribedToPlan: function (plan) {
                if (this.validSubscription && this.user_subscription.stripe_plan == plan) {
                    return true;
                }
                return false;
            },
            createSubscription: function () {
                userSubscriptionStore.dispatch('createUserSubscription', this.form);
            },
            cancelSubscription : function() {
                userSubscriptionStore.dispatch('cancelSubscription', this.user_subscription.id);
            },
            downloadLink : function(invoice_id) {
                return this.action('User\Subscription\UserSubscriptionInvoiceController@show', {invoice : invoice_id});
            }
        }
    }
</script>