<template>
    <section>
        <h4>Create Bitt</h4>
        <form @submit.prevent="saveUpdateBitt">
            <div class="flyform--group">
                <input type="text" name="title" v-model="form.title">
                <label for="title">Title</label>
            </div>

            <!--<div class="flyform&#45;&#45;group">-->
                <!--<label>Category</label>-->
                <!--<div class="flyform&#45;&#45;group-select">-->
                    <!--<select name="category" v-model="form.category">-->
                        <!--<option></option>-->
                        <!--<option v-for="category in categories" :value="category.id">{{ category.name }}</option>-->
                    <!--</select>-->
                <!--</div>-->
            <!--</div>-->

            <div class="flyform--group">
                <textarea name="description" v-model="form.description"></textarea>
                <label>Description</label>
            </div>

            <div class="flyform--group">
                <div ref="editor" v-file-editor class="editor"></div>
                <label>Script</label>
            </div>

            <!--<div class="flyform&#45;&#45;group">-->
                <!--<label>Select Systems</label>-->
                <!--<select multiple name="category" v-model="form.systems">-->
                    <!--<option></option>-->
                    <!--<option v-for="system in systems" :value="system.id">{{ system.name }}</option>-->
                <!--</select>-->
            <!--</div>-->

            <div class="flyform--group">
                <label>Run Script With</label>
                <div class="flyform--group-select">
                    <select name="user" v-model="form.user">
                        <option value="root">Root</option>
                        <option value="codepier">CodePier</option>
                    </select>
                </div>
            </div>

            <!--<div class="flyform&#45;&#45;group-checkbox">-->
                <!--<label>-->
                    <!--<input type="checkbox" name="active" v-model="form.private">-->
                    <!--<span class="icon"></span>-->
                    <!--<span class="icon-visibility_off"></span> Make Private-->
                <!--</label>-->
            <!--</div>-->

            <!--<div class="flyform&#45;&#45;group-checkbox">-->
                <!--<label>-->
                    <!--<input type="checkbox" name="active" v-model="form.active">-->
                    <!--<span class="icon"></span>-->
                    <!--<span class="icon-verified"></span> Verified-->
                <!--</label>-->
            <!--</div>-->

            <div class="btn-footer">
                <button class="btn btn-primary" type="submit">
                    <template v-if="bittId">
                        Update
                    </template>
                    <template v-else>
                        Create
                    </template>
                    Bitt
                </button>
            </div>
        </form>
    </section>
</template>

<script>
export default {
  created() {
    // this.$store.dispatch("admin_categories/get");
    // this.$store.dispatch("server_systems/get");
    if (this.bittId) {
        this.$store.dispatch("bitts/show", this.bittId).then((bitt) => {
          this.form.fill(bitt);
          this.renderContent();
        });
    } else {
      this.renderContent();
    }
  },
  data() {
    return {
      form: this.createForm({
        title: null,
        script: null,
        user : 'root',
        systems: [],
        private: true,
        category: null,
        description: null
      })
    };
  },
  mounted() {
    this.$store.commit('bitts/view', null)
  },
  methods: {
    renderContent() {
      let editor = this.$refs.editor;
      console.info(editor)
      ace
        .edit(editor)
        .setValue(
          this.form.script ? this.form.script : ""
        );
      ace.edit(editor).clearSelection(1);
    },
    getContent() {
      return ace.edit(this.$refs.editor).getValue();
    },
    saveUpdateBitt() {

      this.form.script = this.getContent();

      if (this.bittId) {
        this.$store.dispatch("bitts/update", {
          form: this.form.data(),
          bitt: this.bittId
        }).then((bitt) => {
          this.$store.commit("bitts/view", bitt)
          this.$router.push({ name : 'bitts_market_place'})
        });
      } else {
        this.$store.dispatch("bitts/store", this.form).then((bitt) => {
          this.$store.commit("bitts/view", bitt)
          this.$router.push({ name : 'bitts_market_place'})
        });
      }
    }
  },
  computed: {
    bittId() {
      return this.$route.params.bitt_id;
    },
    systems() {
      return this.$store.state.server_systems.systems;
    },
    categories() {
      return this.$store.state.admin_categories.categories;
    }
  }
};
</script>
