// services/iaAutoComplete.ts
export async function generateAutoCompleteSummary(description: string) {
  // Tomar token CSRF del meta tag
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  // Petición POST al endpoint Laravel
  const response = await fetch('/dashboard/ia/auto-complete', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': token || '', // Incluir token CSRF
    },
    body: JSON.stringify({
      'in-0': description,
      'user_id': '' // opcional, igual que en el backend
    }),
  });

  if (!response.ok) {
    throw new Error(`Error en autocompletado IA: ${response.status}`);
  }

  // Retorna la respuesta completa en JSON
  return await response.json();
}
