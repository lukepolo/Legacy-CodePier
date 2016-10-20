<template>
    <section>
        <p>Select Servers</p>
        <div class="form-group" v-for="server in servers" v-if="servers">
            <template v-if="hasFeature(server)">
                <input type="checkbox" :value="server.id" v-model="selected_servers" name="selected_servers[]">
            </template>
            <template v-else>
                {{ feature_message }}
            </template>
            {{ server.ip }}
        </div>
    </section>
</template>
<script>
    export default {
        props: ['servers', 'param', 'feature', 'feature_message'],
        data() {
            return {
                selected_servers : []
            }
        },
        methods : {
            hasFeature : function(server) {
                if(this.feature) {
                    return this.serverHasFeature(server, this.feature);
                }

                return true;
            }
        },
        watch : {
            'selected_servers' () {
                _.set(this.$parent, this.param, this.selected_servers);
            }
        },
        mounted () {
            this.selected_servers = _.get(this.$parent, this.param);
        }
    }
</script>