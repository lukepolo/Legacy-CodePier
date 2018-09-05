export default function($service, stateName) {
  return {
    get({ commit }, parameters) {
      return $service.get(parameters).then((response) => {
        commit(`SET_${stateName.toUpperCase()}S`, response.data);
        return response.data;
      });
    },
    create({ commit }, { data, parameters }) {
      return $service.create(parameters, data).then((response) => {
        commit(`CREATED_${stateName.toUpperCase()}`, response.data);
        return response.data;
      });
    },
    show({ commit }, parameters) {
      return $service.show(parameters).then((response) => {
        commit(`SHOW_${stateName.toUpperCase()}`, response.data);
        return response.data;
      });
    },
    update({ commit }, { parameters, data }) {
      return $service.update(parameters, data).then((response) => {
        commit(`UPDATED_${stateName.toUpperCase()}`, response.data);
        return response.data;
      });
    },
    destroy({ commit }, parameters) {
      $service.destroy(parameters).then((response) => {
        commit(`DESTROYED_${stateName.toUpperCase()}`, parameters);
        return response.data;
      });
    },
  };
}
