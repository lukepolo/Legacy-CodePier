<template>
  <li>
    <router-link
      :to="{
        name: routeName,
        params: {
          section: feature,
        },
      }"
    >
      {{ title }}
    </router-link>
  </li>
</template>

<script>
import uncamelize from "varie/lib/utilities/uncamelize";
export default {
  props: {
    feature: {
      required: true,
    },
    routeName: {
      required: true,
    },
  },
  computed: {
    title() {
      // TODO - move ucwords to varie
      // TODO - create utilities repo for varie
      return (
        uncamelize(this.feature)
          .split(" ")
          .map((s) => s.charAt(0).toUpperCase() + s.substring(1))
          .join(" ") + " Features"
      );
    },
  },
};
</script>
