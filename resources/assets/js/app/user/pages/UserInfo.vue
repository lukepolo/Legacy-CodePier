<template>
    <form @submit.prevent="updateUser">

        <div class="flyform--group">
            <input name="name" type="text" v-model="form.name" placeholder=" ">
            <label for="name">Name</label>
        </div>

        <div class="flyform--group">
            <input name="email" type="email" v-model="form.email" placeholder=" ">
            <label for="email">Email</label>
        </div>

        <section v-if="user.password">
            <div class="flyform--group">
                <input name="new_password" type="password" placeholder=" ">
                <label for="new_password">New Password</label>
            </div>
            <div class="flyform--group">
                <input name="confirm_password" type="password" placeholder=" ">
                <label for="confirm_password">Confirm Password</label>
            </div>
        </section>

        <div class="flyform--group">
            <label>Workflows</label>
            <small>these help you build your site via tutorial style (recommended)</small>
        </div>

        <div class="flyform--group-checkbox">
            <label>
                <input v-model="form.workflow" name="workflow" type="checkbox">
                <span class="icon"></span>
                Enable
            </label>
        </div>

        <div class="flyform--footer">
            <div class="flyform--footer-btns">
                <button class="btn btn-primary" type="submit">Update Profile</button>
            </div>
            <div class="flyform--footer-links">
                <template v-if="user.second_auth_active">
                    <a @click="deactivateSecondAuth" class="text-error">Deactivate Second Authentication</a>
                </template>
                <template v-else>
                    <template v-if="secondAuthImage">
                        <br>

                        <div class="grid-10">
                            <div class="span-1">

                            </div>

                            <div class="span-3">
                                <br>
                                <img :src="secondAuthImage">
                            </div>


                            <form @submit.prevent="validateSecondAuth" class="span-5">
                                <div class="flex flex--baseline">
                                    <div class="flyform--group flex--grow">
                                        <input type="text" :value="secondAuthSecret" readonly placeholder=" ">
                                        <label>Secret</label>
                                    </div>
                                    <div class="flex--spacing">
                                        <tooltip message="Copy to Clipboard" placement="top">
                                            <clipboard :data="secondAuthSecret"></clipboard>
                                        </tooltip>
                                    </div>
                                </div>

                                <div class="flex flex--baseline">
                                    <div class="flyform--group flex--grow">
                                        <input type="text" v-model="token" placeholder=" ">
                                        <label>Token</label>
                                    </div>

                                    <div class="flex--spacing">
                                        <button class="btn btn-small btn-primary" type="submit">Validate</button>
                                    </div>
                                </div>

                            </form>
                        </div>


                    </template>
                    <template v-else>
                        <a @click="activateSecondAuth" class="text-success">Set Up Second Authentication</a>
                    </template>
                </template>
            </div>
        </div>
    </form>
</template>

<script>
    export default {
        data() {
            return {
                form: this.createForm({
                    user : user.id,
                    name: null,
                    email: null,
                    new_password: null,
                    confirm_password: null,
                    workflow : null,
                    second_auth_active : null
                }),
                token : null,
                secondAuthImage : null,
                secondAuthSecret : null
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
                this.form.name = this.user.name
                this.form.email = this.user.email
                this.form.workflow = this.user.workflow
                this.form.second_auth_active = this.user.second_auth_active
            },
            updateUser() {
                this.form.user_id = this.user.id
                this.$store.dispatch('user/update', this.form)
            },
            validateSecondAuth() {
                this.$store.dispatch('auth/validateSecondAuth', this.token)
            },
            activateSecondAuth() {
                this.$store.dispatch('auth/getSecondAuthQr').then((secondAuth) => {
                    this.secondAuthImage = secondAuth.image
                    this.secondAuthSecret = secondAuth.secret
                })
            },
            deactivateSecondAuth() {
                this.form.second_auth_active = false
                this.updateUser()
            }
        }
    }
</script>