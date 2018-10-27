import ApiErrors from "./ApiErrors";
import LoadingScreen from "./LoadingScreen";
import ValidationErrors from "./ValidationErrors";
import JwtGuardMiddleware from "varie-authentication-plugin/lib/guards/jwt/JwtGuardMiddleware";

export default [ApiErrors, ValidationErrors, LoadingScreen, JwtGuardMiddleware];
