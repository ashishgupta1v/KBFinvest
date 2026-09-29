/**
 * useScrollReveal
 * Lightweight IntersectionObserver composable for scroll-triggered entrance animations.
 * Zero external dependencies — uses CSS classes and native browser API.
 *
 * Usage:
 *   <section v-reveal>...</section>
 *   <div v-reveal="{ delay: 150 }">...</div>
 *
 * Or as a composable:
 *   const { revealRef } = useScrollReveal();
 *   <div :ref="el => revealRef(el)">...</div>
 *
 * CSS classes toggled: .reveal (initial state) -> .reveal-visible (when scrolled into view)
 */
import { onUnmounted } from 'vue';

let _observer = null;

function isSupported() {
  return typeof window !== 'undefined' && 'IntersectionObserver' in window;
}

function getObserver() {
  if (_observer) return _observer;
  if (!isSupported()) return null;

  _observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('reveal-visible');
          _observer.unobserve(entry.target); // fire once
        }
      });
    },
    { threshold: 0.08, rootMargin: '0px 0px -40px 0px' }
  );
  return _observer;
}

export function useScrollReveal() {
  const observed = [];

  function revealRef(el) {
    if (!el || observed.includes(el)) return;
    observed.push(el);

    if (!isSupported()) {
      el.classList.add('reveal-visible');
      return;
    }

    el.classList.add('reveal');
    const obs = getObserver();
    if (obs) obs.observe(el);
  }

  onUnmounted(() => {
    const obs = getObserver();
    if (obs) {
      observed.forEach((el) => {
        try { obs.unobserve(el); } catch (_) {}
      });
    }
  });

  return { revealRef };
}

/**
 * v-reveal directive — attach directly without ref boilerplate:
 *   <div v-reveal>...</div>
 *   <div v-reveal="{ delay: 150 }">...</div>
 */
export const vReveal = {
  mounted(el, binding) {
    if (!isSupported()) {
      el.classList.add('reveal-visible');
      return;
    }

    const delay = binding?.value?.delay ?? 0;
    if (delay) {
      el.style.transitionDelay = `${delay}ms`;
      el.style.animationDelay = `${delay}ms`;
    }
    el.classList.add('reveal');
    const obs = getObserver();
    if (obs) {
      obs.observe(el);
    } else {
      el.classList.add('reveal-visible');
    }
  },
  unmounted(el) {
    const obs = getObserver();
    if (obs) {
      try { obs.unobserve(el); } catch (_) {}
    }
  },
};
