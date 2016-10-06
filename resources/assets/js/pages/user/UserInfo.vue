<style>
    input {
        color :black;
    }
</style>
<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <user-nav></user-nav>
            <form v-on:submit.prevent="onSubmit">
                <div class="form-group">
                    <label>Name</label>
                    <input name="name" type="name" v-model="form.name">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input name="email" type="email" v-model="form.email">
                </div>
                <section v-if="user.password">
                    <div class="form-group">
                        <label>New Password</label>
                        <input name="new_password" type="password">
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input name="confirm_password" type="password">
                    </div>
                </section>
                <button type="submit">Update Profile</button>
            </form>
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
                    name : user.name,
                    email : user.email,
                    new_password : null,
                    confirm_password : null
                }
            }
        },
        computed : {
            user : () => {
                return userStore.state.user;
            }
        },
        methods : {
            onSubmit: function() {
                Vue.http.put(this.action('User\UserController@update', { user : user.id }), this.form, {
                }).then((response) => {
                    userStore.state.user = response.data;
                }, (errors) => {
                    alert(error);
                });
            }
        }
    }
</script>