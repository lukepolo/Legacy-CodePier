export const get = (context, server) => {
    return Vue.request().get(
        Vue.action('ServerServerSshKeyController@index', { server: server }),
        'user_server_ssh_keys/setAll',
    );
};

export const store = (context, data) => {
    return Vue.request(data).post(
        Vue.action('ServerServerSshKeyController@store', {
            server: data.server,
        }),
        'user_server_ssh_keys/add',
    );
};

export const destroy = (context, data) => {
    return Vue.request(data).delete(
        Vue.action('ServerServerSshKeyController@destroy', {
            server: data.server,
            ssh_key: data.ssh_key,
        }),
        'user_server_ssh_keys/remove',
    );
};
