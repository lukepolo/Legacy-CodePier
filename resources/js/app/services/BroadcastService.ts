import Echo from "laravel-echo";
import { injectable, inject } from "inversify";

@injectable()
export default class BroadcastService {
  private $echo;

  constructor(@inject("ConfigService") configService) {
    // @ts-ignore
    window.io = require("socket.io-client");

    this.$echo = new Echo({
      broadcaster: "socket.io",
      key: configService.get("services.PUSHER_APP_KEY"),
      host:
        configService.get("app.ENV") === "local"
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
