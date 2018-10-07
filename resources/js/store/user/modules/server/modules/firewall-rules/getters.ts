import { FirewallRulesState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: FirewallRulesState) => {
      return state;
    },
  };
}
