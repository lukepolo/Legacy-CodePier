<template>
    <section>
        <h4>Create Bitt</h4>
        <form @submit.prevent="saveUpdateBitt">
            <div class="flyform--group">
                <input type="text" name="title" v-model="form.title">
                <label for="title">Title</label>
            </div>

            <div class="flyform--group">
                <label>Category</label>
                <div class="flyform--group-select">
                    <select name="category" v-model="form.category">
                        <option></option>
                        <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                    </select>
                </div>
            </div>

            <div class="flyform--group">
                <textarea name="description" v-model="form.description"></textarea>
                <label>Description</label>
            </div>

            <div class="flyform--group">
                <textarea name="script" v-model="form.script" rows="8"></textarea>
                <label>Script</label>
            </div>

            <div class="flyform--group">
                <label>Select Systems</label>
                <select multiple name="category" v-model="form.systems">
                    <option></option>
                    <option v-for="system in systems" :value="system.id">{{ system.name }}</option>
                </select>
            </div>

            <div class="flyform--group">
                <label>System User</label>
                <div class="flyform--group-select">
                    <select name="user" v-model="form.user">
                        <option value="root">Root</option>
                        <option value="codepier">CodePier</option>
                    </select>
                </div>
            </div>

            <div class="flyform--group-checkbox">
                <label>
                    <input type="checkbox" name="active" v-model="form.private">
                    <span class="icon"></span>
                    <span class="icon-visibility_off"></span> Make Private
                </label>
            </div>

            <div class="flyform--group-checkbox">
                <label>
                    <input type="checkbox" name="active" v-model="form.active">
                    <span class="icon"></span>
                    <span class="icon-verified"></span> Verified
                </label>
            </div>

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
    this.$store.dispatch("admin_categories/get");
    this.$store.dispatch("server_systems/get");
    //     if (this.bittId) {
    //       this.$store.dispatch("getBitt", this.bittId).then(bitt => {
    //         this.form.title = bitt.title;
    //         this.form.script = bitt.script;
    //         this.form.private = bitt.private;
    //         this.form.description = bitt.description;
    //         this.form.category = bitt.categories[0].id;
    //         this.form.systems = _.map(bitt.systems, "id");
    //       });
    //     }
    //   });
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
  methods: {
    saveUpdateBitt() {
      if (this.bittId) {
        // this.$store.dispatch("bitts/update", {
        //   form: this.form,
        //   bitt: this.bittId
        // });
      } else {
        this.$store.dispatch("bitts/store", this.form).then((bitt) => {
          console.info(bitt)
          this.$store.commit("bitts/set", bitt)
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
