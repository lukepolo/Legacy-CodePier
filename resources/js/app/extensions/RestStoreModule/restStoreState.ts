import pluralize from "pluralize";

export default function(stateName) {
  let pluralized = pluralize(stateName).toLowerCase();
  let singular = pluralize.singular(stateName).toLowerCase();

  return {
    [singular]: null,
    [pluralized]: [],
  };
}
