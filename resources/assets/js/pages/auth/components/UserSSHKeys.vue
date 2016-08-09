<template>
    <div>
        <form v-on:submit.prevent="onSubmit">
            <div class="form-group">
                <label>Name</label>
                <input name="name" type="text" class="form-control">
            </div>
            <div class="form-group">
                <label>Public Key0</label>
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
                    <td>{{ key.ssh_key }}</td>
                    <td><a href="#" class="fa fa-remove">x</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                ssh_keys : []
            }
        },
        methods : {
            onSubmit: function() {
                Vue.http.post(laroute.action('User\Features\UserSshKeyController@store'), this.getFormData(this.$el)).then((response) => {
                   this.ssh_keys.push(response.json());
                }, (errors) => {
                    alert(error);
                });
            }
        },
        mounted () {
            Vue.http.get(laroute.action('User\Features\UserSshKeyController@index')).then((response) => {
                this.ssh_keys = response.json();
            }, (errors) => {
                alert(error);
            })
        },
    }
</script>