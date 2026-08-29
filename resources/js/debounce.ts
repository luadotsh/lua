let timeoutId: ReturnType<typeof setTimeout> | undefined;

export default function debounce<T extends unknown[]>(
  callback: (...args: T) => void,
  wait = 1000,
) {
  return (...args: T) => {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
      callback.apply(null, args);
    }, wait);
  };
}
