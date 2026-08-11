
const state = {
    search: '',
    role: '',
    perPage: 10,
    page: 1,
    editingId: null,
    detailsId: null,
    deletingId: null,
    saveLoading: false,
    searchTimer: null,
};

/* ---------- helpers ---------- */
function esc(value) {
    if (value === null || value === undefined) return '';
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function formatDate(value) {
    if (!value) return '—';
    const date = new Date(value);
    return date.toLocaleDateString('nl-NL', {
        day: '2-digit', month: 'short', year: 'numeric',
    });
}

function roleLabel(role) {
    return {
        user: 'Klant',
        technician: 'Technicien',
        admin: 'Beheerder',
    }[role] || role;
}

function roleBadge(role) {
    const styles = {
        user: 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
        technician: 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400',
        admin: 'bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
    };
    const dots = {
        user: 'bg-blue-500',
        technician: 'bg-indigo-500',
        admin: 'bg-purple-500',
    };
    return `<span class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold ${styles[role] || styles.user}">
        <span class="h-1.5 w-1.5 rounded-full ${dots[role] || dots.user}"></span>${roleLabel(role)}
    </span>`;
}

function initial(name) {
    return esc(String(name || '?').charAt(0).toUpperCase());
}

/* ---------- data loading ---------- */
async function load() {
    try {
        const { data } = await axios.get('/admin/klanten/data', {
            params: {
                search: state.search,
                role: state.role,
                per_page: state.perPage,
                page: state.page,
            },
        });

        renderCounts(data.counts);
        renderRows(data.data);
        renderPagination(data.pagination);
    } catch (error) {
        window.SlimmePC.toast.error('Kan klanten niet laden. Probeer het opnieuw.');
    }
}

function renderCounts(counts) {
    $('#countUsers').text('Klanten: ' + counts.users);
    $('#countTechnicians').text('Techniciens: ' + counts.technicians);
    $('#countAdmins').text('Beheerders: ' + counts.admins);
}

function renderRows(rows) {
    const $tbody = $('#klantTableBody');

    if (!rows.length) {
        $tbody.html(`
            <tr>
                <td colspan="6" class="px-6 py-14 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                        </span>
                        <p class="font-semibold" style="color: var(--c-heading)">Geen klanten gevonden</p>
                        <p class="text-xs" style="color: var(--c-muted)">Pas je zoekopdracht of filters aan, of voeg een nieuwe klant toe.</p>
                    </div>
                </td>
            </tr>
        `);
        return;
    }

    $tbody.html(rows.map((k) => `
        <tr class="border-t transition hover:bg-blue-50/40 dark:hover:bg-slate-800/40" style="border-color: rgba(148, 163, 184, 0.12)">
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] text-sm font-bold text-white">
                        ${initial(k.name)}
                    </span>
                    <div class="min-w-0">
                        <p class="truncate font-semibold" style="color: var(--c-heading)">${esc(k.name)}</p>
                        <p class="truncate text-xs" style="color: var(--c-muted)">${esc(k.email)}</p>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4">
                <span class="text-xs font-bold" style="color: var(--c-muted)">${esc(k.klantnummer) || '—'}</span>
            </td>
            <td class="px-6 py-4">
                <p class="text-xs font-medium" style="color: var(--c-body)">${esc(k.street) ? esc(k.street) + ' ' + esc(k.house_number) : '—'}</p>
                <p class="text-xs" style="color: var(--c-muted)">${esc(k.postcode) ? esc(k.postcode) + ', ' : ''}${esc(k.city) || '—'}</p>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                    ${roleBadge(k.role)}
                    <select data-role-select="${k.id}" title="Rol wijzigen"
                            class="w-32 cursor-pointer appearance-none rounded-lg border py-1.5 pl-2.5 pr-7 text-[11px] font-semibold leading-none outline-none transition hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:hover:border-blue-500/60 dark:focus:ring-blue-900/40"
                            style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading); background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='m19.5 8.25-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E&quot;); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 12px;">
                        <option value="user" ${k.role === 'user' ? 'selected' : ''}>Klant</option>
                        <option value="technician" ${k.role === 'technician' ? 'selected' : ''}>Technicien</option>
                        <option value="admin" ${k.role === 'admin' ? 'selected' : ''}>Beheerder</option>
                    </select>
                </div>
            </td>
            <td class="px-6 py-4">
                ${k.is_blocked
                    ? '<span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-bold text-red-600 dark:bg-red-900/30 dark:text-red-400">Geblokkeerd</span>'
                    : '<span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-[11px] font-bold text-green-600 dark:bg-green-900/30 dark:text-green-400"><span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>Actief</span>'}
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-1">
                    <button type="button" data-view="${k.id}" title="Bekijk"
                            class="rounded-lg p-2 transition hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-900/30 dark:hover:text-blue-400" style="color: var(--c-muted)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>

                    <button type="button" data-edit="${k.id}" title="Bewerk"
                            class="rounded-lg p-2 transition hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400" style="color: var(--c-muted)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                    </button>

                    <button type="button" data-block="${k.id}" data-blocked="${k.is_blocked ? 1 : 0}" title="${k.is_blocked ? 'Deblokkeren' : 'Blokkeren'}"
                            class="rounded-lg p-2 transition hover:bg-amber-50 hover:text-amber-600 dark:hover:bg-amber-900/30 dark:hover:text-amber-400" style="color: var(--c-muted)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            ${k.is_blocked
                                ? '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />'
                                : '<path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />'}
                        </svg>
                    </button>

                    <button type="button" data-delete="${k.id}" title="Verwijder"
                            class="rounded-lg p-2 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/30 dark:hover:text-red-400" style="color: var(--c-muted)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>
                </div>
            </td>
        </tr>
    `).join(''));
}

function renderPagination(pag) {
    const $el = $('#klantPagination');

    if (pag.last <= 1) {
        $el.html('');
        return;
    }

    const windowSize = 3;
    let start = Math.max(1, pag.current - windowSize);
    let end = Math.min(pag.last, pag.current + windowSize);
    if (end - start < windowSize * 2) {
        start = Math.max(1, end - (windowSize * 2));
    }

    const pages = [];
    for (let i = start; i <= end; i++) pages.push(i);

    const btn = (label, page, disabled = false, active = false) => `
        <button type="button" data-page="${page}" ${disabled ? 'disabled' : ''}
                class="inline-flex h-10 min-w-10 items-center justify-center rounded-xl px-3 text-sm font-semibold transition
                       ${active
                            ? 'bg-gradient-to-r from-[#075be8] to-[#064bd7] text-white shadow-[0_8px_20px_rgba(0,91,234,0.3)]'
                            : disabled
                                ? 'cursor-not-allowed opacity-40'
                                : 'border hover:border-blue-300 hover:text-blue-600 dark:hover:border-blue-500/50'}
                       " style="${active ? '' : 'border-color: rgba(148, 163, 184, 0.25); color: var(--c-heading)'}">
            ${label}
        </button>`;

    $el.html(`
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-xs" style="color: var(--c-muted)">
                ${pag.total} klanten — pagina ${pag.current} van ${pag.last}
            </p>
            <div class="flex flex-wrap items-center gap-2">
                ${btn('Vorige', pag.current - 1, pag.current <= 1)}
                ${pages.map((p) => btn(p, p, false, p === pag.current)).join('')}
                ${btn('Volgende', pag.current + 1, pag.current >= pag.last)}
            </div>
        </div>
    `);
}

/* ---------- form helpers ---------- */
function clearFormErrors() {
    $('.field-error', '#klantForm').addClass('hidden').text('');
}

function showFormErrors(errors) {
    clearFormErrors();
    if (!errors) return;

    Object.entries(errors).forEach(([field, messages]) => {
        const $input = $(`#klantForm [name="${field}"]`).first();
        const $error = $input.closest('div').find('.field-error').first();
        if ($error.length) {
            $error.text(messages[0]).removeClass('hidden');
        }
    });
}

function resetForm() {
    $('#klantForm')[0].reset();
    $('#klantId').val('');
    clearFormErrors();
}

function setSaveLoading(loading, label) {
    if (loading) {
        $('#klantSaveBtn')
            .prop('disabled', true)
            .addClass('cursor-not-allowed opacity-70')
            .find('[data-btn-label]')
            .html('<span class="spinner spinner-sm mr-2"></span>Bezig met opslaan...');
    } else {
        $('#klantSaveBtn')
            .prop('disabled', false)
            .removeClass('cursor-not-allowed opacity-70')
            .find('[data-btn-label]')
            .text(label || 'Klant opslaan');
    }
}

/* ---------- actions ---------- */
function openCreateModal() {
    state.editingId = null;
    resetForm();
    $('#modal-klantFormModal h3').text('Nieuwe klant');
    $('#modal-klantFormModal h3').next('p').text('Voeg een nieuwe klant toe aan Slimme-PC');
    $('#passwordOptionalHint').show();
    $('#klantSaveBtn').find('[data-btn-label]').text('Klant opslaan');
    window.SlimmePC.modal.open('klantFormModal');
}

async function openEditModal(id) {
    try {
        const { data } = await axios.get(`/admin/klanten/${id}`);
        const k = data.klant;

        state.editingId = id;
        resetForm();

        $('#klantId').val(k.id);
        $('#k-name').val(k.name);
        $('#k-email').val(k.email);
        $('#k-phone').val(k.phone || '');
        $('#k-klantnummer').val(k.klantnummer || '');
        $('#k-street').val(k.street || '');
        $('#k-house').val(k.house_number || '');
        $('#k-postcode').val(k.postcode || '');
        $('#k-city').val(k.city || '');
        $('#k-role').val(k.role);
        $('#k-password').val('');

        $('#modal-klantFormModal h3').text('Klant bewerken');
        $('#modal-klantFormModal h3').next('p').text('Werk de gegevens van deze klant bij');
        $('#passwordOptionalHint').text('(leeg laten = niet wijzigen)');
        $('#klantSaveBtn').find('[data-btn-label]').text('Wijzigingen opslaan');

        window.SlimmePC.modal.open('klantFormModal');
    } catch (error) {
        window.SlimmePC.toast.error('Kon de klantgegevens niet laden.');
    }
}

async function openDetailsModal(id) {
    try {
        const { data } = await axios.get(`/admin/klanten/${id}`);
        const k = data.klant;
        state.detailsId = id;

        const infoRow = (label, value, withCopy = false) => `
            <div class="flex items-center justify-between gap-4 rounded-xl border px-4 py-3" style="border-color: rgba(148, 163, 184, 0.15)">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--c-muted)">${label}</span>
                <span class="text-sm font-semibold text-end" style="color: var(--c-heading)">${esc(value) || '—'}</span>
            </div>`;

        $('#klantDetailsContent').html(`
            <div class="flex flex-col items-center gap-3 border-b pb-5" style="border-color: rgba(148, 163, 184, 0.15)">
                <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-[#075be8] to-[#064bd7] text-2xl font-extrabold text-white shadow-[0_12px_30px_rgba(0,91,234,0.3)]">
                    ${initial(k.name)}
                </span>
                <div class="text-center">
                    <p class="text-lg font-extrabold" style="color: var(--c-heading)">${esc(k.name)}</p>
                    <div class="mt-1.5 flex flex-wrap items-center justify-center gap-2">
                        ${roleBadge(k.role)}
                        ${k.is_blocked
                            ? '<span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-bold text-red-600 dark:bg-red-900/30 dark:text-red-400">Geblokkeerd</span>'
                            : '<span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-[11px] font-bold text-green-600 dark:bg-green-900/30 dark:text-green-400">Actief</span>'}
                        ${k.email_verified_at
                            ? '<span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">E-mail geverifieerd</span>'
                            : ''}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                ${infoRow('E-mailadres', k.email)}
                ${infoRow('Telefoon', k.phone)}
                ${infoRow('Klantnummer', k.klantnummer)}
                ${infoRow('Stad', k.city)}
                ${infoRow('Straat', k.street ? k.street + ' ' + (k.house_number || '') : null)}
                ${infoRow('Postcode', k.postcode)}
                ${infoRow('Lid sinds', formatDate(k.created_at))}
                ${infoRow('Rol', roleLabel(k.role))}
            </div>
        `);

        $('#klantDetailsDeleteBtn').toggle(k.role !== 'admin');
        window.SlimmePC.modal.open('klantDetailsModal');
    } catch (error) {
        window.SlimmePC.toast.error('Kon de klantgegevens niet laden.');
    }
}

function askDelete(id, name) {
    state.deletingId = id;
    $('#deleteKlantName').text(`"${esc(name)}"`);
    window.SlimmePC.modal.close('klantDetailsModal');
    window.SlimmePC.modal.open('klantDeleteModal');
}

async function confirmDelete() {
    if (!state.deletingId) return;

    const $btn = $('#klantDeleteConfirmBtn')
        .prop('disabled', true)
        .addClass('cursor-not-allowed opacity-70')
        .html('<span class="spinner spinner-sm mr-2"></span>Bezig...');

    try {
        await axios.delete(`/admin/klanten/${state.deletingId}`);
        window.SlimmePC.toast.success('Klant succesvol verwijderd.');
        window.SlimmePC.modal.close('klantDeleteModal');
        state.deletingId = null;
        await load();
    } catch (error) {
        window.SlimmePC.toast.error(error.response?.data?.message || 'Verwijderen mislukt.');
    } finally {
        $btn.prop('disabled', false).removeClass('cursor-not-allowed opacity-70')
            .html('Ja, verwijderen');
    }
}

async function toggleBlock(id, isBlocked) {
    try {
        const { data } = await axios.post(`/admin/klanten/${id}/toggle-block`);
        window.SlimmePC.toast.success(data.message);
        await load();
    } catch (error) {
        window.SlimmePC.toast.error(error.response?.data?.message || 'Actie mislukt.');
    }
}

async function changeRole(id, role) {
    const $select = $(`[data-role-select="${id}"]`);
    const previous = $select.data('previous-role');

    try {
        const { data } = await axios.post(`/admin/klanten/${id}/role`, { role });
        window.SlimmePC.toast.success(data.message);
        await load();
    } catch (error) {
        window.SlimmePC.toast.error(error.response?.data?.message || 'Rol wijzigen mislukt.');
        if (previous) $select.val(previous);
    }
}

/* ---------- save (create / update) ---------- */
async function saveKlant() {
    if (state.saveLoading) return;
    state.saveLoading = true;
    setSaveLoading(true);

    const editing = Boolean(state.editingId);
    const url = editing ? `/admin/klanten/${state.editingId}` : '/admin/klanten';
    const method = editing ? 'put' : 'post';

    const payload = {
        name: $('#k-name').val(),
        email: $('#k-email').val(),
        phone: $('#k-phone').val() || null,
        klantnummer: $('#k-klantnummer').val() || null,
        street: $('#k-street').val() || null,
        house_number: $('#k-house').val() || null,
        postcode: $('#k-postcode').val() || null,
        city: $('#k-city').val() || null,
        role: $('#k-role').val(),
        password: $('#k-password').val() || null,
    };

    try {
        const { data } = await axios[method](url, payload);

        if (data.generated_password) {
            window.SlimmePC.toast.success(
                `${data.message} Genereerd wachtwoord: ${data.generated_password}`
            );
        } else {
            window.SlimmePC.toast.success(data.message);
        }

        window.SlimmePC.modal.close('klantFormModal');
        resetForm();
        state.editingId = null;
        await load();
    } catch (error) {
        if (error.response?.status === 422) {
            showFormErrors(error.response.data.errors);
        } else {
            window.SlimmePC.toast.error(error.response?.data?.message || 'Opslaan mislukt.');
        }
    } finally {
        state.saveLoading = false;
        setSaveLoading(false, editing ? 'Wijzigingen opslaan' : 'Klant opslaan');
    }
}

/* ---------- init / bindings ---------- */
$(function () {
    if (!$('#klantTableBody').length) return;

    load();

    // Search (debounced)
    $('#klantSearch').on('input', function () {
        window.clearTimeout(state.searchTimer);
        const value = $(this).val().trim();
        state.searchTimer = window.setTimeout(() => {
            state.search = value;
            state.page = 1;
            load();
        }, 350);
    });

    // Filters
    $('#klantRoleFilter').on('change', function () {
        state.role = $(this).val();
        state.page = 1;
        load();
    });

    $('#klantPerPage').on('change', function () {
        state.perPage = parseInt($(this).val(), 10);
        state.page = 1;
        load();
    });

    // Pagination
    $(document).on('click', '[data-page]', function () {
        state.page = parseInt($(this).data('page'), 10);
        load();
    });

    // Create
    $(document).on('click', '[data-open-create]', openCreateModal);

    // Row actions
    $(document).on('click', '[data-view]', function () {
        openDetailsModal($(this).data('view'));
    });

    $(document).on('click', '[data-edit]', function () {
        openEditModal($(this).data('edit'));
    });

    $(document).on('click', '[data-block]', function () {
        const $btn = $(this);
        toggleBlock($btn.data('block'), $btn.data('blocked') === 1);
    });

    $(document).on('click', '[data-delete]', function () {
        const $row = $(this).closest('tr');
        const name = $row.find('p.font-semibold').first().text();
        askDelete($(this).data('delete'), name);
    });

    // Role select in row
    $(document).on('change', '[data-role-select]', function () {
        const $select = $(this);
        const id = $select.data('role-select');
        const newRole = $select.val();
        $select.data('previous-role', newRole);

        const messages = {
            user: 'Weet je zeker dat je deze klant als gewone klant instelt?',
            technician: 'Weet je zeker dat je deze klant tot technicien wilt promoveren?',
            admin: 'Weet je zeker dat je deze klant tot beheerder wilt promoveren?',
        };

        window.SlimmePC.confirm(
            messages[newRole] || 'Rol wijzigen?',
            () => changeRole(id, newRole),
            () => load()
        );
    });

    // Save
    $(document).on('click', '[data-save-klant]', saveKlant);

    // Enter key in form triggers save
    $('#klantForm').on('keydown', function (e) {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
            saveKlant();
        }
    });

    // Details modal actions
    $(document).on('click', '[data-edit-from-details]', function () {
        if (state.detailsId) {
            window.SlimmePC.modal.close('klantDetailsModal');
            openEditModal(state.detailsId);
        }
    });

    $(document).on('click', '[data-delete-from-details]', function () {
        if (state.detailsId) {
            const name = $('#klantDetailsContent p.text-lg').first().text();
            askDelete(state.detailsId, name);
        }
    });

    // Delete confirm
    $('#klantDeleteConfirmBtn').on('click', confirmDelete);
});
