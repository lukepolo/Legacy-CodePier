export const getPile = function(pildId, attribute) {
  const pile = _.find(this.$store.state.user_piles.piles, {
    id: parseInt(pildId),
  });
  if (pile) {
    if (attribute) {
      return pile[attribute];
    }
    return pile;
  }
  return {};
};
