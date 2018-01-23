<template>
    <div class="section-header--btn-left">
        <div class="btn pull-left" @click="back"><span class="icon-arrow-left"></span> Back</div>
    </div>
</template>

<script>
export default {
  methods: {
    back() {
      window.history.back();
    }
  }
};
</script>
