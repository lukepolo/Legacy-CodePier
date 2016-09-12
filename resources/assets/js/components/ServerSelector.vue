<template>
    <section>
        <p>Select Servers</p>
        <div class="form-group" v-for="server in servers" v-if="servers">
            <input type="checkbox" :value="server.id" v-model="selected_servers" name="selected_servers[]"> {{ server.ip }}
        </div>
    </section>
</template>
<script>
    export default {
        props: ['servers', 'param'],
        data() {
            return {
                selected_servers : []
            }
        },
        watch : {
            'selected_servers' : function() {
                _.set(this.$parent, this.param, this.selected_servers);
            }
        },
        mounted : function() {
            this.selected_servers = _.get(this.$parent, this.param);
        }
    }
</script>