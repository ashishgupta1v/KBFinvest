<script setup>
import { computed } from 'vue';

const props = defineProps({
  series: {
    type: Array,
    required: true, // [{ cls: 'ln-a', vals: [...] }, { cls: 'ln-b', vals: [...], fill: false }]
  },
  xLabels: {
    type: Array,
    default: () => ['Start', 'Mid', 'End'],
  },
  title: {
    type: String,
    default: 'Projection Chart',
  },
});

function niceMax(v) {
  if (!(v > 0)) return 1;
  const e = Math.pow(10, Math.floor(Math.log10(v)));
  const m = v / e;
  return (m <= 1 ? 1 : m <= 2 ? 2 : m <= 2.5 ? 2.5 : m <= 5 ? 5 : 10) * e;
}

function shortInr(n) {
  n = Math.round(Number(n) || 0);
  const a = Math.abs(n);
  if (a >= 1e7) return '₹' + (n / 1e7).toFixed(2).replace(/\.00$/, '') + ' cr';
  if (a >= 1e5) return '₹' + (n / 1e5).toFixed(2).replace(/\.00$/, '') + ' L';
  if (a >= 1e3) return '₹' + (n / 1e3).toFixed(0) + 'k';
  return '₹' + n;
}

const W = 460;
const H = 200;
const L = 72;
const R = 16;
const T = 16;
const B = 28;
const pw = W - L - R;
const ph = H - T - B;

const maxVal = computed(() => {
  let m = 0;
  props.series.forEach((s) => {
    s.vals.forEach((v) => {
      if (v > m) m = v;
    });
  });
  return niceMax(m);
});

const gridLines = computed(() => {
  const lines = [];
  for (let g = 0; g <= 3; g++) {
    const yv = (maxVal.value * g) / 3;
    const yy = T + ph - (yv / maxVal.value) * ph;
    lines.push({ yv, yy, label: shortInr(yv) });
  }
  return lines;
});

const paths = computed(() => {
  if (!props.series.length || !props.series[0].vals.length) return [];
  const n = props.series[0].vals.length;
  const X = (i) => L + (n < 2 ? pw / 2 : (i / (n - 1)) * pw);
  const Y = (v) => T + ph - (Math.max(0, v) / maxVal.value) * ph;

  return props.series.map((s) => {
    const pts = s.vals.map((v, i) => `${X(i).toFixed(1)} ${Y(v).toFixed(1)}`);
    const linePath = `M ${pts.join(' L ')}`;
    let areaPath = '';
    if (s.fill !== false) {
      areaPath = `M ${X(0).toFixed(1)} ${Y(0).toFixed(1)} L ${pts.join(' L ')} L ${X(n - 1).toFixed(1)} ${Y(0).toFixed(1)} Z`;
    }
    const lastX = X(n - 1);
    const lastY = Y(s.vals[n - 1]);
    return {
      linePath,
      areaPath,
      color: s.cls === 'ln-a' ? '#D4A537' : '#3B7BD0',
      gradId: s.cls === 'ln-a' ? 'cgA' : 'cgB',
      lastX,
      lastY,
    };
  });
});
</script>

<template>
  <div class="w-full overflow-hidden bg-kb-surface/60 rounded-xl p-3 border border-kb-line">
    <div class="text-xs font-medium text-kb-muted mb-2">{{ title }}</div>
    <svg class="w-full h-auto" :viewBox="`0 0 ${W} ${H}`" role="img" :aria-label="title">
      <defs>
        <linearGradient id="cgA" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stop-color="#D4A537" stop-opacity="0.38" />
          <stop offset="100%" stop-color="#D4A537" stop-opacity="0" />
        </linearGradient>
        <linearGradient id="cgB" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stop-color="#3B7BD0" stop-opacity="0.32" />
          <stop offset="100%" stop-color="#3B7BD0" stop-opacity="0" />
        </linearGradient>
      </defs>

      <!-- Grid lines & Axis labels -->
      <g>
        <line
          v-for="g in gridLines"
          :key="g.yv"
          :x1="L"
          :y1="g.yy"
          :x2="W - R"
          :y2="g.yy"
          class="stroke-kb-line"
          stroke-dasharray="3 3"
        />
        <text
          v-for="g in gridLines"
          :key="'lbl-' + g.yv"
          :x="L - 8"
          :y="g.yy + 3.5"
          text-anchor="end"
          class="fill-kb-muted text-[10px] select-none font-mono"
        >
          {{ g.label }}
        </text>
      </g>

      <!-- Paths -->
      <g v-for="(p, i) in paths" :key="i">
        <path v-if="p.areaPath" :d="p.areaPath" :fill="`url(#${p.gradId})`" />
        <path :d="p.linePath" fill="none" :stroke="p.color" stroke-width="2.5" stroke-linecap="round" />
        <circle :cx="p.lastX" :cy="p.lastY" r="4" :fill="p.color" class="stroke-kb-bg" stroke-width="1.5" />
      </g>

      <!-- X-axis Labels -->
      <g>
        <text
          v-for="(lb, idx) in xLabels"
          :key="idx"
          :x="L + (idx / (xLabels.length - 1)) * pw"
          :y="H - 8"
          :text-anchor="idx === 0 ? 'start' : idx === xLabels.length - 1 ? 'end' : 'middle'"
          class="fill-kb-muted text-[10px] select-none"
        >
          {{ lb }}
        </text>
      </g>
    </svg>
  </div>
</template>
