<template>
   <section>
       <div class="providers grid-4">
           <label v-for="provider in notification_providers">
               <div class="providers--item" @click="isConnected(provider.id) ? disconnectProvider(provider.id) : connectProvider(provider)">
                   <div class="providers--item-header">
                       <div class="providers--item-icon"><span :class="'icon-' + provider.name.toLowerCase()"></span></div>
                       <div class="providers--item-name">{{provider.name}}</div>
                   </div>
                   <div class="providers--item-footer">
                       <template v-if="isConnected(provider.id)">
                           <div class="providers--item-footer-disconnect"><h4><span class="icon-check_circle"></span> Disconnect</h4></div>
                       </template>
                       <template v-else>
                           <div class="providers--item-footer-connect">
                               <h4><span class="icon-link"></span> Connect Account</h4>
                           </div>
                       </template>
                   </div>
               </div>
           </label>
       </div>

       <div v-if="notification_settings">
           <form class="settings" @submit.prevent="updateUserNotifications" ref="notification_settings_form">
               <notification-group title="Site Deployment Notifications" :notification_settings="notification_settings['site_deployment']"></notification-group>
               <notification-group title="LifeLines Notifications" :notification_settings="notification_settings['lifelines']"></notification-group>
               <notification-group title="Server Monitoring Notifications" :notification_settings="notification_settings['server_monitoring']"></notification-group>
               <notification-group title="Servers Notifications" :notification_settings="notification_settings['servers']"></notification-group>
               <notification-group title="Buoys Notifications" :notification_settings="notification_settings['buoys']"></notification-group>
           </form>
           <div class="flyform--footer">
               <div class="flyform--footer-btns">
                   <button class="btn btn-primary" type="submit">Update Settings</button>
               </div>
           </div>
       </div>
   </section>

</template>

<script>
    import NotificationGroup from './../components/NotificationGroup'
    export default {
        components : {
            NotificationGroup
        },
        computed: {
            notification_settings() {
                return _.groupBy(this.$store.state.notification_settings.settings, 'group')
            },
            notification_providers() {
                return this.$store.state.notification_providers.providers
            },
            user_notification_providers() {
                return this.$store.state.user_notification_providers.providers
            },
        },
        methods: {
            connectProvider(provider) {
                window.location.replace(
                    this.action('Auth\OauthController@newProvider', { provider : provider.provider_name})
                )
            },
            isConnected: function (notification_provider_id) {
                if (_.find(this.user_notification_providers, {'notification_provider_id': notification_provider_id})) {
                    return true
                }
                return false
            },
            disconnectProvider: function (notification_provider_id) {
                let notification_provider = _.find(this.user_notification_providers, function (notification_provider) {
                    return notification_provider.notification_provider_id === notification_provider_id
                }).id

                this.$store.dispatch('user_notification_providers/destroy', {
                    user: this.$store.state.user.user.id,
                    notification_provider: notification_provider
                })
            },
            updateUserNotifications() {
                this.$store.dispatch('user_notification_settings/update', this.getFormData(this.$refs.notification_settings_form))
            }
        },
        created() {
            this.$store.dispatch('notification_settings/get')
            this.$store.dispatch('notification_providers/get')
            this.$store.dispatch('user_notification_settings/get')
        },
    }
</script>