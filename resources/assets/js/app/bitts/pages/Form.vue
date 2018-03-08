<template>
    <section>
        Bitt Form
        <div class="jcf-form-wrap">

            <form @submit.prevent="saveUpdateBitt">

                <div class="jcf-input-group">
                    <input type="text" name="title" v-model="form.title">
                    <label for="title">
                        <span class="float-label">Title</span>
                    </label>
                </div>

                <div class="jcf-input-group">
                    <div class="input-question">Category</div>
                    <div class="select-wrap">
                        <select name="category" v-model="form.category">
                            <option></option>
                            <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                        </select>
                    </div>
                </div>

                <div class="jcf-input-group">
                    <div class="input-question">Systems</div>
                    <div class="select-wrap">
                        <select multiple name="category" v-model="form.systems">
                            <option></option>
                            <option v-for="system in systems" :value="system.id">{{ system.name }}</option>
                        </select>
                    </div>
                </div>

                <div class="jcf-input-group">
                    <div class="input-question">Systems</div>
                    <div class="select-wrap">
                        <select name="user" v-model="form.user">
                            <option value="root">Root</option>
                            <option value="codepier">CodePier</option>
                        </select>
                    </div>
                </div>

                <div class="jcf-input-group">
                    <div class="input-question">Description</div>
                    <textarea name="description" v-model="form.description"></textarea>
                </div>

                <div class="jcf-input-group">
                    <div class="input-question">Script</div>
                    <textarea name="script" v-model="form.script"></textarea>
                </div>

                <div class="jcf-input-group input-checkbox">
                    <div class="input-question">Private Bitt</div>
                    <label>
                        <input type="checkbox" name="active" v-model="form.private">
                        <span class="icon"></span>
                        Private
                    </label>
                </div>

                <div class="jcf-input-group input-checkbox">
                    <div class="input-question">Active</div>
                    <label>
                        <input type="checkbox" name="active" v-model="form.active">
                        <span class="icon"></span>
                        Verified
                    </label>
                </div>

                <!--<div class="jcf-input-group input-checkbox">-->
                    <!--<div class="input-question">Active</div>-->
                    <!--<label>-->
                        <!--<input type="checkbox" name="active" v-model="form.active">-->
                        <!--<span class="icon"></span>-->
                        <!--Verified-->
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
        </div>
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
