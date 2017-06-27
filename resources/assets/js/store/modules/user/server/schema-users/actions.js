export const get = (context, server) => {
    return Vue.request().get(
        Vue.action('ServerServerSchemaUserController@index', {
            server: server,
        }),
        'user_server_schema_users/setAll',
    );
};

export const store = (context, data) => {
    return Vue.request(data).post(
        Vue.action('ServerServerSchemaUserController@store', {
            server: data.server,
        }),
        'user_server_schema_users/add',
    );
};

// TODO - there will be an update

export const destroy = (context, data) => {
    return Vue.request(data).delete(
        Vue.action('ServerServerSchemaUserController@destroy', {
            server: data.server,
            schema_user: data.schema_user,
        }),
        'user_server_schema_users/remove',
    );
};
