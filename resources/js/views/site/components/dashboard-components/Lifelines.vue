<template>
    <div class="grid--item">
        <div class="flex flex--center heading">
            <h3 class="flex--grow text-center">
                Lifelines
            </h3>

            <tooltip message="Add Lifeline">
                <span class="btn btn-small btn-primary" :class="{ 'btn-disabled' : !this.showLifelineForm }" @click="showLifelineForm= !showLifelineForm">
                    <span class="icon-plus"></span>
                </span>
            </tooltip>
        </div>

        <div v-if="!lifeLines.length">
            <p>Lifelines enable you to add monitoring to your services by pinging a URL periodically. If the time goes over your set threshold, CodePier will let you know.</p>
            <div class="text-empty text-center">You don't have any lifelines active.</div>
        </div>
        <div v-else>
            <p>Add the provided URL (<span class="icon-clipboard"></span>) to your service to start monitoring.<br></p>
        </div>

        <div class="lifeline">
            <life-line v-for="lifeLine in lifeLines" :key="lifeLine.id" :lifeLine="lifeLine" :site="site"></life-line>
        </div>

        <template v-if="!showLifelineForm">
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

                <div class="flyform--footer-btns">
                    <span class="btn btn-small" @click="showLifelineForm= !showLifelineForm">Cancel</span>
                    <button type="submit" class="btn btn-small btn-primary">Create Lifeline</button>
                </div>
            </form>
        </template>

    </div>
</template>

<script>
import LifeLine from "./Lifeline";
export default {
  props: {
    site: {
      required: true,
    },
  },
  components: {
    LifeLine,
  },
  data() {
    return {
      showLifelineForm: true,
      form: this.createForm({
        name: null,
        threshold: 5,
        site: this.$route.params.site_id,
      }),
    };
  },
  watch: {
    $route: function() {
      this.fetchData();
    },
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      // this.$store.dispatch(
      //   "user_site_life_lines/get",
      //   this.$route.params.site_id,
      // );
    },
    createLifeline() {
      this.$store.dispatch("user_site_life_lines/store", this.form).then(() => {
        this.form.reset();
        this.showLifelineForm = true;
      });
    },
  },
  computed: {
    lifeLines() {
      return [];
      return this.$store.state.user_site_life_lines.life_lines;
    },
  },
};
</script>
