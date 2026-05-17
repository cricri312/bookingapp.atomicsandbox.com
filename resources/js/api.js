let onUnauthenticated = null;

export function setUnauthenticatedHandler(fn) {
    onUnauthenticated = fn;
}

export async function apiFetch(path, options = {}) {
    const token = localStorage.getItem('token');

    const res = await fetch('/api' + path, {
        ...options,
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            ...(token ? { Authorization: `Bearer ${token}` } : {}),
            ...(options.headers ?? {}),
        },
    });

    if (res.status === 401) {
        localStorage.removeItem('token');
        onUnauthenticated?.();
    }

    const data = await res.json();
    return { res, data };
}
