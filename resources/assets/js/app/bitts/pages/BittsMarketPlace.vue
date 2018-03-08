<template>
    <section>
        <h3>Bitts</h3>
        <router-link :to="{ name: 'bitt_create' }">Create Bitt</router-link>
        <div class="group-container">
            <bitt :bitt="bitt" v-for="bitt in bitts" :key="bitt.id"></bitt>
        </div>
    </section>
</template>

<script>
import { Bitt } from "../components";

export default {
  components: {
    Bitt
  },
  created() {
    // this.$store.dispatch("getCategories").then(() => {
      this.$store.dispatch("bitts/get");
    // });
  },
  computed: {
    bitts() {
      return this.$store.state.bitts.bitts.data;
    },
    pagination() {
      return this.$store.state.bitts.bitts;
    },
    categories() {
      // return this.$store.state.categoriesStore.categories;
    }
  }
};
</script>
