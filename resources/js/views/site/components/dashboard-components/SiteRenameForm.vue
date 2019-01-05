<template>
  <confirm
    dispatch="user_sites/renameSite"
    :params="{
      site: site.id,
      pile_id: site.pile_id,
      domain: renameForm.domain,
      wildcard_domain: renameForm.wildcard_domain,
    }"
    confirm_class="btn-link"
    confirm_position="bottom"
    message="Update Site Details"
    confirm_btn="btn-primary"
  >
    <tooltip message="Update Site Details" placement="bottom">
      <span class="icon-pencil"></span>
    </tooltip>
    <div slot="form">
      <div class="flyform--group-checkbox">
        <label>
          <input
            v-model="renameForm.wildcard_domain"
            type="checkbox"
            name="wildcard_domain"
          />
          <span class="icon"></span> Wildcard Domain
          <tooltip
            :message="'If your site requires wildcard for sub domains'"
            size="medium"
          >
            <span class="fa fa-info-circle"></span>
          </tooltip>
        </label>
      </div>

      <div class="flyform--group">
        <input
          v-model="renameForm.domain"
          type="text"
          name="domain"
          placeholder=" "
        />
        <label for="domain">Domain</label>
      </div>
    </div>
  </confirm>
</template>

<script>
export default {
  props: {
    site: {
      required: true,
    },
  },
  data() {
    return {
      renameForm: this.createForm({
        domain: null,
        wildcard_domain: null,
      }),
    };
  },
  watch: {
    site: {
      immediate: true,
      handler(site) {
        this.renameForm.fill({
          domain: site.name,
          wildcard_domain: site.wildcard_domain,
        });
      },
    },
  },
  methods: {},
};
</script>
