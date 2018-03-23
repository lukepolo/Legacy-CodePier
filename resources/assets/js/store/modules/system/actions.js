export const setVersion = ({ commit }, data) => {
  commit("setVersion", data.version);
};

export const markAnnouncementRead = () => {
  return Vue.request().post(
    Vue.action("AnnouncementsController@store"),
    "user/set",
  );
};
