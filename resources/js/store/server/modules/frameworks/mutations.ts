import { FrameworksState } from "./stateInterface";

export default function() {
  return {
    SET_FRAMEWORKS: (state: FrameworksState, data) => {
      // @ts-ignore
      state.frameworks = data;
    },
  };
}
