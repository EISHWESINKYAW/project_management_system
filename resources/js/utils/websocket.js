import Echo from "laravel-echo";
import Pusher from "pusher-js";

class WebSocketService {
    constructor() {
        this.echo = null;
    }

    init(token) {
        if (this.echo) {
            return this.echo;
        }

        this.echo = new Echo({
            broadcaster: "reverb",
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: import.meta.env.VITE_REVERB_HOST,
            wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
            wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
            forceTLS:
                (import.meta.env.VITE_REVERB_SCHEME ?? "https") === "https",
            enabledTransports: ["ws", "wss"],
            authEndpoint: import.meta.env.VITE_REVERB_AUTH_URL,
            auth: {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            },

        });

        return this.echo;
    }

    publicListen(channelInfo, callback) {
        const channel = this.echo
            .channel(channelInfo.channel_name)
            .listen(channelInfo.event_name, (data) => {
                callback(data);
                return data;
            });
        return channel;
    }

    privateListen(channelInfo, callback) {
        const channel = this.echo
            .private(channelInfo.channel_name)
            .listen(channelInfo.event_name, (data) => {
                callback(data);
                return data;
            });
        return channel;
    }
}

const webSocket = new WebSocketService();
export default webSocket;
