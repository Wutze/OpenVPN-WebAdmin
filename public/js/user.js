function t(key, fallback = '') {
  if (window.I18N && typeof window.I18N[key] === 'string' && window.I18N[key] !== '') {
    return window.I18N[key];
  }
  return fallback || key;
}

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

function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
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

function renderProfiles(rows) {
  const tbody = document.querySelector('#profiles-table tbody');
  if (!tbody) return;

  tbody.innerHTML = rows.map((row) => {
    const safeSystem = escapeHtml(row.system);
    const files = (row.files || []).map((f) => escapeHtml(f)).join(', ');
    const zipName = row.zip_exists ? escapeHtml(row.zip_file) : `<span class="text-muted">${t('_MSG_NOT_AVAILABLE', 'nicht vorhanden')}</span>`;
    const mtime = escapeHtml(row.zip_mtime || '-');

    return `
      <tr>
        <td>${safeSystem}</td>
        <td>${Number(row.file_count || 0)}<div class="small text-muted">${files}</div></td>
        <td>${zipName}</td>
        <td>${mtime}</td>
        <td>
          <button class="btn btn-sm btn-outline-primary js-build-zip" data-system="${encodeURIComponent(String(row.system || ''))}">${t('_ACTION_BUILD_ZIP', 'ZIP erstellen')}</button>
          <a class="btn btn-sm btn-success ${row.zip_exists ? '' : 'disabled'}" href="?op=download&system=${encodeURIComponent(String(row.system || ''))}">${t('_DOWNLOAD', 'Download')}</a>
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

    const system = decodeURIComponent(target.dataset.system || '');
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

document.addEventListener('DOMContentLoaded', () => {
  initLogout();
  initProfiles();
  initAccount();
});
