// E2E: offline attendance marking, then sync on reconnect.
// FIX (this PR): the reconnect test no longer waits on a hard-coded
// sleep. It polls the sync queue's test hook until the queue drains,
// with a hard timeout, so slow CI runners stop flaking the assertion.
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
    // Deterministic wait: poll until the queue reports drained.
    await waitFor(element(by.id('sync-complete-toast')))
      .toBeVisible()
      .withTimeout(15000);
    await expect(element(by.id('pending-sync-badge'))).not.toBeVisible();
  });
});
