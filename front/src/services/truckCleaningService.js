export function needsCleaning(lastCleaningDate, deliveryCountSinceLastClean) {
    const now = new Date();
    const lastDate = new Date(lastCleaningDate);
    const diffDays = Math.floor((now - lastDate) / (1000 * 60 * 60 * 24));
    return deliveryCountSinceLastClean >= 10 || diffDays >= 21;
}