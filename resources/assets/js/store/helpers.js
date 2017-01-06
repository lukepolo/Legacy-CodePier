export function action (action, parameters) {
  return laroute.action(action, parameters)
}

export function route (route, parameters) {
  return laroute.route(route, { planet: 'world' })
}
