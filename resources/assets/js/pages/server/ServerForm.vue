<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column" >
            <h3 class="section-header primary">Create New Server</h3>

            <div class="section-content">
                <div class="container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#basic" aria-controls="home" role="tab" data-toggle="tab">Basic Server</a></li>
                        <li role="presentation"><a href="#load-balancer" aria-controls="profile" role="tab" data-toggle="tab">Load Balancer</a></li>
                    </ul>

                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="basic">
                            <div class="jcf-form-wrap">

                                <form v-on:submit.prevent="onSubmit" class="validation-form floating-labels">
                                    <input type="hidden" name="pile_id" :value="pile.id">
                                    <div class="input-group input-radio">
                                        <div class="input-question">Server Provider</div>
                                        <template v-for="user_server_provider in user_server_providers">
                                            <label>
                                                <input type="radio" name="server_provider_id" :value="user_server_provider.server_provider_id">
                                                <span class="icon"></span>{{ user_server_provider.server_provider.provider_name }}
                                            </label>
                                        </template>
                                    </div>

                                    <div class="input-group">
                                        <input type="text" id="name" name="name" required>
                                        <label for="name"><span class="float-label">Name</span></label>
                                    </div>

                                    <div class="input-group">
                                        <div class="input-question">Server Option</div>

                                        <select name="server_option">
                                            <option v-for="option in options" :value="option.id">{{ option.memory }} MB RAM - {{ option.cpus }} CPUS - {{ option.space }} SSD - ${{ option.priceHourly }} / Hour - ${{ option.priceMonthly }} / Month </option>
                                        </select>
                                    </div>

                                    <div class="input-group">
                                        <div class="input-question">Server Region</div>

                                        <select name="server_region">
                                            <option v-for="region in regions" :value="region.id">{{ region.name }}</option>
                                        </select>
                                    </div>

                                    <div class="input-group input-checkbox">
                                        <div class="input-question">Server Options</div>
                                        <template v-for="feature in features">
                                            <label>
                                                <input type="checkbox" name="features[]" :value="feature.id">
                                                <span class="icon"></span>{{ 'Enable ' + feature.feature }} <small>{{ feature.cost }}</small>
                                            </label>
                                        </template>
                                    </div>
                                    <div class="btn-footer">
                                        <button class="btn">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Create Server</button>
                                    </div>
                                </form>
                            </div><!-- end form-wrap -->

                        </div>
                        <div role="tabpanel" class="tab-pane" id="load-balancer">...</div>
                    </div>
                </div>
            </div>
        </section>
    </section>
</template>

<script>
    import LeftNav from './../../core/LeftNav.vue';
    export default {
        components : {
            LeftNav
        },
        data() {
            return {
                options : null,
                regions : null,
                features : null,
                user_server_providers: null
            }
        },
        computed : {
            pile : function() {
                var pile = _.find(user.piles, function(pile) {
                    return pile.id == localStorage.getItem('current_pile_id');
                });

                if(pile) {
                    return pile;
                }

                return {
                    id : null
                }
            }
        },
        methods : {
            onSubmit: function() {
                Vue.http.post(this.action('Server\ServerController@store'), this.getFormData($(this.$el))).then((response) => {
                    window.location = '/';
                }, (errors) => {
                    alert(error);
                });
            }
        },
        mounted() {
            Vue.http.get(this.action('Server\Providers\DigitalOcean\DigitalOceanServerOptionsController@index')).then((response) => {
                this.options = response.json();
            }, (errors) => {
                alert(error);
            });

            Vue.http.get(this.action('Server\Providers\DigitalOcean\DigitalOceanServerRegionsController@index')).then((response) => {
                this.regions = response.json();
            }, (errors) => {
                alert(error);
            });

            Vue.http.get(this.action('Server\Providers\DigitalOcean\DigitalOceanServerFeaturesController@index')).then((response) => {
                this.features = response.json();
            }, (errors) => {
                alert(error);
            });

            Vue.http.get(this.action('User\Providers\UserServerProviderController@index')).then((response) => {
                this.user_server_providers = response.json();
            }, (errors) => {
                alert(error);
            });
        }
    }
</script>