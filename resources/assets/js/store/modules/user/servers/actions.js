export const get = ({ dispatch }) => {
  return Vue.request()
    .get(Vue.action("ServerServerController@index"), "user_servers/setAll")
    .then(servers => {
      _.each(servers, function(server) {
        dispatch("listenTo", server);
      });
    });
};

export const show = (context, server) => {
  return Vue.request().get(
    Vue.action("ServerServerController@show", { server: server }),
    "user_servers/set"
  );
};

export const store = ({ dispatch }, data) => {
  return Vue.request(data)
    .post(Vue.action("ServerServerController@store"), "user_servers/add")
    .then(server => {
      dispatch("listenTo", server);
      app.showSuccess("Your server is in queue to be provisioned");
      return server;
    });
};

export const archive = (context, server) => {
  return Vue.request(server)
    .delete(Vue.action("ServerServerController@destroy", { server: server }), [
      "user_servers/remove",
      "user_site_servers/remove"
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
    "user_servers/setTrashed"
  );
};

export const restore = ({ dispatch }, server) => {
  return Vue.request(server)
    .post(Vue.action("ServerServerController@restore", { server: server }), [
      "user_servers/add",
      "user_servers/removeFromTrash"
    ])
    .then(server => {
      dispatch("listenTo", server);
      return server;
    });
};

export const listenTo = ({ commit, state, dispatch }, server) => {
  if (_.indexOf(state.listening_to, server.id) === -1) {
    commit("listenTo", server);

    if (server.progress < 100) {
      dispatch("user_server_provisioning/getCurrentStep", server.id, {
        root: true
      });
    }

    Echo.private("App.Models.Server.Server." + server.id)
      .listen("ServerServerProvisionStatusChanged", data => {
        commit(
          "user_servers/update",
          {
            response: data.server
          },
          {
            root: true
          }
        );

        commit(
          "user_site_servers/update",
          {
            response: data.server
          },
          {
            root: true
          }
        );

        commit(
          "user_server_provisioning/setCurrentStep",
          {
            response: data.serverCurrentProvisioningStep
          },
          {
            root: true
          }
        );
      })
      .listen("ServerServerSshConnectionFailed", data => {
        commit(
          "user_servers/update",
          {
            response: data.server
          },
          { root: true }
        );
      })
      .listen("ServerServerFailedToCreate", data => {
        commit(
          "user_servers/update",
          {
            response: data.server
          },
          { root: true }
        );
      })
      .listen("ServerServerCommandUpdated", data => {
        commit("user_commands/update", data.command, { root: true });
        commit("events/update", data.command, { root: true });
      })
      .notification(notification => {
        switch (notification.type) {
          case "App\\Notifications\\ServerServerMemory":
          case "App\\Notifications\\ServerServerDiskUsage":
          case "App\\Notifications\\ServerServerLoad":
            commit(
              "user_servers/updateStats",
              {
                server: server.id,
                stats: notification.stats
              },
              {
                root: true
              }
            );
            break;
        }
      });
  }
};
