import StateServiceInterface from "varie/lib/state/StateServiceInterface";

export default function(to, from) {
  console.info($app.make<StateServiceInterface>('$store').getStore().dispatch('auth/me'))
  return true;
}
