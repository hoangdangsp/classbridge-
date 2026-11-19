// E2E: offline attendance marking, then sync on reconnect.
// KNOWN FLAKE: the reconnect test below waits on a hard-coded sleep.
// On slow CI runners the sync queue has not drained after 3s, so the
// server assertion fails roughly 1 run in 4. This pattern accounts for
// most of the 74 Detox flakes currently in the teacher-app suite.
import { device, element, by, expect } from 'detox';

describe('attendance sync', () => {
  beforeAll(async () => {
    await device.launchApp({ permissions: { notifications: 'YES' } });
  });

  it('marks attendance while online', async () => {
    await element(by.id('session-list-item-0')).tap();
    await element(by.id('student-row-0-attendance')).tap();
    await expect(element(by.id('attendance-saved-banner'))).toBeVisible();
  });

  it('marks attendance offline, then syncs when connectivity returns', async () => {
    await device.setURLBlacklist(['.*']);
    await element(by.id('session-list-item-1')).tap();
    await element(by.id('student-row-0-attendance')).tap();
    await element(by.id('student-row-1-attendance')).tap();
    await expect(element(by.id('pending-sync-badge'))).toBeVisible();

    await device.setURLBlacklist([]);
    // FLAKE SOURCE: fixed sleep instead of waiting for the queue to drain.
    await new Promise((r) => setTimeout(r, 3000));
    await expect(element(by.id('pending-sync-badge'))).not.toBeVisible();
    await expect(element(by.id('sync-complete-toast'))).toBeVisible();
  });
});
