import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: "pusher",
  key: process.env.VUE_APP_PUSHER_APP_KEY,
  cluster: process.env.VUE_APP_PUSHER_APP_CLUSTER,
  encrypted: true,
});

// Écouter les notifications en temps réel
window.Echo.channel("notifications").listen(
  "new-notification",
  (notification) => {
    this.$store.dispatch("notification/addNotification", notification);
  }
);
