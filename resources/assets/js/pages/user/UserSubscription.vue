<template>
    <div>
        <user-nav></user-nav>
        <template v-if="validSubscription">
            <p v-if="isCanceled">
                Your subscription has been canceled and will end on {{ subscription.ends_at }}
            </p>
            <p v-else>
                <template v-if="upcomingSubscription">
                    Your next billing is on {{ upcomingSubscription.date }}
                </template>
            </p>
        </template>

        <form v-on:submit.prevent="onSubmit">
            <div class="radio" v-for="plan in plans">
                <label>
                    <template v-if="subscribedToPlan(plan.id)">
                        Current Plan -
                    </template>
                    <template v-else>
                        <input type="radio" name="plan" :value="plan.id">
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
                <input type="text" name="number">

                <label>Exp Month</label>
                <input type="text" name="exp_month">

                <label>Exp Year</label>
                <input type="text" name="exp_year">

                <label>CVC</label>
                <input type="password" name="cvc">
            </div>

            <button type="submit">Save Subscription</button>
        </form>

        <a v-on:click="cancelSubscription" v-if="validSubscription">Cancel Subscription</a>

        <template v-if="invoices">
            <table>
                <tr v-for="invoice in invoices">
                    <td> {{ invoice.date }}</td>
                    <td> {{ invoice.total }}</td>
                    <td><a href="#">Download</a></td>
                </tr>
            </table>
        </template>
    </div>
</template>
<script>
    import UserNav from './components/UserNav.vue';
    export default {
        components : {
            UserNav
        },
        data() {
            return {
                user: user,
                showCardForm: false,
                plans: [],
                subscription: null,
                upcomingSubscription: null,
                invoices: []
            }
        },
        computed: {
            validSubscription: function () {
                if (this.subscription != null) {
                    return true;
                }
                return false;
            },
            isCanceled: function () {
                return this.subscription.ends_at != null;
            }
        },
        methods: {
            subscribedToPlan: function (plan) {
                if (this.validSubscription && this.subscription.stripe_plan == plan) {
                    return true;
                }
                return false;
            },
            onSubmit: function () {
                Vue.http.post(this.action('User\Subscription\UserSubscriptionController@store'), this.getFormData(this.$el)).then((response) => {
                    this.ssh_keys.push(response.json());
                }, (errors) => {
                    alert(error);
                });
            },
            getSubscription() {
                Vue.http.get(this.action('User\Subscription\UserSubscriptionController@index')).then((response) => {
                    this.subscription = response.json();

                    if (this.validSubscription && !this.isCanceled) {
                        Vue.http.get(this.action('User\Subscription\UserSubscriptionUpcomingInvoiceController@index')).then((response) => {
                            this.upcomingSubscription = response.json();
                        }, (errors) => {
                            alert(error);
                        });
                    }
                }, (errors) => {
                    alert(error);
                })
            },
            cancelSubscription() {
                Vue.http.delete(this.action('User\Subscription\UserSubscriptionController@destroy', {subscription: this.subscription.id})).then((response) => {
                    this.getSubscription();
                }, (errors) => {
                    alert(error);
                });
            }
        },
        mounted () {
            Vue.http.get(this.action('SubscriptionController@index')).then((response) => {
                this.plans = response.json();
            }, (errors) => {
                alert(error);
            });

            this.getSubscription();
        }
    }
</script>