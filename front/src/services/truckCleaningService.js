export function needsCleaning(lastCleaningDate) {
    const now = new Date();
    const lastDate = new Date(lastCleaningDate);
    const diffDays = Math.floor((now - lastDate) / (1000 * 60 * 60 * 24));
    return diffDays >= 14;
}