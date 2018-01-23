<template>
    <div class="settings--container">
        <h3>{{ title }}</h3>
        <template v-for="notification_setting in notification_settings">
            <notification-setting :key="notification_setting.name" :notification_setting="notification_setting"></notification-setting>
        </template>
    </div>
</template>

<script>
import NotificationSetting from "./NotificationSetting";
export default {
  props: ["title", "notification_settings"],
  components: {
    NotificationSetting
  }
};
</script>
