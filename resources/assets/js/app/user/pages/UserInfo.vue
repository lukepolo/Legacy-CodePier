<template>
    <section>
        <div class="jcf-form-wrap">

            <form @submit.prevent="onSubmit" class="floating-labels">

                <div class="jcf-input-group">
                    <input name="name" type="text" v-model="form.name">
                    <label for="name">
                        <span class="float-label">Name</span>
                    </label>
                </div>

                <div class="jcf-input-group">
                    <input name="email" type="email" v-model="form.email">
                    <label for="email">
                        <span class="float-label">Email</span>
                    </label>
                </div>

                <section v-if="user.password">

                    <div class="jcf-input-group">
                        <input name="new_password" type="password">
                        <label for="new_password">
                            <span class="float-label">New Password</span>
                        </label>
                    </div>
                    <div class="jcf-input-group">
                        <input name="confirm_password" type="password">
                        <label for="confirm_password">
                            <span class="float-label">Confirm Password</span>
                        </label>
                    </div>
                </section>

                <div class="jcf-input-group input-checkbox">
                    <div class="input-question">Workflows </div>
                    <label>
                        <input v-model="form.workflow" name="workflow" type="checkbox">
                        <span class="icon"></span> Enabled
                    </label>
                </div>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Update Profile</button>
                </div>

            </form>
        </div>
    </section>

</template>

<script>
    export default {
        data() {
            return {
                form: {
                    user : user.id,
                    name: null,
                    email: null,
                    new_password: null,
                    confirm_password: null,
                    workflow : null,
                }
            }
        },
        watch: {
            'user' : 'setData'
        },
        created() {
            this.setData()
        },
        computed: {
            user() {
                return this.$store.state.user.user;
            }
        },
        methods: {
            setData() {
                  console.info('set seom data')
                this.form.name = this.user.name
                this.form.email = this.user.email
                this.form.workflow = this.user.workflow
                console.info(this.user)

            },
            onSubmit() {

                this.form.user_id = this.user.id;

                this.$store.dispatch('user/update', this.form)
            }
        }
    }
</script>