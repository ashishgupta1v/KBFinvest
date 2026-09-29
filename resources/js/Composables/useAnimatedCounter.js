/**
 * useAnimatedCounter
 * Smoothly interpolates a numeric value over `duration` ms using requestAnimationFrame.
 * Returns a `display` ref (string) that can be passed directly to a formatter.
 */
import { ref, watch } from 'vue';

export function useAnimatedCounter(sourceRef, formatter = (v) => v, duration = 260) {
  const display = ref(formatter(sourceRef.value));
  const glowing = ref(false);

  let rafId = null;
  let startTime = null;
  let from = sourceRef.value;
  let to = sourceRef.value;

  function animate(ts) {
    if (!startTime) startTime = ts;
    const elapsed = ts - startTime;
    const progress = Math.min(elapsed / duration, 1);
    // Ease out cubic
    const eased = 1 - Math.pow(1 - progress, 3);
    const current = from + (to - from) * eased;
    display.value = formatter(current);

    if (progress < 1) {
      rafId = requestAnimationFrame(animate);
    } else {
      display.value = formatter(to);
      glowing.value = true;
      setTimeout(() => { glowing.value = false; }, 600);
    }
  }

  function startAnimation(newVal) {
    if (rafId) cancelAnimationFrame(rafId);
    from = parseFloat(String(display.value).replace(/[^0-9.-]/g, '')) || from;
    to = newVal;
    startTime = null;
    rafId = requestAnimationFrame(animate);
  }

  watch(sourceRef, (newVal) => {
    if (typeof newVal === 'number' && isFinite(newVal)) {
      startAnimation(newVal);
    } else {
      display.value = formatter(newVal);
    }
  });

  return { display, glowing };
}
