<template>
    <section>
        <p>
            Buoy Form
            <div class="jcf-form-wrap">

                <form @submit.prevent="saveBuoy">

                    <div class="jcf-input-group">
                        <input type="text" name="title" v-model="form.title">
                        <label for="title">
                            <span class="float-label">Title</span>
                        </label>
                    </div>

                    <div v-if="!image">
                        Icon
                        <template v-if="buoyAppId && buoy && buoy.icon_url">
                            <img :src="buoy.icon_url" style="max-width:100px">
                        </template>
                        <input type="file" @change="onFileChange">
                    </div>
                    <div v-else>
                        <img :src="image" style="max-width:100px">
                        <span @click="removeImage">Cancel</span>
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
                        <div class="input-question">Description</div>
                        <textarea name="description" v-model="form.description"></textarea>
                    </div>

                    <h3>Ports</h3>
                    <template v-for="(port, port_description) in form.ports">

                        <div class="jcf-input-group">
                            <input type="text" name="ports[]" v-model="form.ports[port_description].local_port">
                            <label for="ports[]">
                                <span class="float-label">{{ port_description }} : Docker Port {{ port.docker_port }}</span>
                            </label>
                        </div>

                    </template>

                    <h3>Options </h3>

                    <template v-for="(option, option_title) in form.options">

                        <div class="jcf-input-group">
                            <input type="text" name="optionValues[]" v-model="form.options[option_title].value">
                            <label for="optionValues[]">
                                <span class="float-label">{{ option_title }} : {{ option.description }}</span>
                            </label>
                        </div>

                    </template>

                    <div class="jcf-input-group input-checkbox">
                        <div class="input-question">Active</div>
                        <label>
                            <input type="checkbox" name="active" v-model="form.active">
                            <span class="icon"></span>
                            Active
                        </label>
                    </div>

                    <div class="btn-footer">
                        <button class="btn btn-primary" type="submit">
                            <template v-if="buoyAppId">
                                Update
                            </template>
                            <template v-else>
                                Create
                            </template>
                            Buoy
                        </button>
                    </div>

                </form>
            </div>
        </p>
    </section>
</template>

<script>
export default {
  created() {
    this.$store.dispatch("admin_categories/get").then(() => {
      this.$store.dispatch("buoys/show", this.buoyAppId).then(buoy => {
        this.form.icon = buoy.icon;
        this.form.ports = buoy.ports;
        this.form.title = buoy.title;
        this.form.active = buoy.active;
        this.form.options = buoy.options;
        this.form.buoy_class = buoy.buoy_class;
        this.form.description = buoy.description;
        if (buoy.categories.length) {
          this.form.category = buoy.categories[0].id;
        }
      });
    });
  },
  data() {
    return {
      form: this.createForm({
        ports: [],
        icon: null,
        options: [],
        title: null,
        active: false,
        category: null,
        buoy_class: null,
        description: null
      }),
      image: null
    };
  },
  methods: {
    saveBuoy() {
      let form = this.form;
      let data = new FormData();

      data.append("icon", form.icon);
      data.append("title", form.title);
      data.append("active", form.active);
      data.append("category", form.category);
      data.append("buoy_class", form.buoy_class);
      data.append("description", form.description);
      data.append("ports", JSON.stringify(form.ports));
      data.append("options", JSON.stringify(form.options));

      this.$store.dispatch("buoys/update", {
        form: data,
        buoy_app: this.buoyAppId
      });
    },
    createImage(file) {
      const reader = new FileReader();

      reader.onload = e => {
        this.image = e.target.result;
      };

      reader.readAsDataURL(file);
    },
    removeImage() {
      this.form.icon = null;
      this.image = null;
    },
    onFileChange(e) {
      const files = e.target.files || e.dataTransfer.files;
      if (!files.length) {
        return;
      }

      const image = files[0];

      this.form.icon = image;

      this.createImage(image);
    }
  },
  computed: {
    buoyAppId() {
      return this.$route.params.buoy_id;
    },
    buoy() {
      return this.$store.state.buoys.buoy_app;
    },
    categories() {
      return this.$store.state.admin_categories.categories;
    }
  }
};
</script>
