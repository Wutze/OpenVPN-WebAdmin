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

function initProfiles() {
  const downloads = document.querySelectorAll('.js-profile-download');
  if (downloads.length === 0) return;

  const table = document.getElementById('profile-users-table');
  const selection = document.getElementById('profileUserSelection');
  let selectedUid = null;
  let selectedName = '';
  const escapeAttribute = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;',
  })[char]);

  const updateDownloadLinks = () => {
    downloads.forEach((link) => {
      const url = new URL(link.href, window.location.href);
      if (selectedUid === null) {
        url.searchParams.delete('uid');
      } else {
        url.searchParams.set('uid', selectedUid);
      }
      link.href = url.toString();
    });
    if (selection) {
      selection.textContent = selectedUid === null
        ? t('_PROFILE_USERS_DOWNLOADING_SELF', 'Download fuer den eigenen Benutzer.')
        : `${t('_PROFILE_USERS_SELECTED', 'Ausgewaehlter Benutzer')}: ${selectedName} (UID ${selectedUid})`;
    }
  };

  updateDownloadLinks();
  if (!table || !window.jQuery || typeof window.jQuery.fn.bootstrapTable !== 'function') return;

  const $table = window.jQuery(table);
  $table.bootstrapTable({
    sidePagination: 'server',
    pagination: true,
    search: true,
    pageSize: 10,
    pageList: [10, 25, 50, 100],
    queryParams: (params) => ({
      limit: params.limit,
      offset: params.offset,
      search: params.search,
    }),
    url: '?op=data&select=profile_users',
    columns: [
      { field: 'uid', title: t('_PROFILE_USERS_UID', 'UID'), sortable: false },
      { field: 'uname', title: t('_PROFILE_USERS_NAME', 'Benutzername'), sortable: false },
      {
        field: 'select',
        title: t('_PROFILE_USERS_ACTION', 'Auswahl'),
        formatter: (_value, row) => `<button type="button" class="btn btn-sm btn-outline-primary js-select-profile-user" data-uid="${Number(row.uid)}" data-name="${escapeAttribute(row.uname)}">${t('_PROFILE_USERS_SELECT', 'Auswaehlen')}</button>`,
      },
    ],
  });

  table.addEventListener('click', (event) => {
    const target = event.target;
    if (!(target instanceof HTMLElement)) return;
    const button = target.closest('.js-select-profile-user');
    if (!button) return;

    const uid = Number(button.dataset.uid);
    if (!Number.isSafeInteger(uid) || uid <= 0) return;
    selectedUid = String(uid);
    selectedName = button.dataset.name || '';
    updateDownloadLinks();
  });

  const ownUserButton = document.getElementById('profileOwnUserBtn');
  if (ownUserButton) {
    ownUserButton.addEventListener('click', () => {
      selectedUid = null;
      selectedName = '';
      updateDownloadLinks();
    });
  }
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
