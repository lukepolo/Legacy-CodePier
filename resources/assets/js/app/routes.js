import user from "./user/routes";
import admin from "./admin/routes";
import bitts from "./bitts/routes";
import buoys from "./buoys/routes";
import piles from "./piles/routes";
import sites from "./sites/routes";
import teams from "./teams/routes";
import servers from "./servers/routes";

import { Piles } from "./piles/pages";
import PageNotFound from "../components/PageNotFound";

export default [
  { path: "/", name: "dashboard", component: Piles },
  { path: "/events-bar", name: "events-bar" },
  ...user,
  ...admin,
  ...bitts,
  ...buoys,
  ...piles,
  ...sites,
  ...teams,
  ...servers,

  { path: "*", component: PageNotFound }
];
