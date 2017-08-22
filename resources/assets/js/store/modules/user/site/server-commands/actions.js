export const clearStuckCommands = (context, site) => {
  return Vue.request()
    .delete(
      Vue.action("SiteSiteServerCommandsController@destroy", { site: site })
    )
    .then(() => {
      app.showSuccess("You have cleared your stuck commands for this site.");

      location.reload();

      return true;
    });
};
