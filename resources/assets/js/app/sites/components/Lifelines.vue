<template>
    <section>

        <form @submit.prevent="createLifeline">
            Name : <input type="text" v-model="form.name">
            <br>
            Should check-in every <input type="text" v-model="form.threshold"> minutes
            <br>
            <button type="submit">Create Life Line</button>
        </form>

        <p v-for="lifeLine in lifeLines">
           <life-line :lifeLine="lifeLine" :site="site"></life-line>
        </p>
    </section>
</template>

<script>
    import LifeLine from './Lifeline.vue'
    export default {
        components : {
            LifeLine
        },
        data() {
            return {
                form: this.createForm({
                    name : null,
                    threshold : null,
                    site : this.$route.params.site_id
                })
            }
        },
        created() {
            this.$store.dispatch('user_site_life_lines/get', this.$route.params.site_id)
        },
        methods : {
            createLifeline() {
                this.$store.dispatch('user_site_life_lines/store', this.form)
            }
        },
        computed : {
            site() {
                return this.$store.state.user_sites.site
            },
            lifeLines() {
                return this.$store.state.user_site_life_lines.life_lines
            },
        }
    }
</script>