<template>
  <section id="middle" class="section-column">
    <template v-if="workFlowCompleted">
      <h3 class="section-header primary" v-if="site">
        <router-link :to="{ name: 'site', params: { site: site.id } }">
          {{ site.name }}
        </router-link>

        <template v-if="site.domain !== 'default'">
          &nbsp;
          <a :href="'//' + site.domain" target="_blank"
            ><i class="fa fa-external-link"></i
          ></a>
        </template>

        <div class="section-header--btn-right">
          <drop-down tag="span" v-if="hasServers">
            <button
              slot="header"
              class="btn btn-default btn-xs dropdown-toggle"
              type="button"
              data-toggle="dropdown"
            >
              <span class="icon-refresh2"></span>
            </button>

            <ul slot="content" class="dropdown-menu nowrap dropdown-list">
              <li>
                <confirm-dropdown
                  dispatch="user_site_services/restartWebServices"
                  :params="site.id"
                  ><a @click.prevent href="#"
                    ><span class="icon-web"></span> Restart Web Services</a
                  ></confirm-dropdown
                >
              </li>
              <li>
                <confirm-dropdown
                  dispatch="user_site_services/restartServers"
                  :params="site.id"
                  ><a @click.prevent href="#"
                    ><span class="icon-server"></span> Restart Servers</a
                  ></confirm-dropdown
                >
              </li>
              <li>
                <confirm-dropdown
                  dispatch="user_site_services/restartDatabases"
                  :params="site.id"
                  ><a @click.prevent href="#"
                    ><span class="icon-database"></span> Restart Databases</a
                  ></confirm-dropdown
                >
              </li>
              <li>
                <confirm-dropdown
                  dispatch="user_site_services/restartWorkers"
                  :params="site.id"
                  ><a @click.prevent href="#"
                    ><span class="icon-worker"></span> Restart Workers &
                    Daemons</a
                  ></confirm-dropdown
                >
              </li>
            </ul>
          </drop-down>

          <drop-down tag="span" v-if="hasServers">
            <button
              slot="header"
              class="btn btn-default btn-xs dropdown-toggle"
              type="button"
              data-toggle="dropdown"
            >
              <span class="fa fa-life-buoy"></span>
            </button>

            <ul slot="content" class="dropdown-menu nowrap dropdown-list">
              <li>
                <confirm-dropdown
                  dispatch="user_sites/fixServerConfigurations"
                  :params="site.id"
                  ><a href="#"
                    ><span class="fa fa-medkit"></span> Fix Server
                    Configurations</a
                  ></confirm-dropdown
                >
              </li>
              <li>
                <confirm-dropdown
                  dispatch="user_site_server_commands/clearStuckCommands"
                  :params="site.id"
                  ><a href="#"
                    ><span class="fa fa-bomb"></span> Clear Stuck Commands</a
                  ></confirm-dropdown
                >
              </li>
            </ul>
          </drop-down>
        </div>
      </h3>
      <div>^^ header</div>
      <br />

      <div class="section-content">
        <div class="container">
          <ul class="wizard" v-if="site">
            <router-link
              :to="{
                name: 'site.repository-information',
                params: { site: site.id },
              }"
              tag="li"
              class="wizard-item"
            >
              <a>Site Setup</a>
            </router-link>

            <router-link
              :to="{ name: 'site.ssh-keys', params: { site: site.id } }"
              tag="li"
              class="wizard-item"
            >
              <a>Security</a>
            </router-link>

            <router-link
              :to="{
                name: 'site.environment-variables',
                params: { site: site.id },
              }"
              tag="li"
              class="wizard-item"
            >
              <a>Server Setup</a>
            </router-link>
          </ul>
          <div class="tab-container tab-left child-view">
            <div class="nav nav-tabs"><component :is="subNav"></component></div>
            <div class="tab-content"><router-view></router-view></div>
          </div>
        </div>
      </div>
    </template>
    <template v-else>
      <router-view></router-view>
    </template>
  </section>
</template>

<script>
export default {
  computed: {
    site() {
      return this.$store.getters["user/sites/show"](this.$route.params.site);
    },
    workFlowCompleted() {
      return this.$store.getters["user/sites/workFlowCompleted"](this.site);
    },
    hasServers() {
      return false;
    },
    subNav() {
      return this.$route.meta.data.subNav;
    },
  },
};
</script>
