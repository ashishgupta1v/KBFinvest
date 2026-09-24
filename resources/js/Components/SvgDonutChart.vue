<script setup>
import { computed } from 'vue';

const props = defineProps({
  parts: {
    type: Array,
    required: true, // [{ l: 'Label', v: 100, c: '#D4A537', t: '₹100' }]
  },
  mid: {
    type: String,
    default: '',
  },
  sub: {
    type: String,
    default: '',
  },
});

const r = 54;
const c = 2 * Math.PI * r;

const total = computed(() => {
  return props.parts.reduce((s, p) => s + (Number(p.v) || 0), 0) || 1;
});

const circles = computed(() => {
  let off = 0;
  return props.parts.map((p) => {
    const len = c * ((Number(p.v) || 0) / total.value);
    const strokeDasharray = `${len.toFixed(2)} ${(c - len).toFixed(2)}`;
    const strokeDashoffset = (-off).toFixed(2);
    off += len;
    return {
      c: p.c,
      strokeDasharray,
      strokeDashoffset,
    };
  });
});
</script>

<template>
  <div class="flex flex-col sm:flex-row items-center gap-4 bg-kb-surface/60 rounded-xl p-4 border border-kb-line w-full">
    <div class="relative w-32 h-32 shrink-0">
      <svg class="w-full h-full transform -rotate-90" viewBox="0 0 132 132" role="img">
        <circle cx="66" cy="66" :r="r" fill="none" class="stroke-kb-line" stroke-width="15" />
        <circle
          v-for="(circ, idx) in circles"
          :key="idx"
          cx="66"
          cy="66"
          :r="r"
          fill="none"
          :stroke="circ.c"
          stroke-width="15"
          :stroke-dasharray="circ.strokeDasharray"
          :stroke-dashoffset="circ.strokeDashoffset"
          class="transition-all duration-300"
        />
      </svg>
      <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-center px-1">
        <span class="text-sm font-bold text-kb-text leading-tight">{{ mid }}</span>
        <span class="text-[9px] uppercase tracking-wider text-kb-muted">{{ sub }}</span>
      </div>
    </div>

    <!-- Legend -->
    <div class="flex flex-col gap-1.5 w-full text-xs">
      <div
        v-for="(p, i) in parts"
        :key="i"
        class="flex items-center justify-between gap-2 py-0.5 border-b border-kb-line/40 last:border-0"
      >
        <div class="flex items-center gap-1.5 truncate">
          <span class="w-2.5 h-2.5 rounded-full shrink-0" :style="{ backgroundColor: p.c }"></span>
          <span class="text-kb-body truncate">{{ p.l }}</span>
        </div>
        <span class="font-semibold text-kb-text font-mono shrink-0">{{ p.t || Math.round((p.v / total) * 100) + '%' }}</span>
      </div>
    </div>
  </div>
</template>
