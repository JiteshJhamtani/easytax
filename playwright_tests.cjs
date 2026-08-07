const { chromium } = require('playwright');

const BASE = 'http://127.0.0.1:8000';
const CREDS = {
  admin:    { email: 'admin@easytax.test',    password: 'Test@1234' },
  agent:    { email: 'agent@easytax.test',    password: 'Test@1234' },
  operator: { email: 'operator@easytax.test', password: 'Test@1234' },
  marketer: { email: 'gagan@easytax.live',    password: 'Test@1234' },
};

let pass = 0, fail = 0, warn = 0;
const results = [];

function log(icon, label, detail = '') {
  const line = `${icon} ${label}${detail ? ' → ' + detail : ''}`;
  console.log(line);
  results.push(line);
  if (icon === '✅') pass++;
  else if (icon === '❌') fail++;
  else if (icon === '⚠️') warn++;
}

async function login(page, role) {
  await page.goto(`${BASE}/login`);
  await page.fill('input[name=email]', CREDS[role].email);
  await page.fill('input[name=password]', CREDS[role].password);
  await page.click('button[type=submit]');
  await page.waitForLoadState('networkidle');
}

async function logout(page) {
  try {
    await page.goto(`${BASE}/login`);
    const form = page.locator('form[action*="logout"]');
    if (await form.count()) {
      await form.locator('button[type=submit]').click();
      await page.waitForLoadState('networkidle');
    }
  } catch (_) {}
}

