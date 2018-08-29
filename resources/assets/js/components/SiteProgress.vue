<template>
<div class="progress">
    <div class="progress--item" :class="{ completed : createdSite }">
        <div class="progress--item-number">
            1
        </div>
        <div class="progress--item-name">
            Site Setup
        </div>
    </div>

    <div class="progress--item-divider"></div>

    <div class="progress--item" :class="{ completed : provisionedInfrastructure }">
        <div class="progress--item-number">
            2
        </div>
        <div class="progress--item-name">
            Provision Infrastructure
        </div>
    </div>

    <div class="progress--item-divider"></div>

    <div class="progress--item">
        <div class="progress--item-number">
            3
        </div>
        <div class="progress--item-name">
            Deploy
        </div>
    </div>

</div>
</template>

<script>
export default {
  props: {
    createdSite: {
      default: false,
    },
    siteSetup: {
      default: false,
    },
    provisionedInfrastructure: {
      default: false,
    },
  },
};
</script>
