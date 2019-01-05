<template>
  <div class="settings--container">
    <h3>{{ title }}</h3>
    <template v-for="setting in settings">
      <notification-setting
        :key="setting.name"
        :setting="setting"
      ></notification-setting>
    </template>
  </div>
</template>

<script>
import NotificationSetting from "./NotificationSetting";
export default {
  props: ["title", "settings"],
  components: {
    NotificationSetting,
  },
};
</script>
