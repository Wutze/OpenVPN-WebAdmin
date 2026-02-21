/**
 * this File is part of OpenVPN-WebAdmin - (c) 2020/2025 OpenVPN-WebAdmin
 */

function t(key, fallback = '') {
  if (window.I18N && typeof window.I18N[key] === 'string' && window.I18N[key] !== '') {
    return window.I18N[key];
  }
  return fallback || key;
}

function tf(key, ...args) {
  let text = t(key, key);
  args.forEach((arg) => {
    text = text.replace('%s', String(arg));
  });
  return text;
}

function ledFormatter(value) {
  return Number(value) === 1
    ? `<div class="mini-led-green" title="${t('_YES', 'Ja')}"></div>`
    : `<div class="mini-led-red" title="${t('_NO', 'Nein')}"></div>`;
}

function onlineFormatter(value) {
  return Number(value) === 1
    ? `<div class="mini-led-green-blink" title="${t('_STATUS_ONLINE', 'Online')}"></div>`
    : `<div class="mini-led-gray" title="${t('_STATUS_OFFLINE', 'Offline')}"></div>`;
}

function formatDateForInput(value) {
  if (!value) return '';
  return String(value).slice(0, 10);
}

window.userDetailFormatter = function (_index, row) {
  return `
    <div class="d-flex justify-content-between align-items-center py-2">
      <div>
        <div><strong>${t('_LABEL_STATUS', 'Status')}:</strong> ${Number(row.user_online) === 1 ? t('_STATUS_ONLINE', 'Online') : t('_STATUS_OFFLINE', 'Offline')}</div>
        <div><strong>${t('_LABEL_ACTIVE', 'Aktiv')}:</strong> ${Number(row.user_enable) === 1 ? t('_YES', 'Ja') : t('_NO', 'Nein')}</div>
      </div>
      <button class="btn btn-sm btn-primary js-open-user-modal" data-user='${encodeURIComponent(JSON.stringify(row))}'>
        ${t('_USERS_DETAIL_TITLE', 'Details')}
      </button>
    </div>
  `;
};

async function postForm(url, payload) {
  const merged = Object.assign({}, payload);
  if (window.CSRF_TOKEN) {
    merged._csrf = window.CSRF_TOKEN;
  }
  const body = new URLSearchParams(merged);
  const response = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
    body,
  });

  let data = {};
  try {
    data = await response.json();
  } catch (_error) {
    data = { status: 'error', message: t('_MSG_INVALID_SERVER_RESPONSE', 'Ungueltige Serverantwort') };
  }

  if (!response.ok || data.status === 'error') {
    throw new Error(data.message || t('_MSG_GENERIC_ERROR', 'Unbekannter Fehler'));
  }

  return data;
}

function showAlert(targetId, type, message) {
  const el = document.getElementById(targetId);
  if (!el) return;
  el.textContent = '';
  const box = document.createElement('div');
  box.className = `alert alert-${type} py-2 mb-0`;
  box.textContent = String(typeof message !== 'undefined' && message !== null ? message : '');
  el.appendChild(box);
}

