export const get = ({ dispatch }) => {
  return Vue.request()
    .get(Vue.action("ServerServerController@index"), "user_servers/setAll")
    .then((servers) => {
      _.each(servers, function(server) {
        dispatch("listenTo", server);
      });
    });
};

export const show = (context, server) => {
  return Vue.request().get(
    Vue.action("ServerServerController@show", { server: server }),
    "user_servers/set",
  );
};

export const store = ({ dispatch, commit }, data) => {
  return Vue.request(data)
    .post(Vue.action("ServerServerController@store"), "user_servers/add")
    .then((server) => {
      dispatch("listenTo", server);
      commit("user_sites/set", { response: null }, { root: true });
      app.showSuccess("Your server is in queue to be provisioned");
      return server;
    });
};

export const updatePrivateIps = ({ dispatch, commit }, data) => {
  return Vue.request(data)
    .patch(
      Vue.action("ServerServerPrivateIpAddressController@update", {
        server: data.server,
      }),
      "user_servers/set",
      "user_servers/update",
    )
    .then((server) => {
      app.showSuccess("You have updated your server private IP addresses");
      return server;
    });
};

export const deleteServer = ({ dispatch }, server) => {
  return Vue.request(server)
    .delete(
      Vue.action("ServerServerController@destroy", {
        server: server,
        force: true,
      }),
    )
    .then(() => {
      dispatch("getTrashed");
      if (app.$router.currentRoute.params.server_id) {
        app.$router.push("/");
      }
      app.showSuccess("You have deleted the server");
    });
};

export const archive = (context, server) => {
  return Vue.request(server)
    .delete(Vue.action("ServerServerController@destroy", { server: server }), [
      "user_servers/remove",
      "user_site_servers/remove",
    ])
    .then(() => {
      if (app.$router.currentRoute.params.server_id) {
        app.$router.push("/");
      }
      app.showSuccess("You have archived the server");
    });
};

export const getTrashed = () => {
  return Vue.request().get(
    Vue.action("ServerServerController@index", { trashed: true }),
    "user_servers/setTrashed",
  );
};

export const getSudoPassword = (context, { server }) => {
  return Vue.request().get(
    Vue.action("ServerServerController@getSudoPassword", { server }),
  );
};

export const refreshSudoPassword = (context, { server }) => {
  return Vue.request().post(
    Vue.action("ServerServerController@refreshSudoPassword", { server }),
  );
};

export const getDatabasePassword = (context, { server }) => {
  return Vue.request().get(
    Vue.action("ServerServerController@getDatabasePassword", { server }),
  );
};

export const restore = ({ dispatch }, server) => {
  return Vue.request(server)
    .post(Vue.action("ServerServerController@restore", { server: server }), [
      "user_servers/add",
      "user_servers/removeFromTrash",
    ])
    .then((server) => {
      dispatch("listenTo", server);
      return server;
    });
};

export const listenTo = ({ commit, state, dispatch }, server) => {
  if (_.indexOf(state.listening_to, server.id) === -1) {
    commit("listenTo", server);

    if (server.progress < 100) {
      dispatch("user_server_provisioning/getCurrentStep", server.id, {
        root: true,
      });
    }

    Echo.private("App.Models.Server.Server." + server.id)
      .listen("Server\\ServerProvisionStatusChanged", (data) => {
        commit(
          "user_servers/update",
          {
            response: data.server,
          },
          {
            root: true,
          },
        );

        commit(
          "user_site_servers/update",
          {
            response: data.server,
          },
          {
            root: true,
          },
        );

        commit(
          "user_server_provisioning/setCurrentStep",
          {
            response: data.serverCurrentProvisioningStep,
          },
          {
            root: true,
          },
        );

        if (data.server.provision_steps.length) {
          commit("events/update", data.server, { root: true });
        }
      })
      .listen("Server\\ServerSshConnectionFailed", (data) => {
        commit(
          "user_servers/update",
          {
            response: data.server,
          },
          { root: true },
        );
      })
      .listen("Server\\ServerFailedToCreate", (data) => {
        commit(
          "user_servers/update",
          {
            response: data.server,
          },
          { root: true },
        );
      })
      .listen("Server\\ServerCommandUpdated", (data) => {
        commit("user_commands/update", data.command, { root: true });
        commit("events/update", data.command, { root: true });
      })
      .listen("Server\\ServerFeatureInstalled", (data) => {
        commit(
          "user_servers/update",
          { response: data.server },
          { root: true },
        );
      })
      .listen("Server\\ServerStartToProvision", (data) => {
        commit(
          "user_servers/update",
          { response: data.server },
          { root: true },
        );
        if (data.server.provision_steps.length) {
          commit("events/add", { response: data.server }, { root: true });
        }
      })
      .notification((notification) => {
        switch (notification.type) {
          case "App\\Notifications\\Server\\ServerMemory":
          case "App\\Notifications\\Server\\ServerDiskUsage":
          case "App\\Notifications\\Server\\ServerLoad":
            commit("user_server_stats/update", notification.stats, {
              root: true,
            });
            break;
        }
      });
  }
};
