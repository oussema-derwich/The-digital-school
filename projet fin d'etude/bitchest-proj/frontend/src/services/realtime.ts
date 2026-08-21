import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

// Use the Vite env variable if provided, otherwise default to window._env
const PUSHER_KEY = import.meta.env.VITE_PUSHER_KEY || (window as any).__PUSHER_KEY || ''
const PUSHER_CLUSTER = import.meta.env.VITE_PUSHER_CLUSTER || (window as any).__PUSHER_CLUSTER || 'mt1'

// If no Pusher key is provided, export a no-op echo-like object to avoid runtime errors
let echo: any = null

if (PUSHER_KEY && PUSHER_KEY.length > 0) {
  // eslint-disable-next-line @typescript-eslint/ban-ts-comment
  // @ts-ignore
  window.Pusher = Pusher

  echo = new Echo({
    broadcaster: 'pusher',
    key: PUSHER_KEY,
    cluster: PUSHER_CLUSTER,
    forceTLS: true,
    encrypted: true,
    // If using self-hosted pusher-compatible server, you can set host/port
    // wsHost: import.meta.env.VITE_PUSHER_HOST || undefined,
    // wsPort: import.meta.env.VITE_PUSHER_PORT ? parseInt(import.meta.env.VITE_PUSHER_PORT) : undefined,
  })
} else {
  // Minimal no-op substitute so app code can call .private().listen() safely
  echo = {
    private: () => ({
      listen: (event: string, cb: any) => {
        // noop in dev when no pusher configured
        // keep for debug
        // console.info('Realtime disabled. Event listened:', event)
        return { stop: () => {} }
      }
    }),
    channel: () => ({
      listen: (_event: string, _cb: any) => ({ stop: () => {} })
    })
  }
}

export default echo

// Example usage elsewhere in the app:
// import echo from '@/services/realtime'
// echo.private(`user.${userId}`).listen('.crypto.alert', (e) => { console.log('alert', e) })
