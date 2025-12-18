// services/iaAutoComplete.ts

let isRefreshing = false;
let debounceTimer: number;

export async function generateAutoCompleteSummary(description: string) {
  const token = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content');

  try {
    const response = await fetch('/dashboard/ia/auto-complete', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token ?? '',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
      body: JSON.stringify({
        'in-0': description,
        user_id: '',
      }),
    });

    // 🛡️ Manejo correcto de sesión expirada
    if (response.status === 419) {
      if (!isRefreshing) {
        isRefreshing = true;
        alert('Tu sesión expiró. Se recargará la página.');
        window.location.reload();
      }
      return null;
    }

    if (!response.ok) {
      throw new Error(`Error IA: ${response.status}`);
    }

    return await response.json();

  } catch (error) {
    console.error('IA AutoComplete error:', error);
    throw error;
  }
}
