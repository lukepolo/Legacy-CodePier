<template>
  <drop-down tag="span" v-if="!site.user_repository_provider_id">
    <div class="grid--item" slot="header">
      <div class="providers--item">
        <div class="providers--item-header">
          <div class="providers--item-icon"><span class="icon-web"></span></div>
        </div>
        <div class="providers--item-footer">
          <div class="providers--item-footer-connect">
            <h4 class="providers--title">Site SSH Key</h4>
          </div>
        </div>
      </div>
    </div>
    <div slot="content" class="dropdown-menu dropdown-content nowrap">
      <h3>
        <!--<confirm-dropdown dispatch="user_site_ssh_keys/refreshPublicKey" :params="site.id">-->
        <!--Public SSH Key &nbsp;-->
        <!--<tooltip message="Refresh SSH Key">-->
        <!--<a @click.prevent href="#"><span class="fa fa-refresh"></span></a>-->
        <!--</tooltip>-->
        <!--</confirm-dropdown>-->
      </h3>

      <div class="flyform--group flyform--group-nomargin">
        <textarea rows="5" readonly>{{ site.public_ssh_key }}</textarea>
      </div>

      <div class="text-right">
        <tooltip message="Copy to Clipboard">
          <clipboard :data="site.public_ssh_key"></clipboard>
        </tooltip>
      </div>
    </div>
  </drop-down>
</template>

<script>
export default {
  props: ["site"],
};
</script>
