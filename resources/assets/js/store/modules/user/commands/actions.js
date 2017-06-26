export const get = () => {
  return Vue.request().get(
    Vue.action('UserUserController@getRunningCommands'),
    'user_commands/setAll',
  );
};
