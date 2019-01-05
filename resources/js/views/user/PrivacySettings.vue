<template>
  <div>
    <div class="flyform--group">
      <label>Allow Data Usage for Marketing Purposes</label>
      <div class="toggleSwitch">
        No &nbsp;
        <div
          @click="toggleMarketing"
          class="toggleSwitch--button toggleSwitch--button-switch"
          :class="{ right: user.marketing, left: !user.marketing }"
        ></div>
        &nbsp; Yes
      </div>
      <small>This data is only used to send you emails from CodePier.</small>
    </div>

    <div class="flyform--group">
      <label>Allow Data Processing</label>
      <div class="toggleSwitch">
        No &nbsp;
        <div
          @click="toggleProcessing"
          class="toggleSwitch--button toggleSwitch--button-switch"
          :class="{ right: user.processing, left: !user.processing }"
        ></div>
        &nbsp; Yes
      </div>
      <small
        >Data processing is only used for bug reporing and support chat.</small
      >
    </div>

    <div class="flyform--footer">
      <div class="flyform--footer-btns">
        <button
          @click="showDeleteAccountModal = true"
          class="btn--link btn--link-danger"
        >
          Delete My Account
        </button>
        <button class="btn" @click="requestData">Request My Data</button>
      </div>
    </div>
    <modal v-if="showRequestDataModal">
      <div class="modal--header"><h1>You have requested your data!</h1></div>
      <div class="modal--body">
        <p>
          It may take up to 48 hours to process your data to send to you. You
          will receive it at {{ user.email }}.
        </p>
      </div>

      <div class="modal--footer">
        <span class="btn btn-primary" @click="showRequestDataModal = false"
          >Close</span
        >
      </div>
    </modal>

    <modal v-if="showDeleteAccountModal">
      <div class="modal--header">
        <h1>Are you sure you want to delete your account?!</h1>
      </div>
      <div class="modal--body">
        <p>THIS PROCESS IS UNRECOVERABLE, ARE YOU SURE YOU WANT TO CONTINUE?</p>
      </div>

      <div class="modal--footer">
        <span class="btn btn-danger" @click="deleteAccount">YES IM SURE</span>
        <span class="btn btn-primary" @click="showDeleteAccountModal = false"
          >CANCEL</span
        >
      </div>
    </modal>
  </div>
</template>

<script>
export default {
  data() {
    return {
      showRequestDataModal: false,
      showDeleteAccountModal: false,
    };
  },
  methods: {
    requestData() {
      this.$store.dispatch("user/requestData").then(() => {
        this.showRequestDataModal = true;
      });
    },
    toggleMarketing() {
      this.user.marketing = !this.user.marketing;
      this.$store.dispatch("user/update", {
        marketing: this.user.marketing,
      });
    },
    toggleProcessing() {
      this.user.processing = !this.user.processing;
      this.$store.dispatch("user/update", {
        processing: this.user.processing,
      });
    },
    deleteAccount() {
      this.$store.dispatch("user/destroy").then(() => {
        window.location.reload();
      });
    },
  },
};
</script>
