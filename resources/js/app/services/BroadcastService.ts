import Echo from "laravel-echo";
import { injectable, inject } from "inversify";

@injectable()
export default class BroadcastService {
  private $echo;

  constructor(@inject("$config") $config) {
    // @ts-ignore
    window.io = require("socket.io-client");

    let env = $config.get("env");

    this.$echo = new Echo({
      broadcaster: "socket.io",
      key: env.PUSHER_APP_KEY,
      host:
        env.ENV === "local"
          ? `${window.location.hostname}:6001`
          : "https://ws.codepier.io:6001",
    });
  }

  listen(channel, event, callback) {
    return this.$echo.channel("app").listen(event, callback);
  }

  isConnected() {
    return this.$echo.connector.socket.connected;
  }
}
