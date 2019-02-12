<template>
    <div class="events--item">
        <div
            class="events--item-status"
            :class="{
                'events--item-status-neutral' : event.status === 'Queued',
                'events--item-status-success' : event.status === 'Completed',
                'events--item-status-error' : event.status === 'Failed',
                'icon-spinner' : event.status === 'Running'
             }"></div>
        <div class="events--item-name">
            <drop-down-event
                    :title="deploymentTitle"
                    :event="event"
                    :prefix="event.id"
            >
                <template v-for="server_deployment in event.server_deployments">
                    <drop-down-event
                            :title="getServer(server_deployment.server_id, 'name') + ' (' + getServer(server_deployment.server_id, 'ip') + ')' + ' - ' + server_deployment.status"
                            :event="server_deployment"
                            :prefix="'server_deployment_'+server_deployment.id"
                    >
                        <ul>
                            <template v-for="deployment_event in server_deployment.events">
                                <li :class="{'events--item-error' : deployment_event.failed }" v-if="deployment_event.step">
                                    <drop-down-event
                                            :title="deployment_event.step.step + (deployment_event.completed ? ' - took ' + formatSeconds(deployment_event.runtime) + ' seconds' : '')"
                                            :event="deployment_event"
                                            :prefix="'server_deployment_event_' + deployment_event.id"
                                            :dropdown="filterArray(deployment_event.log).length > 0"
                                    >
                                    <pre v-for="log in filterArray(deployment_event.log)">{{ log }}</pre>
                                    </drop-down-event>
                                </li>
                            </template>
                        </ul>
                    </drop-down-event>
                </template>
            </drop-down-event>
        </div>
        <div class="events--item-pile"><span class="icon-layers"></span> {{ pile.name }}</div>
        <div class="events--item-site">
            <span class="icon-browser"></span>
            {{ site.name }}
        </div>

        <div class="events--item-commit">
            <a target="_blank" :href="repositoryUrl" v-if="repositoryUrl && event.git_commit"><span class="icon-github"></span> </a>

            <!--<confirm dispatch="user_site_deployments/rollback" confirm_class="btn btn-small" :params="{ siteDeployment : event.id, site : event.site_id } " v-if="event.status === 'Completed'">-->
                <!--Rollback-->
            <!--</confirm>-->
        </div>
        <div class="events--item-time">
            <time-ago :time="event.created_at"></time-ago>
        </div>
    </div>
</template>

<script>
import DropDownEvent from "./DropDownEvent.vue";
export default {
  components: {
    DropDownEvent,
  },
  props: ["event"],
  methods: {
    filterArray(data) {
      if (!Array.isArray(data)) {
        data = [data];
      }

      return _.reject(_.reject(data, _.isEmpty), _.isNull);
    },
    formatSeconds(number) {
      let seconds = parseFloat(number).toFixed(2);

      if (!isNaN(seconds)) {
        return seconds;
      }
    },
  },
  computed: {
    site() {
      return this.getSite(this.event.site_id);
    },
    pile() {
      return this.getPile(this.site.pile_id);
    },
    repositoryUrl() {
      let userRepositoryProvider = this.user_repository_providers.find(
        (provider) => {
          return this.site.user_repository_provider_id === provider.id;
        },
      );

      if (userRepositoryProvider) {
        let repositoryProvider = this.repository_providers.find((provider) => {
          return provider.id === userRepositoryProvider.repository_provider_id;
        });

        if (repositoryProvider) {
          return (
            "https://" +
            repositoryProvider.url +
            "/" +
            this.site.repository +
            "/" +
            repositoryProvider.commit_url +
            "/" +
            this.event.git_commit
          );
        }
      }
    },
    repository_providers() {
      return this.$store.state.repository_providers.providers;
    },
    totalAmountOfTime() {
      let totalTime = 0;
      this.event.server_deployments.forEach((server_deployment) => {
        server_deployment.events.forEach((deployment_event) => {
          let time = parseFloat(deployment_event.runtime);
          if (time) {
            totalTime += time;
          }
        });
      });
      return this.formatSeconds(totalTime);
    },
    deploymentTitle() {
      let title = "Deployment";
      if (this.event.status === "Completed") {
        title = `${title} (${this.totalAmountOfTime} seconds)`;
      }
      return title;
    },
    user_repository_providers() {
      return this.$store.state.user_repository_providers.providers;
    },
  },
};
</script>
