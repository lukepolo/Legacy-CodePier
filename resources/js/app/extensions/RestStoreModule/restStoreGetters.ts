export default function(stateName) {
  return {
    show: (state) => {
      return (id) => {
        return state[`${stateName}s`].find((data) => {
          return data.id == id;
        });
      };
    },
  };
}
