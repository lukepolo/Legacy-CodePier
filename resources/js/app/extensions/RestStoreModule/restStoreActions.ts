import pluralize from "pluralize";

export default function($service, stateName) {
  let pluralized = pluralize(stateName).toLowerCase();
  let singular = pluralize.singular(stateName).toLowerCase();

  return {
    get({ commit }, parameters) {
      return $service.get(parameters).then((response) => {
        commit(`SET_${pluralized.toUpperCase()}`, response.data);
        return response.data;
      });
    },
    create({ commit }, { data, parameters }) {
      return $service.create(parameters, data).then((response) => {
        commit(`CREATED_${singular.toUpperCase()}`, response.data);
        return response.data;
      });
    },
    show({ commit }, parameters) {
      return $service.show(parameters).then((response) => {
        commit(`SHOW_${singular.toUpperCase()}`, response.data);
        return response.data;
      });
    },
    update({ commit }, { parameters, data }) {
      return $service.update(parameters, data).then((response) => {
        commit(`UPDATED_${singular.toUpperCase()}`, response.data);
        return response.data;
      });
    },
    destroy({ commit }, parameters) {
      $service.destroy(parameters).then((response) => {
        commit(`DESTROYED_${singular.toUpperCase()}`, parameters);
        return response.data;
      });
    },
  };
}
