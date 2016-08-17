<style>
    input {
        color :black;
    }
</style>
<template>
    <div>
        <user-nav></user-nav>
        <form v-on:submit.prevent="onSubmit">
            <div class="form-group">
                <label>Name</label>
                <input name="name" type="name" :value="user.name">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input name="email" type="email" :value="user.email">
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
    </div>
</template>

<script>
    import UserNav from './components/UserNav.vue';
    export default {
        components : {
            UserNav
        },
        data() {
            return {
                user: userStore.state.user
            }
        },
        methods : {
            onSubmit: function() {
                Vue.http.put(this.action('User\UserController@update', { user : user.id }), this.getFormData(this.$el), {
                }).then((response) => {
                    userStore.state.user = response.json();
                }, (errors) => {
                    alert(error);
                });
            }
        }
    }
</script>