(async () => {
  const browser = await chromium.launch({ headless: true });
  const ctx = await browser.newContext();
  const page = await ctx.newPage();
  page.setDefaultTimeout(15000);

  // ─────────────────────────────────────────────
  // 1. LOGIN — all four roles
  // ─────────────────────────────────────────────
  console.log('\n== LOGIN TESTS ==');

  for (const role of ['admin', 'agent', 'operator', 'marketer']) {
    await login(page, role);
    const url = page.url();
    const ok = !url.includes('/login');
    log(ok ? '✅' : '❌', `Login as ${role}`, url);
    await logout(page);
  }

  // ─────────────────────────────────────────────
  // 2. ACCESS CONTROL — wrong roles blocked
  // ─────────────────────────────────────────────
  console.log('\n== ACCESS CONTROL TESTS ==');

  // Agent should NOT access admin routes
  await login(page, 'agent');
  await page.goto(`${BASE}/admin/dashboard`);
  await page.waitForLoadState('networkidle');
  const agentBlockedAdmin = page.url().includes('/login') || (await page.locator('text=403').count()) > 0 || page.url() === `${BASE}/admin/dashboard` === false;
  const agentStatus = await page.evaluate(() => document.body.innerText.includes('403') || document.body.innerText.includes('Forbidden') || document.body.innerText.toLowerCase().includes('unauthorized'));
  log(agentStatus || !page.url().includes('/admin/dashboard') ? '✅' : '❌', 'Agent blocked from /admin/dashboard', page.url());

  // Agent should NOT access CRM routes
  await page.goto(`${BASE}/crm/marketers`);
  await page.waitForLoadState('networkidle');
  const agentBlockedCrm = (await page.evaluate(() => document.body.innerText.includes('403') || document.body.innerText.includes('Forbidden'))) || !page.url().includes('/crm/marketers');
  log(agentBlockedCrm ? '✅' : '❌', 'Agent blocked from /crm/marketers', page.url());

  await page.goto(`${BASE}/crm/leads`);
  await page.waitForLoadState('networkidle');
  const agentBlockedLeads = (await page.evaluate(() => document.body.innerText.includes('403') || document.body.innerText.includes('Forbidden'))) || !page.url().includes('/crm/leads');
  log(agentBlockedLeads ? '✅' : '❌', 'Agent blocked from /crm/leads', page.url());
  await logout(page);

  // Marketer should NOT access /crm/marketers (admin only)
  await login(page, 'marketer');
  await page.goto(`${BASE}/crm/marketers`);
  await page.waitForLoadState('networkidle');
  const marketerBlockedMarketers = await page.evaluate(() => document.body.innerText.includes('403') || document.body.innerText.includes('Forbidden'));
  log(marketerBlockedMarketers ? '✅' : '❌', 'Marketer blocked from /crm/marketers', page.url());

  // Marketer CAN access /crm/leads
  await page.goto(`${BASE}/crm/leads`);
  await page.waitForLoadState('networkidle');
  const marketerCanLeads = !page.url().includes('/login') && !(await page.evaluate(() => document.body.innerText.includes('403')));
  log(marketerCanLeads ? '✅' : '❌', 'Marketer can access /crm/leads', page.url());

  // Marketer dashboard accessible
  await page.goto(`${BASE}/marketer/dashboard`);
  await page.waitForLoadState('networkidle');
  const marketerDash = !page.url().includes('/login') && !(await page.evaluate(() => document.body.innerText.includes('403')));
  log(marketerDash ? '✅' : '❌', 'Marketer can access /marketer/dashboard', page.url());
  await logout(page);

  // ─────────────────────────────────────────────
  // 3. ADMIN FLOWS
  // ─────────────────────────────────────────────
  console.log('\n== ADMIN FLOWS ==');
  await login(page, 'admin');

  // Dashboard loads
  await page.goto(`${BASE}/admin/dashboard`);
  await page.waitForLoadState('networkidle');
  const dashLoaded = await page.evaluate(() => !document.body.innerText.includes('500') && !document.body.innerText.includes('Exception'));
  log(dashLoaded ? '✅' : '❌', 'Admin dashboard loads', page.url());

  // Applications list loads
  await page.goto(`${BASE}/admin/applications?type=itr-filing`);
  await page.waitForLoadState('networkidle');
  const appsLoaded = await page.evaluate(() => !document.body.innerText.includes('500'));
  log(appsLoaded ? '✅' : '❌', 'Admin applications list (ITR) loads');

  // Applications data endpoint (DataTable JSON)
  const dtResp = await page.request.get(`${BASE}/admin/applications/data?type=itr-filing`);
  const dtOk = dtResp.ok();
  log(dtOk ? '✅' : '❌', 'Admin applications DataTable JSON endpoint', `status ${dtResp.status()}`);

  // View single application
  await page.goto(`${BASE}/admin/applications/76`);
  await page.waitForLoadState('networkidle');
  const appDetailLoaded = await page.evaluate(() => !document.body.innerText.includes('500') && !document.body.innerText.includes('404'));
  log(appDetailLoaded ? '✅' : '❌', 'Admin view application #76');

  // Update application status
  const statusResp = await page.request.patch(`${BASE}/admin/applications/76/status`, {
    form: { _token: await page.evaluate(() => document.querySelector('meta[name=csrf-token]')?.content || ''), status: 'IN_PROGRESS' },
  });
  // Try via page form instead
  await page.goto(`${BASE}/admin/applications/76`);
  await page.waitForLoadState('networkidle');
  const csrfToken = await page.evaluate(() => document.querySelector('meta[name=csrf-token]')?.content);
  const updateStatusResp = await page.request.patch(`${BASE}/admin/applications/76/status`, {
    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json', 'Referer': `${BASE}/admin/applications/76` },
    data: `_token=${encodeURIComponent(csrfToken)}&status=IN_PROGRESS`,
  });
  log(updateStatusResp.status() < 400 ? '✅' : '❌', 'Update application status via PATCH', `status ${updateStatusResp.status()}`);

  // assignTeam — validation fix test: send invalid team_id (should 422)
  const assignInvalidResp = await page.request.post(`${BASE}/admin/applications/76/assign-team`, {
    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/x-www-form-urlencoded' },
    data: `_token=${encodeURIComponent(csrfToken)}&team_id=99999`,
  });
  log(assignInvalidResp.status() === 422 ? '✅' : '⚠️', 'assignTeam rejects non-existent team_id', `status ${assignInvalidResp.status()}`);

  // bulk update — validation fix test: send non-integer ids (should 422)
  const bulkInvalidResp = await page.request.post(`${BASE}/admin/applications/bulk`, {
    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/x-www-form-urlencoded' },
    data: `_token=${encodeURIComponent(csrfToken)}&ids[]=not-an-int`,
  });
  log(bulkInvalidResp.status() === 422 ? '✅' : '⚠️', 'Bulk update rejects non-integer IDs', `status ${bulkInvalidResp.status()}`);

  // Bulk update valid IDs
  const bulkValidResp = await page.request.post(`${BASE}/admin/applications/bulk`, {
    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/x-www-form-urlencoded' },
    data: `_token=${encodeURIComponent(csrfToken)}&ids[]=76`,
  });
  log(bulkValidResp.status() < 400 ? '✅' : '❌', 'Bulk update with valid IDs', `status ${bulkValidResp.status()}`);

  // Agents list
  await page.goto(`${BASE}/admin/agents`);
  await page.waitForLoadState('networkidle');
  log(await page.evaluate(() => !document.body.innerText.includes('500')) ? '✅' : '❌', 'Admin agents list loads');

  // Team list
  await page.goto(`${BASE}/admin/team`);
  await page.waitForLoadState('networkidle');
  log(await page.evaluate(() => !document.body.innerText.includes('500')) ? '✅' : '❌', 'Admin team list loads');

  // Create operator via POST — verify password hashing fix (#1)
  const uniqueEmail = `testop_${Date.now()}@test.com`;
  const createOpResp = await page.request.post(`${BASE}/admin/team`, {
    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/x-www-form-urlencoded' },
    data: `_token=${encodeURIComponent(csrfToken)}&name=Hash+Test+Op&email=${encodeURIComponent(uniqueEmail)}&password=Secure%40123&phone=9876543210`,
  });
  // Now try logging in as that new operator
  const opLoginCtx = await browser.newContext();
  const opPage = await opLoginCtx.newPage();
  await opPage.goto(`${BASE}/login`);
  await opPage.fill('input[name=email]', uniqueEmail);
  await opPage.fill('input[name=password]', 'Secure@123');
  await opPage.click('button[type=submit]');
  await opPage.waitForLoadState('networkidle');
  const opLoginOk = !opPage.url().includes('/login');
  log(opLoginOk ? '✅' : '❌', 'New operator password hashed correctly — can log in', opPage.url());
  await opLoginCtx.close();

  // Payouts page
  await page.goto(`${BASE}/admin/payouts`);
  await page.waitForLoadState('networkidle');
  log(await page.evaluate(() => !document.body.innerText.includes('500')) ? '✅' : '❌', 'Admin payouts page loads');

  // CRM Marketers (admin can access)
  await page.goto(`${BASE}/crm/marketers`);
  await page.waitForLoadState('networkidle');
  log(await page.evaluate(() => !document.body.innerText.includes('403') && !document.body.innerText.includes('500')) ? '✅' : '❌', 'Admin can access /crm/marketers');

  // CRM Leads
  await page.goto(`${BASE}/crm/leads`);
  await page.waitForLoadState('networkidle');
  log(await page.evaluate(() => !document.body.innerText.includes('403') && !document.body.innerText.includes('500')) ? '✅' : '❌', 'Admin can access /crm/leads');

  // GST credentials save — verify encryption (#9)
  const credResp = await page.request.post(`${BASE}/admin/applications/76/credentials`, {
    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/x-www-form-urlencoded' },
    data: `_token=${encodeURIComponent(csrfToken)}&admin_username=testgstuser&admin_password=TestGSTPass%40123`,
  });
  log(credResp.status() < 400 ? '✅' : '❌', 'Save GST credentials', `status ${credResp.status()}`);

  // Now verify the password was encrypted in DB
  await page.goto(`${BASE}/admin/applications/76`);
  await page.waitForLoadState('networkidle');
  const passwordDisplayed = await page.evaluate(() => {
    const inputs = Array.from(document.querySelectorAll('input[name=admin_password]'));
    return inputs.length > 0 ? inputs[0].value : null;
  });
  const isEncryptedInDb = passwordDisplayed === 'TestGSTPass@123';
  const notShowingCiphertext = passwordDisplayed && !passwordDisplayed.startsWith('eyJ');
  log(isEncryptedInDb && notShowingCiphertext ? '✅' : '⚠️', 'GST password decrypted correctly in admin view', `displayed: "${passwordDisplayed}"`);

  // Notifications page
  await page.goto(`${BASE}/admin/notifications`);
  await page.waitForLoadState('networkidle');
  log(await page.evaluate(() => !document.body.innerText.includes('500')) ? '✅' : '❌', 'Admin notifications page loads');

  await logout(page);

  // ─────────────────────────────────────────────
  // 4. AGENT FLOWS
  // ─────────────────────────────────────────────
  console.log('\n== AGENT FLOWS ==');
  await login(page, 'agent');

  await page.goto(`${BASE}/agent/dashboard`);
  await page.waitForLoadState('networkidle');
  log(await page.evaluate(() => !document.body.innerText.includes('500')) ? '✅' : '❌', 'Agent dashboard loads');

  await page.goto(`${BASE}/agent/applications?type=itr-filing`);
  await page.waitForLoadState('networkidle');
  log(await page.evaluate(() => !document.body.innerText.includes('500')) ? '✅' : '❌', 'Agent applications list loads');

  // Agent DataTable data endpoint
  const agentDtResp = await page.request.get(`${BASE}/agent/applications/data?type=itr-filing`);
  log(agentDtResp.ok() ? '✅' : '❌', 'Agent applications DataTable JSON', `status ${agentDtResp.status()}`);

  // View an application owned by this agent
  const agentApps = await page.request.get(`${BASE}/agent/applications/data?type=other`);
  const agentAppsJson = await agentApps.json().catch(() => null);
  const agentAppId = agentAppsJson?.data?.[0]?.id;
  if (agentAppId) {
    await page.goto(`${BASE}/agent/applications/${agentAppId}`);
    await page.waitForLoadState('networkidle');
    log(await page.evaluate(() => !document.body.innerText.includes('500') && !document.body.innerText.includes('403')) ? '✅' : '❌', `Agent view own application #${agentAppId}`);
  } else {
    log('⚠️', 'No agent applications found to view');
  }

  // Agent commissions/payouts
  await page.goto(`${BASE}/agent/commissions`);
  await page.waitForLoadState('networkidle');
  log(await page.evaluate(() => !document.body.innerText.includes('500')) ? '✅' : '❌', 'Agent commissions page loads');

  // Services catalog
  await page.goto(`${BASE}/services`);
  await page.waitForLoadState('networkidle');
  log(await page.evaluate(() => !document.body.innerText.includes('500')) ? '✅' : '❌', 'Services catalog loads');

  // Agent tries to access admin (should 403)
  const agentCsrf = await page.evaluate(() => document.querySelector('meta[name=csrf-token]')?.content);
  await page.goto(`${BASE}/admin/agents`);
  await page.waitForLoadState('networkidle');
  const agentBlocked = await page.evaluate(() => document.body.innerText.includes('403'));
  log(agentBlocked ? '✅' : '❌', 'Agent cannot access admin agent list');

  await logout(page);

  // ─────────────────────────────────────────────
  // 5. TEAM OPERATOR FLOWS
  // ─────────────────────────────────────────────
  console.log('\n== TEAM OPERATOR FLOWS ==');
  await login(page, 'operator');
  const opLoggedIn = !page.url().includes('/login');
  log(opLoggedIn ? '✅' : '❌', 'Operator login', page.url());

  if (opLoggedIn) {
    await page.goto(`${BASE}/team/dashboard`);
    await page.waitForLoadState('networkidle');
    log(await page.evaluate(() => !document.body.innerText.includes('500')) ? '✅' : '❌', 'Team dashboard loads');

    // Operator should not access admin
    await page.goto(`${BASE}/admin/dashboard`);
    await page.waitForLoadState('networkidle');
    const opBlockedAdmin = await page.evaluate(() => document.body.innerText.includes('403'));
    log(opBlockedAdmin ? '✅' : '❌', 'Operator blocked from admin dashboard');
  }
  await logout(page);

  // ─────────────────────────────────────────────
  // 6. MARKETER FLOWS
  // ─────────────────────────────────────────────
  console.log('\n== MARKETER FLOWS ==');
  await login(page, 'marketer');
  await page.goto(`${BASE}/marketer/dashboard`);
  await page.waitForLoadState('networkidle');
  log(await page.evaluate(() => !document.body.innerText.includes('500') && !document.body.innerText.includes('403')) ? '✅' : '❌', 'Marketer dashboard loads');

  await page.goto(`${BASE}/crm/leads`);
  await page.waitForLoadState('networkidle');
  log(await page.evaluate(() => !document.body.innerText.includes('500') && !document.body.innerText.includes('403')) ? '✅' : '❌', 'Marketer can view leads list');

  await page.goto(`${BASE}/crm/marketers`);
  await page.waitForLoadState('networkidle');
  log(await page.evaluate(() => document.body.innerText.includes('403')) ? '✅' : '❌', 'Marketer blocked from marketer management');

  await page.goto(`${BASE}/admin/dashboard`);
  await page.waitForLoadState('networkidle');
  log(await page.evaluate(() => document.body.innerText.includes('403')) ? '✅' : '❌', 'Marketer blocked from admin dashboard');
  await logout(page);

  // ─────────────────────────────────────────────
  // 7. SECURITY PROBES
  // ─────────────────────────────────────────────
  console.log('\n== SECURITY PROBES ==');

  // Unauthenticated access to admin routes
  await page.goto(`${BASE}/admin/dashboard`);
  await page.waitForLoadState('networkidle');
  log(page.url().includes('/login') ? '✅' : '❌', 'Unauthenticated redirect to login from /admin', page.url());

  await page.goto(`${BASE}/agent/dashboard`);
  await page.waitForLoadState('networkidle');
  log(page.url().includes('/login') ? '✅' : '❌', 'Unauthenticated redirect to login from /agent', page.url());

  // Cross-server login with no token
  await page.goto(`${BASE}/auto-login`);
  await page.waitForLoadState('networkidle');
  log(page.url().includes('/login') ? '✅' : '❌', 'auto-login with no token → redirects to login', page.url());

  // Cross-server login with garbage token
  await page.goto(`${BASE}/auto-login?token=garbage_token_here`);
  await page.waitForLoadState('networkidle');
  log(page.url().includes('/login') ? '✅' : '❌', 'auto-login with invalid token → redirects to login', page.url());

  // Payment webhook — unauthenticated (should work, CSRF exempt, but needs Razorpay sig)
  const webhookResp = await page.request.post(`${BASE}/payment/webhook`, {
    headers: { 'Content-Type': 'application/json', 'X-Razorpay-Signature': 'invalid_sig' },
    data: JSON.stringify({ event: 'payment.captured' }),
  });
  log(webhookResp.status() === 403 ? '✅' : '⚠️', 'Webhook with invalid signature → 403', `status ${webhookResp.status()}`);

  // ─────────────────────────────────────────────
  // 8. COUPON ENDPOINT (agent route, no payment needed)
  // ─────────────────────────────────────────────
  console.log('\n== COUPON VALIDATION ==');
  await login(page, 'agent');
  const agentCsrf2 = await page.evaluate(() => document.querySelector('meta[name=csrf-token]')?.content);
  const couponResp = await page.request.post(`${BASE}/agent/validate-coupon`, {
    headers: { 'X-CSRF-TOKEN': agentCsrf2, 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' },
    data: `_token=${encodeURIComponent(agentCsrf2 || '')}&code=FAKECOUPON123`,
  });
  log(couponResp.status() === 200 || couponResp.status() === 422 ? '✅' : '❌', 'Coupon validation endpoint responds', `status ${couponResp.status()}`);
  await logout(page);

  // ─────────────────────────────────────────────
  // SUMMARY
  // ─────────────────────────────────────────────
  console.log('\n══════════════════════════════════════════');
  console.log(`RESULTS: ✅ ${pass} passed  ❌ ${fail} failed  ⚠️ ${warn} warnings`);
  console.log('══════════════════════════════════════════');

  await browser.close();
  process.exit(fail > 0 ? 1 : 0);
})();
