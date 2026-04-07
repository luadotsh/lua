let timeoutId = null;

export default function debounce(callback, wait = 1000) {
  return (...args) => {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
      callback.apply(null, args);
    }, wait);
  };
}
