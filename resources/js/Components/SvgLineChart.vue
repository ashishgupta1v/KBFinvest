<script setup>
import { computed, ref } from 'vue';

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
      vals: s.vals,
      X,
      Y,
    };
  });
});

// ── Interactive crosshair tooltip ──
const svgRef = ref(null);
const tooltip = ref(null); // { x, y, svgX, items: [{color, val}] }

function getNearestIndex(svgX) {
  if (!props.series.length || !props.series[0].vals.length) return -1;
  const n = props.series[0].vals.length;
  const raw = (svgX - L) / pw;
  const idx = Math.round(raw * (n - 1));
  return Math.max(0, Math.min(n - 1, idx));
}

function getSvgPos(event) {
  if (!svgRef.value) return null;
  const svg = svgRef.value;
  const rect = svg.getBoundingClientRect();
  const clientX = event.touches ? event.touches[0].clientX : event.clientX;
  const clientY = event.touches ? event.touches[0].clientY : event.clientY;
  // Map to SVG coordinate space
  const scaleX = W / rect.width;
  const svgX = (clientX - rect.left) * scaleX;
  return { svgX, clientX, clientY };
}

function handleMouseMove(event) {
  const pos = getSvgPos(event);
  if (!pos) return;
  const { svgX, clientX, clientY } = pos;

  if (svgX < L || svgX > W - R) {
    tooltip.value = null;
    return;
  }

  const idx = getNearestIndex(svgX);
  if (idx === -1) return;

  const n = props.series[0].vals.length;
  const snapX = L + (n < 2 ? pw / 2 : (idx / (n - 1)) * pw);

  // Interpolate year label
  const totalYears = props.xLabels.length >= 2 ? props.xLabels : ['Now', 'End'];
  const progress = idx / Math.max(1, n - 1);

  // Build tooltip items for each series
  const items = paths.value.map((p) => ({
    color: p.color,
    val: shortInr(p.vals[idx] || 0),
    label: p.color === '#D4A537' ? (props.series.find(s => s.cls === 'ln-a') ? 'Growth' : '') : 'Invested',
  }));

  tooltip.value = {
    svgX: snapX,
    crosshairY1: T,
    crosshairY2: T + ph,
    items,
    xLabel: props.xLabels[Math.round(progress * (props.xLabels.length - 1))] || '',
    clientX,
    clientY,
  };
}

function handleLeave() {
  tooltip.value = null;
}
</script>

<template>
  <div class="w-full overflow-hidden bg-kb-surface/60 rounded-xl p-3 border border-kb-line relative">
    <div class="text-xs font-medium text-kb-muted mb-2">{{ title }}</div>
    <div class="relative">
      <svg
        ref="svgRef"
        class="w-full h-auto cursor-crosshair select-none"
        :viewBox="`0 0 ${W} ${H}`"
        role="img"
        :aria-label="title"
        @mousemove="handleMouseMove"
        @mouseleave="handleLeave"
        @touchmove.prevent="handleMouseMove"
        @touchend="handleLeave"
      >
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

        <!-- Interactive crosshair -->
        <g v-if="tooltip">
          <!-- Vertical dashed line -->
          <line
            :x1="tooltip.svgX"
            :y1="tooltip.crosshairY1"
            :x2="tooltip.svgX"
            :y2="tooltip.crosshairY2"
            stroke="rgba(212,165,55,0.55)"
            stroke-width="1"
            stroke-dasharray="4 3"
          />
          <!-- Dot markers on each series at current index -->
          <circle
            v-for="(item, j) in tooltip.items"
            :key="'dot-' + j"
            :cx="tooltip.svgX"
            :cy="paths[j]?.Y(paths[j]?.vals[getNearestIndex(tooltip.svgX)] || 0)"
            r="5"
            :fill="item.color"
            stroke="#070E1C"
            stroke-width="2"
          />
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

      <!-- Floating glassmorphism tooltip card -->
      <Transition
        enter-active-class="transition duration-100 ease-out"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition duration-75 ease-in"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
      >
        <div
          v-if="tooltip"
          class="pointer-events-none absolute z-30 min-w-[110px] rounded-xl border border-amber-500/40 bg-kb-bg/90 backdrop-blur-md px-3 py-2 shadow-xl shadow-black/40"
          :style="{
            top: '4px',
            left: `clamp(4px, ${((tooltip.svgX - L) / (W - L - R)) * 100}%, calc(100% - 120px))`,
            transform: 'translateX(-50%)',
          }"
        >
          <div class="text-[10px] font-bold text-kb-accent mb-1 tracking-wide">{{ tooltip.xLabel }}</div>
          <div
            v-for="(item, j) in tooltip.items"
            :key="j"
            class="flex items-center gap-1.5 text-[11px] font-mono font-semibold"
          >
            <span class="w-2 h-2 rounded-full shrink-0" :style="{ background: item.color }" />
            <span class="text-kb-text">{{ item.val }}</span>
          </div>
        </div>
      </Transition>
    </div>
  </div>
</template>
