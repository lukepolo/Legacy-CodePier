<template>
    <section>
        <p v-for="repository_provider in repository_providers">

            <label>
                <template v-if="isConnected(repository_provider.id)">

                    <input
                        class="btn"
                        name="user_repository_provider_id"
                        type="radio"
                        v-model="user_provider"
                        :value="isConnected(repository_provider.id).id"
                    >
                    <span class="icon"></span>
                    {{ repository_provider.provider_name }}

                </template>

                <template v-else>
                    Integrate :
                    <a
                        :href="action('Auth\OauthController@newProvider', {
                            provider : repository_provider.provider_name
                        })"
                        class="btn btn-default"
                    >
                        {{ repository_provider.name}}
                    </a>
                </template>

            </label>

        </p>
    </section>
</template>

<script>
export default {
    props : {
        provider : {
            default : null
        }
    },
    data() {
        return {
            user_provider : this.provider
        }
    },
    created() {
        this.$store.dispatch('repository_providers/get')
    },
    watch : {
        'provider' : function (provider) {
            this.user_provider = provider
        },
        'user_provider' : function(provider) {
            this.$emit('update:provider', provider)
        }
    },
    methods: {
        isConnected: function (repository_provider_id) {
            return _.find(this.user_repository_providers, {'repository_provider_id': repository_provider_id})
        },
        disconnectProvider: function (repository_provider_id) {

            let repository_provider = _.find(this.user_repository_providers, function (repository_provider) {
                return repository_provider.repository_provider_id === repository_provider_id
            }).id

            this.$store.dispatch('user_repository_providers/destroy', {
                user: this.$store.state.user.user.id,
                repository_provider: repository_provider
            })

        },
        user_repository_providers() {
            return this.$store.state.user_repository_providers.providers
        },
    },
    computed: {
        repository_providers() {
            return this.$store.state.repository_providers.providers
        },
        user_repository_providers() {
            return this.$store.state.user_repository_providers.providers
        }
    },
}
</script>