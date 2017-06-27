export const getCurrentStep = (context, server) => {
    return Vue.request().get(
        Vue.action('ServerServerProvisionStepsController@index', {
            server: server,
        }),
        'user_server_provisioning/setCurrentStep',
    );
};

export const setVersion = ({ commit }, data) => {
    commit('setVersion', data.version);
};

export const retry = (context, server) => {
    Vue.request()
        .post(
            Vue.action('ServerServerProvisionStepsController@store', {
                server: server,
            }),
            'user_server_provisioning/setCurrentStep',
        )
        .then(() => {
            app.showSuccess('Retrying to provision the server');
        });
};
