export default function($service, stateName) {
  return {
    get({ commit }, parameters) {
      return $service.get(parameters).then((response) => {
        commit(`SET_${stateName.toUpperCase()}`, response.data);
        return response.data;
      });
    },
  };
}
