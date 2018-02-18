<template>
    <div>
        <div class="flex" v-if="!workFlowCompleted">
            <div class="flex--grow">
                <h2>Choose Repository</h2>
                <h4 class="secondary">&nbsp;</h4>
            </div>

            <div class="heading--btns">
                <delete-site :site="site"></delete-site>
            </div>
        </div>

        <hr v-if="!workFlowCompleted">

        <div class="providers grid-4">
            <label v-for="repository_provider in repository_providers">
                <template v-if="isConnected(repository_provider.id)">
                    <input
                            name="user_repository_provider_id"
                            type="radio"
                            v-model="user_provider"
                            :value="isConnected(repository_provider.id).id"
                    >
                </template>

                <div class="providers--item"
                     @click="!isConnected(repository_provider.id) ? registerProvider(repository_provider.provider_name) : null">
                    <div class="providers--item-header">
                        <div class="providers--item-icon"><span
                                :class="'icon-' + repository_provider.name.toLowerCase()"></span></div>
                        <div class="providers--item-name"> {{ repository_provider.name}}</div>
                    </div>
                    <div class="providers--item-footer">
                        <template v-if="isConnected(repository_provider.id)">
                            <div class="providers--item-footer-connected"><h4><span class="icon-check_circle"></span>
                                Select</h4></div>
                        </template>
                        <template v-else>
                            <div class="providers--item-footer-connect"><h4><span class="icon-link"></span> Connect
                                Account</h4></div>
                        </template>
                    </div>
                </div>
            </label>
            <label>
                <slot></slot>
            </label>
        </div>
    </div>
</template>

<script>
    import DeleteSite from "./../components/DeleteSite";

    export default {
        props: {
            provider: {
                default: null
            }
        },
        components : {
            DeleteSite
        },
        data() {
            return {
                user_provider: this.provider
            };
        },
        created() {
            this.$store.dispatch("repository_providers/get");
        },
        watch: {
            provider: function (provider) {
                this.user_provider = provider;
            },
            user_provider: function (provider) {
                this.$emit("update:provider", provider);
            }
        },
        methods: {
            isConnected: function (repository_provider_id) {
                return _.find(this.user_repository_providers, {
                    repository_provider_id: parseInt(repository_provider_id)
                });
            },
            disconnectProvider: function (repository_provider_id) {
                let repository_provider = _.find(this.user_repository_providers, function (repository_provider) {
                    return (
                        repository_provider.repository_provider_id === repository_provider_id
                    );
                }).id;

                this.$store.dispatch("user_repository_providers/destroy", {
                    user: this.$store.state.user.user.id,
                    repository_provider: repository_provider
                });
            },
            registerProvider(provider) {
                window.location.replace(
                    this.action("AuthOauthController@newProvider", {
                        provider: provider
                    })
                );
            }
        },
        computed: {
            repository_providers() {
                return this.$store.state.repository_providers.providers;
            },
            user_repository_providers() {
                return this.$store.state.user_repository_providers.providers;
            },
            site() {
                return this.$store.state.user_sites.site;
            },
        }
    };
</script>
