import { clsx } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs) {
  return twMerge(clsx(inputs));
}

export function getCsrfToken() {
  const match = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
  return match ? decodeURIComponent(match.split('=')[1]) : null;
}

export function authHeaders() {
  const headers = {
    'X-Requested-With': 'XMLHttpRequest',
    'Accept': 'application/json',
  };
  const token = getCsrfToken();
  if (token) {
    headers['X-XSRF-TOKEN'] = token;
  }
  return headers;
}
