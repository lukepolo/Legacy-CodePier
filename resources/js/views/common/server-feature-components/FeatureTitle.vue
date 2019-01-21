<template>
  <li>
    <router-link
      :to="{
        name: routeName,
        params: {
          section: section,
        },
      }"
    >
      {{ sectionTitle }}
    </router-link>
  </li>
</template>

<script>
import uncamelize from "varie/lib/utilities/uncamelize";
export default {
  props: {
    section: {
      required: true,
    },
    routeName: {
      required: true,
    },
  },
  computed: {
    sectionTitle() {
      // TODO - move ucwords to varie
      // TODO - create utilities repo for varie
      return (
        uncamelize(this.section)
          .split(" ")
          .map((s) => s.charAt(0).toUpperCase() + s.substring(1))
          .join(" ") + " Features"
      );
    },
  },
};
</script>
