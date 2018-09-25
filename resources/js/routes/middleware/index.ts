/*
|--------------------------------------------------------------------------
| Route Middleware
|--------------------------------------------------------------------------
| You can setup your global route middleware here, these will execute
| in the order in which you provide them
|
*/
import Auth from "./Auth";
import SiteWorkflowMustBeCompleted from "./SiteWorkflowMustBeCompleted";

export default {
  Auth,
  SiteWorkflowMustBeCompleted,
};
