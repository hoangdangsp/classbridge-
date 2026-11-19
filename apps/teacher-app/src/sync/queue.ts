// Offline sync queue for the teacher app.
// Attendance marks made with no connectivity are appended here and
// flushed, in order, when the network returns. Append-only: entries are
// never edited in place, corrections are new entries.
import NetInfo from '@react-native-community/netinfo';

type SyncEntry = {
  id: string;
  kind: 'attendance';
  payload: Record<string, unknown>;
  createdAt: number;
};

const queue: SyncEntry[] = [];
let flushing = false;

export function enqueue(entry: SyncEntry) {
  queue.push(entry);
}

export function pendingCount() {
  return queue.length;
}

async function flush(send: (e: SyncEntry) => Promise<void>) {
  if (flushing) return;
  flushing = true;
  try {
    while (queue.length > 0) {
      await send(queue[0]);
      queue.shift();
    }
  } finally {
    flushing = false;
  }
}

export function startSyncLoop(send: (e: SyncEntry) => Promise<void>) {
  return NetInfo.addEventListener((state) => {
    if (state.isConnected) {
      void flush(send);
    }
  });
}
