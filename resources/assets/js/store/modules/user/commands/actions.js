export const get = () => {
    return Vue.request().get(
        Vue.action('User\UserController@getRunningCommands'),
        'user_commands/setAll'
    )
}
