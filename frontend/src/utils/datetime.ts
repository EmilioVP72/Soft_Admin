function parseDate(value: unknown): Date | null {
    if (!value) return null;
    const date = new Date(String(value));
    return Number.isNaN(date.getTime()) ? null : date;
}

function twoDigits(value: number): string {
    return String(value).padStart(2, '0');
}

export function formatDateOnly(value: unknown): string {
    const date = parseDate(value);
    if (!date) return '';

    const day = twoDigits(date.getDate());
    const month = twoDigits(date.getMonth() + 1);
    const year = String(date.getFullYear());
    return `${day}-${month}-${year}`;
}

export function formatDateTime24(value: unknown): string {
    const date = parseDate(value);
    if (!date) return '';

    const datePart = formatDateOnly(date.toISOString());
    const hours = twoDigits(date.getHours());
    const minutes = twoDigits(date.getMinutes());
    return `${datePart} ${hours}:${minutes}`;
}

export function toDateInputValue(value: unknown): string {
    const date = parseDate(value);
    if (!date) return '';

    const day = twoDigits(date.getDate());
    const month = twoDigits(date.getMonth() + 1);
    const year = String(date.getFullYear());
    return `${year}-${month}-${day}`;
}
