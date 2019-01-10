import { DeploymentsState } from "./stateInterface";

export default function() {
  return {
    SET_DEPLOYMENT_STEPS: (state: DeploymentsState, data) => {
      state.deployment_steps = data;
    },
    SET_AVAILABLE_DEPLOYMENT_STEPS: (state: DeploymentsState, data) => {
      state.available_deployment_steps = data;
    },
  };
}
