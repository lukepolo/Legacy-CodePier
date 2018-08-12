<template>
    <div class="group--item">
        <div class="group--item-heading">
            <h4>
                <template v-if="editing">
                    <input ref="pileName" v-model="form.name" type="text" placeholder="Pile Name"
                           @keyup.enter="savePile">

                    <div class="action-btn">
                        <button @click="savePile" class="btn btn-small btn-primary"><span
                                class="icon-check_circle"></span></button>

                        <button @click="deletePile()" class="btn btn-small">
                            <template v-if="pile">
                                <span class="icon-trash"></span>
                            </template>
                            <template v-else>
                                <span class="icon-cancel"></span>
                            </template>
                        </button>
                    </div>
                </template>
                <template v-else>
                    <template v-if="pile.name && pile.name.length > 23">
                        <tooltip :message="pile.name" placement="bottom">
                            <div class="group--item-heading-name">{{ pile.name }}</div>
                        </tooltip>
                    </template>
                    <template v-else>
                        <div class="group--item-heading-name">{{ pile.name }}</div>
                    </template>

                    <div class="action-btn">
                        <button @click="editing = true" class="btn btn-small"><span class="icon-pencil"></span></button>

                        <button @click="deletePile()" class="btn btn-small">
                            <span class="icon-trash"></span>
                        </button>
                    </div>
                </template>
            </h4>
        </div>

        <div class="group--item-content" v-if="pile">
            <template v-if="pile.sites && pile.sites.length">
                <h4>Sites</h4>
                <div class="list">
                    <router-link class="list--item" :to="{ name: 'site_overview', params : { site_id : site.id} }"
                                 v-for="site in pile.sites" :key="site.id">
                        <div class="list--item-name">
                            {{ site.name }}
                        </div>
                    </router-link>
                </div>
            </template>
            <template v-else>
                <h4>No Sites</h4>
            </template>

            <div :class="{ 'disabled' : !siteCreateEnabled }" class="group--item-link" @click="addingSite = true"
                 v-if="!addingSite && pile.id">
                <span class="icon-plus"></span> Create New Site
            </div>
            <site-form :pile="pile" :adding.sync="addingSite"></site-form>
        </div>
    </div>
</template>

<script>
import Vue from "vue";
import SiteForm from "./SiteForm";

export default Vue.extend({
  props: ["pile"],
  components: {
    SiteForm,
  },
  data() {
    return {
      form: this.createForm({
        pile: this.pile ? this.pile.id : null,
        name: this.pile ? this.pile.name : null,
      }),
      addingSite: false,
      editing: this.pile ? this.pile.editing : true,
    };
  },
  watch: {
    editing() {
      this.focus();
    },
  },
  mounted() {
    this.focus();
  },
  methods: {
    focus() {
      this.$nextTick(() => {
        if (this.editing) {
          this.$refs.pileName.focus();
        }
      });
    },
    cancel() {
      if (!this.pile) {
        this.$emit("closeNewPile");
      }
      this.editing = false;
    },
    deletePile() {
      if (this.pile) {
        return this.$store.dispatch("user_piles/destroy", this.pile.id);
      }
      this.cancel();
    },
    savePile() {
      if (this.pile) {
        return this.$store.dispatch("user_piles/update", this.form);
      }
      this.$store.dispatch("user_piles/store", this.form);
      this.cancel();
    },
  },
});
</script>
