import pluralize from "pluralize";

export default function(stateName) {
  let pluralized = pluralize(stateName).toLowerCase();
  let singular = pluralize.singular(stateName).toLowerCase();

  return {
    show: (state) => {
      return (id) => {
        return state[pluralized].find((data) => {
          return data.id == id;
        });
      };
    },
  };
}
