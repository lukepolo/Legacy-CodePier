<style>
    input {
        color :black;
    }
</style>
<template>
    <div>
        <profile-nav></profile-nav>
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
    import ProfileNav from './components/ProfileNav.vue';
    export default {
        components : {
            ProfileNav
        },
        data() {
            return {
                user: user
            }
        },
        methods : {
            onSubmit: function() {
                Vue.http.put(laroute.action('User\UserController@update'), this.getFormData(this.$el), {
                }).then((response) => {
                    // TOOD - we need to find a better way
                    // this.someObject = Object.assign({}, this.someObject, { a: 1, b: 2 }) works in 1.0 but 2.0 nope
                    vue.user.name = response.json().name;
                }, (errors) => {
                    alert(error);
                });
            }
        }
    }
</script>