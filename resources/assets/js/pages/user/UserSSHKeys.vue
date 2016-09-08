<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <user-nav></user-nav>
            <form v-on:submit.prevent="createSshkey">
                <div class="form-group">
                    <label>Name</label>
                    <input v-model="form.name" name="name" type="text" class="form-control">
                </div>
                <div class="form-group">
                    <label>Public Key</label>
                    <textarea v-model="form.ssh_key" name="ssh_key" class="form-control"></textarea>
                </div>
                <input type="submit" value="Install SSH Key">
            </form>
            <table class="table">
                <thead>
                <tr>
                    <th>Key Name</th>
                    <th>key info</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    <tr v-for="key in user_ssh_keys">
                        <td>{{ key.name }}</td>
                        <td><a @click.prevent="deleteSshKey(key.id)" class="fa fa-remove"></a></td>
                    </tr>
                </tbody>
            </table>
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
                form : {
                    name : null,
                    ssh_key : null
                }
            }
        },
        created () {
            this.fetchData();
        },
        methods : {
            fetchData : function() {
                userSshKeyStore.dispatch('getUserSshKeys');
            },
            createSshkey: function() {
                userSshKeyStore.dispatch('createUserSshKey', this.form).then(() => {
                    this.form = this.$options.data().form;
                });
            },
            deleteSshKey : function(sshKeyId) {
                userSshKeyStore.dispatch('deleteUserSshKey', sshKeyId);
            }
        },
        computed : {
            user_ssh_keys : function() {
                return userSshKeyStore.state.user_ssh_keys;
            }
        },
    }
</script>