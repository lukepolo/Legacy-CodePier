export const clearStuckCommands = (context, site) => {
  return Vue.request().delete(
    Vue.action("Site\SiteServerCommandsController@destroy", { site : site})
  ).then(() => {

      app.showSuccess('You have cleared your stuck commands for this site.')

      return true
  });
};
