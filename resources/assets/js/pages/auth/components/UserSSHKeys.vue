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
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>key nam here</td>
                <td><a href="#" class="fa fa-remove"> Auth\UserController@getRemoveSshKey </a></td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        props : ['user'],
        data() {
            return {
                ssh_keys : []
            }
        },
        methods : {
            onSubmit: function() {

                Vue.http.post(laroute.action('Auth\UserController@postAddSshKey'), this.getFormData(this.$el), {
                }).then((response) => {
                    // TOOD - we need to find a better way
                    // this.someObject = Object.assign({}, this.someObject, { a: 1, b: 2 }) works in 1.0 but 2.0 nope
                    vue.user.name = response.json().name;
                }, (errors) => {
                    alert(error);
                });
            }
        },
        mounted () {
            console.info('get ssh keys');
        },
    }
</script>