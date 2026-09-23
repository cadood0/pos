function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function showAlert(message, type) {
    const box = document.getElementById('userAlert');

    if (!box) {
        return;
    }

    box.className = 'alert alert-' + type;
    box.textContent = message;
}

function statusLabel(status) {
    return Number(status) === 1 ? 'Active' : 'Inactive';
}

async function loadUsers() {
    const tbody = document.getElementById('userRows');

    if (!tbody) {
        return;
    }

    try {
        const response = await ApiClient.request('/api/users');
        const rows = response.data || [];

        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-muted">No users yet.</td></tr>';
            return;
        }

        tbody.innerHTML = rows.map((user) => `
            <tr>
                <td>${user.id}</td>
                <td>${escapeHtml(user.name)}</td>
                <td>${escapeHtml(user.email)}</td>
                <td>${escapeHtml(user.role || '')}</td>
                <td>${statusLabel(user.status)}</td>
                <td class="text-end">
                    <button
                        class="btn btn-sm btn-outline-danger js-delete-user"
                        data-id="${user.id}">
                        Delete
                    </button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        showAlert(
            (error.payload && error.payload.message) || 'Unable to load users.',
            'danger'
        );
    }
}

async function loadRoles() {
    const select = document.getElementById('role_id');

    if (!select) {
        return;
    }

    try {
        const response = await ApiClient.request('/api/roles');
        const rows = response.data || [];

        rows.forEach((role) => {
            const option = document.createElement('option');
            option.value = String(role.id);
            option.textContent = role.name;
            select.appendChild(option);
        });
    } catch (error) {
        showAlert(
            (error.payload && error.payload.message) || 'Unable to load roles.',
            'danger'
        );
    }
}

document.addEventListener('submit', async (event) => {
    const form = event.target.closest('#userCreateForm');

    if (!form) {
        return;
    }

    event.preventDefault();

    try {
        await ApiClient.request('/api/users', {
            method: 'POST',
            body: JSON.stringify({
                name: form.elements.name.value,
                email: form.elements.email.value,
                password: form.elements.password.value,
                role_id: form.elements.role_id.value,
                status: Number(form.elements.status.value)
            })
        });

        window.location.href = window.APP.usersUrl;
    } catch (error) {
        showAlert(
            (error.payload && error.payload.message) || 'Unable to register user.',
            'danger'
        );
    }
});

document.addEventListener('click', async (event) => {
    const button = event.target.closest('.js-delete-user');

    if (!button) {
        return;
    }

    if (!confirm('Delete this user?')) {
        return;
    }

    try {
        await ApiClient.request('/api/users/' + button.dataset.id, {
            method: 'DELETE'
        });

        await loadUsers();
        showAlert('User deleted.', 'success');
    } catch (error) {
        showAlert(
            (error.payload && error.payload.message) || 'Delete failed.',
            'danger'
        );
    }
});

document.addEventListener('DOMContentLoaded', async () => {
    await loadUsers();
    await loadRoles();
});
