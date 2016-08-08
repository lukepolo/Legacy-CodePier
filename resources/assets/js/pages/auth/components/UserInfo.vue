<style>
    input {
        color :black;
    }
</style>
<template>
    <form v-on:submit.prevent="onSubmit">
        <div class="form-group">
            <label>Name</label>
            <input name="name" type="name" :value="user.name">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" :value="user.email">
        </div>
        <div class="form-group">
            <label>New Password</label>
            <input name="new-password" type="password">
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input name="confirm-password" type="password">
        </div>
        <button type="submit">Update Profile</button>
    </form>
</template>

<script>
    export default {
        props : ['user'],
        methods : {
            onSubmit: function() {
                Vue.http.post(laroute.action('Auth\UserController@postMyProfile'), {
                    name : 'Luke Policinski',
                    email : 'luke@lukepolo.com'
                }).then((response) => {
                    Vue.set('user', response.data);
                }, (errors) => {
                    alert(error);
                });
            }
        }
    }
</script>