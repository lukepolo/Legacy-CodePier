import { SshKeysState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: SshKeysState) => {
      return state;
    },
  };
}
