<template>
    <div class="grid-2 grid-gap-large">
        <div class="grid--item">
            <h3 class="text-center heading">Recent Deployments</h3>

            <div v-if="!recentDeployments">
                <div class="text-empty text-center">Recent deployments will show up here once you have deployed your site.</div>
            </div>
            <template v-else>
                <div class="list">
                    <template v-for="recentDeployment in recentDeployments">
                        <div class="list--item list--item-icons">
                            <div>
                                {{ recentDeployment.status }} <time-ago :time="recentDeployment.created_at"></time-ago>

                                <div>
                                    <small>took ({{ diff(recentDeployment.created_at, recentDeployment.updated_at) }})</small>
                                </div>
                            </div>

                            <confirm dispatch="user_site_deployments/rollback" confirm_position="btns-only" confirm_class="btn-link" :params="{ siteDeployment : recentDeployment.id, site : site.id } " v-if="recentDeployment.status === 'Completed'">
                                <tooltip message="Rollback">
                                    <span class="icon-refresh2"></span>
                                </tooltip>
                            </confirm>
                        </div>


                        <div class="flex deployment">
                            <div class="flex--grow deployment--text">
                            </div>
                            <div class="deployment--btns">
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
export default {
  props: ["site"],
  computed: {
    recentDeployments() {
      // TODO
      return [];
    },
  },
};
</script>
