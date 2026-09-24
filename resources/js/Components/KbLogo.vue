<script setup>
defineProps({
  isScrolled: {
    type: Boolean,
    default: false,
  },
  showTagline: {
    type: Boolean,
    default: true,
  },
  animated: {
    type: Boolean,
    default: true,
  },
  alt: {
    type: String,
    default: 'KB Finvest — Invest Smart. Grow Secure.',
  },
});
</script>

<template>
  <div
    :class="[
      'inline-flex items-center select-none transition-all duration-300',
      animated ? 'hover:scale-[1.02] active:scale-[0.98]' : ''
    ]"
    role="img"
    :aria-label="alt"
  >
    <!-- Dark Luxury Brand Logo -->
    <img
      src="/images/kb-finvest-logo-transparent.png"
      :alt="alt"
      class="w-auto h-full object-contain pointer-events-none select-none"
      width="368"
      height="129"
      loading="eager"
      decoding="async"
    />
  </div>
</template>
