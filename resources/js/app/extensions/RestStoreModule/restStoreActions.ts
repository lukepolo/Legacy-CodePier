export default function($service, stateName) {
  return {
    get({ commit }) {
      return $service.get().then((response) => {
        commit(`SET_${stateName.toUpperCase()}`, response.data);
        return response.data;
      });
    },
  };
}
