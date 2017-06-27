export const get = () => {
    return Vue.request().get(
        Vue.action('BuoyAppController@index'),
        'buoys/setAll',
    );
};

export const show = (context, buoyApp) => {
    return Vue.request().get(
        Vue.action('BuoyAppController@show', { buoy_app: buoyApp }),
        'buoys/set',
    );
};

export const store = (context, data) => {
    return Vue.request(data).post('');
};

export const update = (context, data) => {
    return Vue.request(data.form)
        .post(
            Vue.action('BuoyAppController@update', { buoy_app: data.buoy_app }),
            'buoys/set',
        )
        .then(buoy => {
            app.$router.push({ name: 'buoy_market_place' });
            return buoy;
        });
};

export const destroy = (context, buoyApp) => {
    return Vue.request(buoyApp).delete(
        Vue.action('BuoyAppController@destroy', { buoy_app: buoyApp }),
        'buoys/remove',
    );
};

export const installOnServer = (context, data) => {
    return Vue.request(data)
        .post(
            Vue.action('ServerServerBuoyController@store', {
                server: data.server,
            }),
        )
        .then(() => {
            app.$router.push({
                name: 'server_buoys',
                params: { server_id: data.server },
            });
        });
};
