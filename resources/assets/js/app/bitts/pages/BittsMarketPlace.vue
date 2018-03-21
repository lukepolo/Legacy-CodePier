<template>
    <section v-if="pagination">
        <div class="flex flex--center">
            <h3 class="flex--grow">
                &nbsp;
            </h3>
            <tooltip message="Create Bitt">
                <router-link :to="{ name: 'bitt_create' }">
                    <div class="btn btn-small btn-primary">
                        <span class="icon-plus"></span>
                    </div>
                </router-link>
            </tooltip>
        </div>
        <br>

        <pagination :pagination="pagination" dispatch="bitts/get"></pagination>
        <div class="group">
            <bitt :bitt="bitt" v-on:searchBitts="search" v-for="bitt in bitts" :key="bitt.id"></bitt>
        </div>
        <pagination :pagination="pagination" dispatch="bitts/get"></pagination>
    </section>
</template>

<script>
import { Bitt } from "../components";

export default {
  components: {
    Bitt
  },
  created() {
    this.search();
  },
  methods : {
    search() {
      this.$store.dispatch("bitts/get");
    }
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
