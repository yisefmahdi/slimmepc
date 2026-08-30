/* Shared table loading — used by all admin tables */
window.AdminTable = window.AdminTable || {};
window.AdminTable.loading = function (tbody, cols) {
    if (!tbody) return;
    tbody.innerHTML = `<tr><td colspan="${cols}" class="px-4 py-16 text-center"><div class="flex flex-col items-center gap-3"><svg class="h-8 w-8 animate-spin text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg><p class="text-sm font-semibold" style="color:var(--c-muted)">Gegevens laden...</p><p class="text-xs" style="color:var(--c-muted)">Even geduld</p></div></td></tr>`;
};
