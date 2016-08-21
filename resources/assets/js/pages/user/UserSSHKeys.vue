<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <user-nav></user-nav>
            <form v-on:submit.prevent="onSubmit">
                <div class="form-group">
                    <label>Name</label>
                    <input name="name" type="text" class="form-control">
                </div>
                <div class="form-group">
                    <label>Public Key</label>
                    <textarea name="ssh_key" class="form-control"></textarea>
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
                    <tr v-for="key in ssh_keys">
                        <td>{{ key.name }}</td>
                        <td><a href="#" class="fa fa-remove">x</a></td>
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
                ssh_keys : []
            }
        },
        methods : {
            onSubmit: function() {
                Vue.http.post(this.action('User\Features\UserSshKeyController@store'), this.getFormData(this.$el)).then((response) => {
                   this.ssh_keys.push(response.json());
                }, (errors) => {
                    alert(error);
                });
            }
        },
        mounted () {
            Vue.http.get(this.action('User\Features\UserSshKeyController@index')).then((response) => {
                this.ssh_keys = response.json();
            }, (errors) => {
                alert(error);
            })
        },
    }
</script>