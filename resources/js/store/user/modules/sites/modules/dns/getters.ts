import { DnsState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: DnsState) => {
      return state;
    },
  };
}
