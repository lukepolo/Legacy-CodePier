<template>
    <div class="grid--item">
        <h3 class="heading text-center">Lifelines</h3>

        <life-line v-for="lifeLine in lifeLines" :key="lifeLine.id" :lifeLine="lifeLine" :site="site"></life-line>

        <template v-if="!showLifelineForm">
            <hr style="margin-top: 40px">
            <form @submit.prevent="createLifeline">
                <div class="flyform--group">
                    <input type="text" v-model="form.name" placeholder=" ">
                    <label>Lifeline Name</label>
                </div>

                <div class="flyform--group">
                    <label>Lifeline should check in every</label>
                    <div class="flyform--group-postfix">
                        <input type="number" v-model="form.threshold" placeholder=" ">
                        <label></label>
                        <template>
                            <div class="flyform--group-postfix-label">
                                minutes
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flyform-footer">
                    <div class="flyform--footer-btns">
                        <span class="btn btn-small" @click="showLifelineForm= !showLifelineForm">Cancel</span>
                        <button type="submit" class="btn btn-small btn-primary">Create Lifeline</button>
                    </div>
                </div>
            </form>
        </template>

        <template v-else>
            <div class="flyform--footer">
                <div class="flyform--footer-btns">
                    <span class="btn btn-primary" @click="showLifelineForm= !showLifelineForm">Add Lifeline</span>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    import LifeLine from './Lifeline.vue'
    export default {
        components : {
            LifeLine
        },
        data() {
            return {
                showLifelineForm : true,
                form: this.createForm({
                    name : null,
                    threshold : 5,
                    site : this.$route.params.site_id,
                })
            }
        },
        created() {
            this.$store.dispatch('user_site_life_lines/get', this.$route.params.site_id)
        },
        methods : {
            createLifeline() {
                this.$store.dispatch('user_site_life_lines/store', this.form).then(() => {
                    this.form.reset()
                    this.showLifelineForm = true
                })
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