function initLogout() {
  const logoutBtn = document.getElementById('logoutBtn');
  if (!logoutBtn) return;

  logoutBtn.addEventListener('click', async () => {
    logoutBtn.disabled = true;
    logoutBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>${t('_LOGOUT_PROGRESS', 'Logout...')}`;

    try {
      await fetch('?op=logout', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'logout', _csrf: window.CSRF_TOKEN || '' }),
      });
      window.location.href = '?op=login';
    } catch (_error) {
      logoutBtn.disabled = false;
      logoutBtn.innerHTML = `<i class="bi bi-box-arrow-right"></i> ${t('_LOGOUT_LABEL', 'Logout')}`;
      alert(t('_MSG_LOGOUT_FAILED', 'Logout fehlgeschlagen.'));
    }
  });
}

function initUserTable() {
  const tableEl = document.getElementById('user-table');
  if (!tableEl || !window.jQuery) return;

  const $table = window.jQuery('#user-table');
  $table.bootstrapTable({
    sidePagination: 'server',
    pagination: true,
    search: true,
    sortName: 'uname',
    sortOrder: 'asc',
    url: '?op=data&select=user',
    detailView: true,
    detailFormatter: window.userDetailFormatter,
  });

  const userDetailModalEl = document.getElementById('userDetailModal');
  const userDetailModal = userDetailModalEl ? new bootstrap.Modal(userDetailModalEl) : null;
  let currentDetailRow = null;

  const createForm = document.getElementById('createUserForm');
  if (createForm) {
    createForm.addEventListener('submit', async (event) => {
      event.preventDefault();

      const username = document.getElementById('createUsername').value.trim();
      const password = document.getElementById('createPassword').value;
      const isAdmin = document.getElementById('createIsAdmin').checked ? '1' : '0';

      try {
        await postForm('?op=data&select=user', {
          action: 'create',
          username,
          password,
          is_admin: isAdmin,
        });
        createForm.reset();
        $table.bootstrapTable('refresh');
      } catch (error) {
        alert(error.message);
      }
    });
  }

  document.addEventListener('click', async (event) => {
    const target = event.target;
    if (!(target instanceof HTMLElement)) return;

    try {
      if (target.classList.contains('js-open-user-modal')) {
        const rowRaw = target.dataset.user || '';
        currentDetailRow = JSON.parse(decodeURIComponent(rowRaw));

        const uname = currentDetailRow.uname;
        const netKey = `ovpn-net-${uname}`;
        let netCfg = {};
        try {
          netCfg = JSON.parse(localStorage.getItem(netKey) || '{}');
        } catch (_e) {
          netCfg = {};
        }

        document.getElementById('userDetailModalLabel').textContent = tf('_MSG_USER_DETAILS_FOR', uname);
        document.getElementById('detailUsername').value = uname;
        document.getElementById('detailOnline').value = Number(currentDetailRow.user_online) === 1
          ? t('_STATUS_ONLINE', 'Online')
          : t('_STATUS_OFFLINE', 'Offline');
        document.getElementById('detailIsAdmin').checked = String(currentDetailRow.gname || '').toLowerCase().includes('admin');
        document.getElementById('detailEnabled').checked = Number(currentDetailRow.user_enable) === 1;
        document.getElementById('detailPassword').value = '';
        document.getElementById('detailStartDate').value = formatDateForInput(currentDetailRow.user_start_date);
        document.getElementById('detailEndDate').value = formatDateForInput(currentDetailRow.user_end_date);
        document.getElementById('detailFixedIpEnabled').checked = !!netCfg.enabled;
        document.getElementById('detailFixedIp').value = netCfg.ip || '';
        showAlert('userDetailMessage', 'secondary', t('_MSG_USER_CHANGE_NOTE', 'Aenderungen vornehmen und speichern.'));

        if (userDetailModal) {
          userDetailModal.show();
        }
      }
    } catch (error) {
      alert(error.message);
    }
  });

  const saveBtn = document.getElementById('saveUserDetailBtn');
  const deleteBtn = document.getElementById('deleteUserDetailBtn');
  if (saveBtn) {
    saveBtn.addEventListener('click', async () => {
      if (!currentDetailRow) return;

      const username = currentDetailRow.uname;
      const isAdminCurrent = String(currentDetailRow.gname || '').toLowerCase().includes('admin');
      const enabledCurrent = Number(currentDetailRow.user_enable) === 1;
      const startCurrent = formatDateForInput(currentDetailRow.user_start_date);
      const endCurrent = formatDateForInput(currentDetailRow.user_end_date);

      const isAdminNew = document.getElementById('detailIsAdmin').checked;
      const enabledNew = document.getElementById('detailEnabled').checked;
      const newPassword = document.getElementById('detailPassword').value.trim();
      const startNew = document.getElementById('detailStartDate').value.trim();
      const endNew = document.getElementById('detailEndDate').value.trim();
      const fixedIpEnabled = document.getElementById('detailFixedIpEnabled').checked;
      const fixedIp = document.getElementById('detailFixedIp').value.trim();

      try {
        if (isAdminNew !== isAdminCurrent) {
          await postForm('?op=data&select=user', {
            action: 'set_role',
            username,
            is_admin: isAdminNew ? '1' : '0',
          });
        }

        if (enabledNew !== enabledCurrent) {
          await postForm('?op=data&select=user', {
            action: 'set_enabled',
            username,
            enabled: enabledNew ? '1' : '0',
          });
        }

        if (startNew !== startCurrent || endNew !== endCurrent) {
          await postForm('?op=data&select=user', {
            action: 'set_limits',
            username,
            start_date: startNew,
            end_date: endNew,
          });
        }

        if (newPassword !== '') {
          await postForm('?op=data&select=user', {
            action: 'set_password',
            username,
            password: newPassword,
          });
        }

        const netKey = `ovpn-net-${username}`;
        localStorage.setItem(netKey, JSON.stringify({
          enabled: fixedIpEnabled,
          ip: fixedIp,
        }));

        showAlert('userDetailMessage', 'success', t('_MSG_USER_SAVED', 'Benutzerdaten gespeichert.'));
        $table.bootstrapTable('refresh');
      } catch (error) {
        showAlert('userDetailMessage', 'danger', error.message);
      }
    });
  }

  if (deleteBtn) {
    deleteBtn.addEventListener('click', async () => {
      if (!currentDetailRow) return;
      const username = currentDetailRow.uname;

      if (!confirm(tf('_MSG_DELETE_CONFIRM', username))) return;

      try {
        await postForm('?op=data&select=user', {
          action: 'delete',
          username,
        });

        showAlert('userDetailMessage', 'success', t('_MSG_USER_DELETED', 'Benutzer geloescht.'));
        if (userDetailModal) {
          userDetailModal.hide();
        }
        $table.bootstrapTable('refresh');
      } catch (error) {
        showAlert('userDetailMessage', 'danger', error.message);
      }
    });
  }
}

function initLogTable() {
  const tableEl = document.getElementById('log-table');
  if (!tableEl || !window.jQuery) return;

  const $table = window.jQuery('#log-table');
  if ($table.data('bootstrap.table')) {
    return;
  }

  $table.bootstrapTable({
    sidePagination: 'server',
    pagination: true,
    search: true,
    sortName: 'log_start_time',
    sortOrder: 'desc',
    url: '?op=data&select=log',
  });
}

function renderProfiles(rows) {
  const tbody = document.querySelector('#profiles-table tbody');
  if (!tbody) return;

  tbody.innerHTML = rows.map((row) => {
    const files = (row.files || []).join(', ');
    const zipName = row.zip_exists ? row.zip_file : `<span class="text-muted">${t('_MSG_NOT_AVAILABLE', 'nicht vorhanden')}</span>`;
    const mtime = row.zip_mtime || '-';

    return `
      <tr>
        <td>${row.system}</td>
        <td>${row.file_count}<div class="small text-muted">${files}</div></td>
        <td>${zipName}</td>
        <td>${mtime}</td>
        <td>
          <button class="btn btn-sm btn-outline-primary js-build-zip" data-system="${row.system}">${t('_ACTION_BUILD_ZIP', 'ZIP erstellen')}</button>
          <a class="btn btn-sm btn-success ${row.zip_exists ? '' : 'disabled'}" href="?op=download&system=${encodeURIComponent(row.system)}">${t('_DOWNLOAD', 'Download')}</a>
        </td>
      </tr>
    `;
  }).join('');
}

async function loadProfiles() {
  const response = await fetch('?op=data&select=profiles');
  const data = await response.json();
  if (!response.ok || data.status === 'error') {
    throw new Error(data.message || t('_MSG_PROFILE_LOAD_FAILED', 'Profile konnten nicht geladen werden.'));
  }

  renderProfiles(data.rows || []);
}

function initProfiles() {
  const table = document.getElementById('profiles-table');
  if (!table) return;

  const refreshBtn = document.getElementById('refreshProfilesBtn');

  const refresh = async () => {
    try {
      await loadProfiles();
      showAlert('profilesMessage', 'success', t('_MSG_PROFILES_UPDATED', 'Profile aktualisiert.'));
    } catch (error) {
      showAlert('profilesMessage', 'danger', error.message);
    }
  };

  if (refreshBtn) {
    refreshBtn.addEventListener('click', refresh);
  }

  document.addEventListener('click', async (event) => {
    const target = event.target;
    if (!(target instanceof HTMLElement)) return;
    if (!target.classList.contains('js-build-zip')) return;

    const system = target.dataset.system;
    if (!system) return;

    try {
      await postForm('?op=data&select=profiles', {
        action: 'build_zip',
        system,
      });
      await refresh();
    } catch (error) {
      showAlert('profilesMessage', 'danger', error.message);
    }
  });

  refresh();
}

function initAccount() {
  const form = document.getElementById('accountPasswordForm');
  if (!form) return;

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (newPassword !== confirmPassword) {
      showAlert('accountMessage', 'danger', t('_MSG_NEW_PASSWORD_MISMATCH', 'Neues Passwort und Wiederholung stimmen nicht ueberein.'));
      return;
    }

    try {
      const data = await postForm('?op=data&select=account', {
        action: 'change_password',
        current_password: currentPassword,
        new_password: newPassword,
      });

      form.reset();
      showAlert('accountMessage', 'success', data.message || t('_MSG_PASSWORD_CHANGED', 'Passwort geaendert.'));
    } catch (error) {
      showAlert('accountMessage', 'danger', error.message);
    }
  });
}

async function getJson(url) {
  const response = await fetch(url);
  let data = {};
  try {
    data = await response.json();
  } catch (_error) {
    throw new Error(t('_MSG_INVALID_SERVER_RESPONSE', 'Ungueltige Serverantwort'));
  }

  if (!response.ok || data.status === 'error') {
    throw new Error(data.message || t('_MSG_GENERIC_ERROR', 'Unbekannter Fehler'));
  }

  return data;
}

function renderConfigHistory(rows, system) {
  const tbody = document.querySelector('#configHistoryTable tbody');
  if (!tbody) return;

  tbody.innerHTML = rows.map((row) => `
    <tr>
      <td>
        <div>${row.file}</div>
        <div class="small text-muted">${row.mtime}</div>
      </td>
      <td><span class="text-success">+${row.added || 0}</span> / <span class="text-danger">-${row.removed || 0}</span></td>
      <td class="text-end">
        <button class="btn btn-sm btn-outline-primary js-show-config-diff" data-system="${system}" data-history-file="${row.file}">
          Diff
        </button>
      </td>
    </tr>
  `).join('');
}

function initConfigEditor() {
  const editor = document.getElementById('ovpnEditor');
  if (!editor) return;

  const systemToggleGroup = document.getElementById('configSystemToggleGroup');
  const reloadBtn = document.getElementById('configReloadBtn');
  const saveBtn = document.getElementById('configSaveBtn');
  const diffOutput = document.getElementById('configDiffOutput');
  let currentSystem = '';

  async function loadSystems() {
    const data = await getJson('?op=data&select=config&action=systems');
    const systems = data.systems || [];

    if (systemToggleGroup) {
      systemToggleGroup.innerHTML = systems.map((name, idx) => `
        <button
          type="button"
          class="btn btn-outline-primary ${idx === 0 ? 'active' : ''} js-config-system-toggle"
          data-bs-toggle="button"
          aria-pressed="${idx === 0 ? 'true' : 'false'}"
          data-system="${name}"
        >${name}</button>
      `).join('');
    }
    if (!systems.length) {
      showAlert('configMessage', 'warning', t('_MSG_NO_SYSTEMS', 'Keine Systeme mit client.ovpn gefunden.'));
      editor.value = '';
      currentSystem = '';
      return;
    }

    currentSystem = systems[0];
    await loadConfig(currentSystem);
  }

  async function loadConfig(system) {
    const data = await getJson(`?op=data&select=config&action=get&system=${encodeURIComponent(system)}`);
    editor.value = data.content || '';
    renderConfigHistory(data.history || [], system);
    if (diffOutput) {
      diffOutput.textContent = t('_CONFIG_NO_DIFF', 'Noch kein Vergleich gewaehlt.');
    }
    showAlert('configMessage', 'success', tf('_MSG_CONFIG_LOADED', system));
  }

  if (reloadBtn) {
    reloadBtn.addEventListener('click', async () => {
      if (!currentSystem) return;
      try {
        await loadConfig(currentSystem);
      } catch (error) {
        showAlert('configMessage', 'danger', error.message);
      }
    });
  }

  if (saveBtn) {
    saveBtn.addEventListener('click', async () => {
      if (!currentSystem) return;
      try {
        await postForm('?op=data&select=config', {
          action: 'save',
          system: currentSystem,
          content: editor.value,
        });
        await loadConfig(currentSystem);
        showAlert('configMessage', 'success', t('_MSG_CONFIG_SAVED', 'client.ovpn gespeichert und Historie aktualisiert.'));
      } catch (error) {
        showAlert('configMessage', 'danger', error.message);
      }
    });
  }

  document.addEventListener('click', async (event) => {
    const target = event.target;
    if (!(target instanceof HTMLElement)) return;

    if (target.classList.contains('js-config-system-toggle')) {
      const system = target.dataset.system;
      if (!system) return;

      currentSystem = system;
      document.querySelectorAll('.js-config-system-toggle').forEach((btn) => {
        btn.classList.remove('active');
        btn.setAttribute('aria-pressed', 'false');
      });
      target.classList.add('active');
      target.setAttribute('aria-pressed', 'true');

      try {
        await loadConfig(system);
      } catch (error) {
        showAlert('configMessage', 'danger', error.message);
      }
      return;
    }

    if (!target.classList.contains('js-show-config-diff')) return;

    const system = target.dataset.system;
    const historyFile = target.dataset.historyFile;
    if (!system || !historyFile) return;

    try {
      const data = await getJson(`?op=data&select=config&action=diff&system=${encodeURIComponent(system)}&history_file=${encodeURIComponent(historyFile)}`);
      if (diffOutput) {
        diffOutput.textContent = data.diff || t('_MSG_NO_DIFF', '(keine Unterschiede)');
      }
    } catch (error) {
      showAlert('configMessage', 'danger', error.message);
    }
  });

  loadSystems().catch((error) => showAlert('configMessage', 'danger', error.message));
}

function renderSettingsHistory(rows) {
  const tbody = document.querySelector('#settingsHistoryTable tbody');
  if (!tbody) return;

  tbody.innerHTML = rows.map((row) => `
    <tr>
      <td>
        <div>${row.file}</div>
        <div class="small text-muted">${row.mtime}</div>
      </td>
      <td><span class="text-success">+${row.added || 0}</span> / <span class="text-danger">-${row.removed || 0}</span></td>
      <td class="text-end">
        <button class="btn btn-sm btn-outline-primary js-show-settings-diff" data-history-file="${row.file}">Diff</button>
      </td>
    </tr>
  `).join('');
}

function initSettingsEditor() {
  const editor = document.getElementById('settingsEditor');
  if (!editor) return;

  const sourceEl = document.getElementById('settingsSourceInfo');
  const savePathEl = document.getElementById('settingsSavePath');
  const reloadBtn = document.getElementById('settingsReloadBtn');
  const saveBtn = document.getElementById('settingsSaveBtn');
  const diffOutput = document.getElementById('settingsDiffOutput');

  async function loadSettings() {
    const data = await getJson('?op=data&select=settings&action=get');
    editor.value = data.content || '';
    if (sourceEl) sourceEl.textContent = data.source || '-';
    if (savePathEl) savePathEl.textContent = data.save_path || '-';
    renderSettingsHistory(data.history || []);
    if (diffOutput) diffOutput.textContent = t('_CONFIG_NO_DIFF', 'Noch kein Vergleich gewaehlt.');
    showAlert('settingsMessage', 'success', t('_MSG_SETTINGS_LOADED', 'Server-Konfiguration geladen.'));
  }

  if (reloadBtn) {
    reloadBtn.addEventListener('click', () => {
      loadSettings().catch((error) => showAlert('settingsMessage', 'danger', error.message));
    });
  }

  if (saveBtn) {
    saveBtn.addEventListener('click', async () => {
      try {
        await postForm('?op=data&select=settings', {
          action: 'save',
          content: editor.value,
        });
        await loadSettings();
        showAlert('settingsMessage', 'success', t('_MSG_SETTINGS_SAVED', 'Server-Konfiguration gespeichert und Historie aktualisiert.'));
      } catch (error) {
        showAlert('settingsMessage', 'danger', error.message);
      }
    });
  }

  document.addEventListener('click', async (event) => {
    const target = event.target;
    if (!(target instanceof HTMLElement)) return;
    if (!target.classList.contains('js-show-settings-diff')) return;

    const historyFile = target.dataset.historyFile;
    if (!historyFile) return;

    try {
      const data = await getJson(`?op=data&select=settings&action=diff&history_file=${encodeURIComponent(historyFile)}`);
      if (diffOutput) diffOutput.textContent = data.diff || t('_MSG_NO_DIFF', '(keine Unterschiede)');
    } catch (error) {
      showAlert('settingsMessage', 'danger', error.message);
    }
  });

  loadSettings().catch((error) => showAlert('settingsMessage', 'danger', error.message));
}

function initDashboardStats() {
  const statWrap = document.getElementById('dashboardAdminStats');
  if (!statWrap) return;

  const loadEl = document.getElementById('statLoad');
  const diskEl = document.getElementById('statDisk');
  const usersEl = document.getElementById('statUsers');
  const errorsEl = document.getElementById('statErrors');

  const refresh = async () => {
    try {
      const data = await getJson('?op=data&select=dashboard_stats');
      if (loadEl) loadEl.textContent = Number(data.load_1m || 0).toFixed(2);
      if (diskEl) diskEl.textContent = `${Number(data.disk_used_percent || 0).toFixed(1)}%`;
      if (usersEl) usersEl.textContent = `${Number(data.users_total || 0)} / ${Number(data.users_online || 0)}`;
      if (errorsEl) errorsEl.textContent = String(typeof data.error_count !== 'undefined' ? data.error_count : 0);
    } catch (_error) {
      if (loadEl) loadEl.textContent = '!';
      if (diskEl) diskEl.textContent = '!';
      if (usersEl) usersEl.textContent = '!';
      if (errorsEl) errorsEl.textContent = '!';
    }
  };

  refresh();
}

document.addEventListener('DOMContentLoaded', () => {
  initLogout();
  initUserTable();
  initLogTable();
  initProfiles();
  initAccount();
  initConfigEditor();
  initSettingsEditor();
  initDashboardStats();
});
