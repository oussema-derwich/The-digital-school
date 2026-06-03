import Pusher from "pusher-js";

const pusher = new Pusher("122150b7a5900df41ae9", {
  cluster: "eu",
  forceTLS: true,
});

export default pusher;
