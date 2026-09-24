<script setup>
import { ref, reactive, computed, watch } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SvgIcon from '@/Components/SvgIcon.vue';
import SvgLineChart from '@/Components/SvgLineChart.vue';
import SvgDonutChart from '@/Components/SvgDonutChart.vue';

const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
  initialCalc: {
    type: String,
    default: 'sip',
  },
});

const calculators = computed(() => props.data.calculators || []);
const activeId = ref(props.initialCalc);
const copied = ref(false);

function copyCalculationSummary() {
  const c = currentCalc.value;
  const res = result.value;
  const summaryText = `${c.title} — KB Finvest Calculation\nResult: ${res.big} (${res.sub})\n${res.rows.map(r => `${r[0]}: ${r[1]}`).join('\n')}\nConsultation: +91 79734 61669 | https://kbfinvest.com`;
  
  if (navigator.clipboard) {
    navigator.clipboard.writeText(summaryText).then(() => {
      copied.value = true;
      setTimeout(() => { copied.value = false; }, 2500);
    });
  }
}

// Category groupings for 14 calculators
const categories = [
  { id: 'all', label: 'All 14 Tools' },
  { id: 'invest', label: 'Investments', ids: ['sip', 'lump', 'stepup', 'swp', 'goal', 'retire'] },
  { id: 'loans', label: 'Loans & Credit', ids: ['emi', 'elig', 'lamf', 'cgtmse'] },
  { id: 'insurance', label: 'Insurance', ids: ['hlv', 'health', 'idv'] },
  { id: 'profile', label: 'Risk Profile', ids: ['risk'] },
];
const activeCategory = ref('all');

const filteredCalculators = computed(() => {
  if (activeCategory.value === 'all') return calculators.value;
  const cat = categories.find((c) => c.id === activeCategory.value);
  if (!cat || !cat.ids) return calculators.value;
  return calculators.value.filter((c) => cat.ids.includes(c.id));
});

// Quick preset chips for common financial queries
const presets = {
  sip: {
    p: [5000, 10000, 25000, 50000],
    y: [5, 10, 15, 20],
  },
  lump: {
    p: [100000, 500000, 1000000, 2500000],
    y: [3, 5, 10, 15],
  },
  emi: {
    p: [1500000, 3000000, 5000000, 10000000],
    y: [10, 15, 20, 25],
  },
  stepup: {
    p: [5000, 10000, 25000],
    s: [5, 10, 15],
  },
};

// State for inputs across all calculators
const formState = reactive({
  // SIP
  sip: { p: 10000, y: 15, r: 11 },
  // Lump sum
  lump: { p: 500000, y: 10, r: 11 },
  // Step-up SIP
  stepup: { p: 10000, s: 10, y: 15, r: 11 },
  // SWP
  swp: { c: 5000000, w: 30000, r: 8, y: 25 },
  // Goal
  goal: { g: 2500000, y: 12, f: 6, r: 11, e: 0 },
  // Retirement
  retire: { a: 35, ra: 60, le: 85, e: 50000, f: 6, r1: 11, r2: 7, s: 500000 },
  // EMI
  emi: { p: 3000000, r: 9, y: 20, x: 0 },
  // Eligibility
  elig: { i: 100000, o: 15000, foir: 55, r: 9, y: 20 },
  // LAMF
  lamf: { sh: 1000000, eq: 2000000, db: 500000 },
  // HLV
  hlv: { inc: 1200000, yrs: 20, deb: 2500000, goal: 2000000, ass: 1000000, cov: 1000000 },
  // Health
  health: { city: 'tier2', adults: 2, kids: 2, age: 42, ped: 'no', cur: 500000 },
  // IDV
  idv: { price: 900000, age: '2', acc: 0, ncb: '2' },
  // CGTMSE
  cgtmse: { amt: 3000000, udyam: true, type: true, nocol: true, lender: true, clean: true, women: false },
  // Risk
  risk: { age: '4', hz: '4', drop: '4', inc: '4', exp: '2' },
});

const currentCalc = computed(() => calculators.value.find((c) => c.id === activeId.value) || calculators.value[0]);

// Math Helpers
function inr(n, dp = 0) {
  if (!isFinite(n)) return '—';
  return '₹' + Number(n).toLocaleString('en-IN', { maximumFractionDigits: dp, minimumFractionDigits: dp });
}

function shortInr(n) {
  n = Math.round(Number(n) || 0);
  const a = Math.abs(n);
  if (a >= 1e7) return '₹' + (n / 1e7).toFixed(2).replace(/\.00$/, '') + ' cr';
  if (a >= 1e5) return '₹' + (n / 1e5).toFixed(2).replace(/\.00$/, '') + ' lakh';
  if (a >= 1e3) return '₹' + (n / 1e3).toFixed(0) + 'k';
  return '₹' + n;
}

function fvSip(p, i, n) {
  return i === 0 ? p * n : p * ((Math.pow(1 + i, n) - 1) / i) * (1 + i);
}

function emiOf(p, i, n) {
  return i === 0 ? p / n : (p * i * Math.pow(1 + i, n)) / (Math.pow(1 + i, n) - 1);
}

function pvAnnuityDue(pmt, rate, n) {
  if (Math.abs(rate) < 1e-9) return pmt * n;
  return (pmt * (1 - Math.pow(1 + rate, -n)) / rate) * (1 + rate);
}

// Reactive Calculation Engine
const result = computed(() => {
  const id = activeId.value;
  const v = formState[id];

  if (id === 'sip') {
    const i = v.r / 1200;
    const n = v.y * 12;
    const fv = fvSip(v.p, i, n);
    const inv = v.p * n;
    return {
      cap: `Estimated value after ${v.y} years`,
      big: shortInr(fv),
      sub: inr(fv),
      rows: [
        ['You invest', inr(inv)],
        ['Estimated wealth gain', inr(fv - inv)],
        ['Monthly instalment', inr(v.p)],
        ['Instalments paid', `${n} months`],
      ],
      chartType: 'line',
      chartTitle: 'Projected SIP Growth (Gold) vs Amount Invested (Blue)',
      xLabels: ['Now', `Yr ${Math.round(v.y / 2)}`, `Yr ${v.y}`],
      series: [
        { cls: 'ln-a', vals: Array.from({ length: 25 }, (_, k) => fvSip(v.p, i, (n * k) / 24)) },
        { cls: 'ln-b', vals: Array.from({ length: 25 }, (_, k) => v.p * ((n * k) / 24)) },
      ],
      wa: `I used the KB Finvest SIP calculator: ${inr(v.p)} a month for ${v.y} years at ${v.r}%. Can we discuss this?`,
    };
  }

  if (id === 'lump') {
    const fv = v.p * Math.pow(1 + v.r / 100, v.y);
    return {
      cap: `Estimated value after ${v.y} years`,
      big: shortInr(fv),
      sub: inr(fv),
      rows: [
        ['You invest', inr(v.p)],
        ['Estimated growth', inr(fv - v.p)],
        ['Multiple of capital', `${(fv / v.p).toFixed(2)}x`],
      ],
      chartType: 'line',
      chartTitle: 'Lump Sum Growth (Gold) vs Principal (Blue)',
      xLabels: ['Now', `Yr ${Math.round(v.y / 2)}`, `Yr ${v.y}`],
      series: [
        { cls: 'ln-a', vals: Array.from({ length: 25 }, (_, k) => v.p * Math.pow(1 + v.r / 100, (v.y * k) / 24)) },
        { cls: 'ln-b', vals: Array.from({ length: 25 }, () => v.p) },
      ],
      wa: `I used the lump sum calculator: ${inr(v.p)} for ${v.y} years at ${v.r}%.`,
    };
  }

  if (id === 'stepup') {
    const i = v.r / 1200;
    let bal = 0;
    let inv = 0;
    let p = v.p;
    const B = [0];
    const I = [0];
    for (let y = 0; y < v.y; y++) {
      for (let m = 0; m < 12; m++) {
        bal = (bal + p) * (1 + i);
        inv += p;
      }
      B.push(bal);
      I.push(inv);
      p *= 1 + v.s / 100;
    }
    const flat = fvSip(v.p, i, v.y * 12);
    return {
      cap: `Estimated value after ${v.y} years`,
      big: shortInr(bal),
      sub: inr(bal),
      rows: [
        ['You invest in total', inr(inv)],
        ['Estimated growth', inr(bal - inv)],
        ['Extra vs a flat SIP', inr(bal - flat)],
      ],
      chartType: 'line',
      chartTitle: 'Step-up SIP Corpus (Gold) vs Total Invested (Blue)',
      xLabels: ['Now', `Yr ${Math.round(v.y / 2)}`, `Yr ${v.y}`],
      series: [
        { cls: 'ln-a', vals: B },
        { cls: 'ln-b', vals: I },
      ],
      wa: `I used the step-up SIP calculator: starting ${inr(v.p)}/mo rising ${v.s}% yearly.`,
    };
  }

  if (id === 'swp') {
    const i = v.r / 1200;
    let bal = v.c;
    const months = v.y * 12;
    let drawn = 0;
    let out = -1;
    const A = [bal];
    for (let k = 0; k < months; k++) {
      bal = bal * (1 + i) - v.w;
      if (bal <= 0 && out === -1) {
        out = k + 1;
        drawn += v.w + bal;
        bal = 0;
      } else if (bal > 0) {
        drawn += v.w;
      }
      if (k % Math.ceil(months / 24) === 0) A.push(Math.max(0, bal));
    }
    const lasts = out === -1 ? `Beyond ${v.y} years` : `${Math.floor(out / 12)} yrs ${out % 12} mo`;
    return {
      cap: 'Your corpus lasts',
      big: lasts,
      sub: out === -1 ? `Still has ${inr(bal)} left at end of ${v.y} years` : `Corpus runs out in month ${out}`,
      rows: [
        ['Corpus at start', inr(v.c)],
        ['Monthly withdrawal', inr(v.w)],
        ['Total withdrawn', inr(drawn)],
        ['Safe monthly draw at this return', inr(v.c * i)],
      ],
      chartType: 'line',
      chartTitle: 'Corpus Balance Trajectory Over Time',
      xLabels: ['Now', `Yr ${Math.round(v.y / 2)}`, `Yr ${v.y}`],
      series: [{ cls: 'ln-a', vals: A }],
      wa: `I used the SWP calculator: corpus ${shortInr(v.c)}, drawing ${inr(v.w)} monthly.`,
    };
  }

  if (id === 'emi') {
    const i = v.r / 1200;
    const n = v.y * 12;
    const emi = emiOf(v.p, i, n);
    const tot = emi * n;
    let bal = v.p;
    let cum = 0;
    const B = [v.p];
    const C = [0];
    for (let k = 0; k < n; k++) {
      const int = bal * i;
      cum += int;
      bal = Math.max(0, bal + int - emi);
      if (k % Math.ceil(n / 24) === 0) {
        B.push(bal);
        C.push(cum);
      }
    }
    return {
      cap: 'Monthly EMI instalment',
      big: inr(emi),
      sub: `Over ${n} instalments at ${v.r}%`,
      rows: [
        ['Principal borrowed', inr(v.p)],
        ['Total interest payable', inr(tot - v.p)],
        ['Total payable (P + I)', inr(tot)],
      ],
      chartType: 'line',
      chartTitle: 'Outstanding Principal (Gold) vs Cumulative Interest Paid (Blue)',
      xLabels: ['Now', `Yr ${Math.round(v.y / 2)}`, `Yr ${v.y}`],
      series: [
        { cls: 'ln-a', vals: B },
        { cls: 'ln-b', vals: C },
      ],
      wa: `I used the EMI calculator: loan ${shortInr(v.p)} at ${v.r}% for ${v.y} years. EMI is ${inr(emi)}.`,
    };
  }

  if (id === 'elig') {
    const cap = (v.i * v.foir) / 100;
    const room = Math.max(0, cap - v.o);
    const i = v.r / 1200;
    const n = v.y * 12;
    const loan = i === 0 ? room * n : room * (Math.pow(1 + i, n) - 1) / (i * Math.pow(1 + i, n));
    return {
      cap: 'Indicative loan amount',
      big: shortInr(loan),
      sub: inr(Math.floor(loan / 1000) * 1000),
      rows: [
        ['Net monthly income', inr(v.i)],
        ['Maximum total EMI allowance', inr(cap)],
        ['Existing commitments', inr(v.o)],
        ['Available EMI capacity', inr(room)],
      ],
      chartType: 'donut',
      mid: `${v.foir}%`,
      sub: 'FOIR Cap',
      donutParts: [
        { l: 'Existing EMIs', v: Math.min(v.o, cap), c: '#64748B', t: inr(Math.min(v.o, cap)) },
        { l: 'Available room', v: room, c: '#D4A537', t: inr(room) },
        { l: 'Living expenses', v: Math.max(0, v.i - cap), c: '#34B07F', t: inr(Math.max(0, v.i - cap)) },
      ],
      wa: `I checked loan eligibility: income ${inr(v.i)}, indicative eligibility ${shortInr(loan)}.`,
    };
  }

  if (id === 'lamf') {
    const a = v.sh * 0.6;
    const b = v.eq * 0.75;
    const c = v.db * 0.85;
    const raw = a + b + c;
    const capped = Math.min(raw, 10000000);
    return {
      cap: 'Indicative credit limit',
      big: shortInr(capped),
      sub: raw > 10000000 ? 'Capped at ₹1 crore per individual across banks' : inr(capped),
      rows: [
        ['Listed shares at 60%', inr(a)],
        ['Equity funds & ETFs at 75%', inr(b)],
        ['Debt mutual funds at 85%', inr(c)],
        ['Portfolio pledged', inr(v.sh + v.eq + v.db)],
      ],
      chartType: 'donut',
      mid: shortInr(capped),
      sub: 'Limit',
      donutParts: [
        { l: 'Shares (60%)', v: a, c: '#3B7BD0', t: inr(a) },
        { l: 'Equity MF (75%)', v: b, c: '#D4A537', t: inr(b) },
        { l: 'Debt MF (85%)', v: c, c: '#34B07F', t: inr(c) },
      ],
      wa: `I checked loan against securities: indicative limit ${shortInr(capped)}.`,
    };
  }

  if (id === 'hlv') {
    const pv = pvAnnuityDue(v.inc, 0.03, v.yrs);
    const need = Math.max(0, pv + v.deb + v.goal - v.ass - v.cov);
    const rounded = Math.ceil(need / 500000) * 500000;
    return {
      cap: 'Suggested additional life cover',
      big: shortInr(rounded),
      sub: inr(need),
      rows: [
        [`Income replacement (${v.yrs} yrs)`, inr(pv)],
        ['Liabilities to clear', inr(v.deb)],
        ['Future goals to fund', inr(v.goal)],
        ['Less assets & cover held', `− ${inr(v.ass + v.cov)}`],
      ],
      chartType: 'donut',
      mid: `${v.yrs}y`,
      sub: 'Support',
      donutParts: [
        { l: 'Income needed', v: pv, c: '#D4A537', t: shortInr(pv) },
        { l: 'Liabilities', v: v.deb, c: '#3B7BD0', t: shortInr(v.deb) },
        { l: 'Future goals', v: v.goal, c: '#34B07F', t: shortInr(v.goal) },
        { l: 'Existing assets', v: v.ass + v.cov, c: '#64748B', t: shortInr(v.ass + v.cov) },
      ],
      wa: `I used the life cover calculator: suggested cover is ${shortInr(rounded)}.`,
    };
  }

  // Fallback / default
  return {
    cap: 'Indicative Assessment',
    big: 'Ready',
    sub: 'Adjust inputs on the left',
    rows: [],
    chartType: 'none',
    wa: `Hello KB Finvest, I used your calculators on the site.`,
  };
});

// Deep-linked URL passing calculator context to Booking
const bookConsultationUrl = computed(() => {
  const c = currentCalc.value;
  const res = result.value;
  if (!c || !res) return '/book';

  const params = new URLSearchParams();
  params.set('topic', c.title);
  params.set('calc', activeId.value);
  if (res.big) params.set('result', res.big);

  const st = formState[activeId.value];
  if (st) {
    if (st.p) params.set('amount', st.p);
    if (st.y) params.set('years', st.y);
    if (st.r) params.set('rate', st.r);
    if (st.amt) params.set('amount', st.amt);
  }
  return `/book?${params.toString()}`;
});

// Milestone projections for compounding tools
const sipMilestones = computed(() => {
  if (activeId.value !== 'sip' && activeId.value !== 'stepup') return null;
  const v = formState[activeId.value];
  const p = Number(v.p) || 10000;
  const r = (Number(v.r) || 12) / 1200;
  const totalY = Number(v.y) || 10;
  
  const years = [3, 5, 10, totalY].filter((y, idx, arr) => arr.indexOf(y) === idx && y > 0 && y <= totalY).sort((a, b) => a - b);
  
  return years.map((y) => {
    const n = y * 12;
    const inv = p * n;
    const fv = r === 0 ? inv : p * ((Math.pow(1 + r, n) - 1) / r) * (1 + r);
    return {
      year: y,
      invested: shortInr(inv),
      projected: shortInr(fv),
    };
  });
});
</script>

<template>
  <AppLayout>
    <Head title="Financial Calculators — SIP, EMI, SWP & CGTMSE in Ludhiana" />

    <!-- Hero Header -->
    <section class="py-12 border-b border-kb-line bg-kb-surface/30">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl space-y-3">
          <div class="inline-block px-3 py-1 rounded-full bg-kb-surface-2 text-xs font-semibold uppercase tracking-wider text-kb-accent border border-kb-border/40">
            Self-Service Tools
          </div>
          <h1 class="text-3xl sm:text-5xl font-bold font-display">
            Run the numbers <span class="italic text-gold-gradient font-serif">yourself</span>
          </h1>
          <p class="text-base text-kb-body leading-relaxed">
            Planning tools that work entirely inside your browser. Nothing you type is transmitted or stored. Every result is illustrative — not a quote, sanction or advice.
          </p>
        </div>

        <!-- Category Selector Pills -->
        <div class="flex items-center gap-2 mt-6 sm:mt-8 overflow-x-auto pb-1 no-scrollbar">
          <button
            v-for="cat in categories"
            :key="cat.id"
            type="button"
            @click="activeCategory = cat.id"
            :class="[
              'px-3.5 py-1.5 rounded-full text-xs font-semibold transition shrink-0 border min-h-[36px]',
              activeCategory === cat.id
                ? 'bg-kb-accent text-black border-kb-accent shadow-sm'
                : 'bg-kb-surface-2/60 text-kb-muted border-kb-line hover:text-kb-text hover:border-kb-border',
            ]"
          >
            {{ cat.label }}
          </button>
        </div>

        <!-- Calculator Pills Selector -->
        <div class="flex items-center gap-2 mt-3 overflow-x-auto pb-2 no-scrollbar">
          <button
            v-for="calc in filteredCalculators"
            :key="calc.id"
            @click="activeId = calc.id"
            :class="[
              'px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shrink-0 border min-h-[40px]',
              activeId === calc.id
                ? 'bg-kb-accent text-black border-kb-accent shadow-md shadow-amber-500/10'
                : 'bg-kb-surface text-kb-body border-kb-line hover:border-kb-border hover:text-kb-text',
            ]"
          >
            <SvgIcon :name="calc.ic" className="w-3.5 h-3.5" />
            <span>{{ calc.label }}</span>
          </button>
        </div>
      </div>
    </section>

    <!-- Active Calculator Workspace -->
    <section class="py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          <!-- Left: Input Controls -->
          <div class="lg:col-span-6 card-luxury p-6 sm:p-8 space-y-6">
            <div>
              <h2 class="text-2xl font-bold text-kb-text font-display flex items-center gap-2.5">
                <SvgIcon :name="currentCalc.ic" className="w-6 h-6 text-kb-accent" />
                <span>{{ currentCalc.title }}</span>
              </h2>
              <p class="text-xs text-kb-muted mt-1 leading-relaxed">{{ currentCalc.desc }}</p>
            </div>

            <!-- Dynamic Input Fields -->
            <div class="space-y-6 pt-2">
              <div
                v-for="f in currentCalc.fields"
                :key="f.k"
                class="space-y-2 pb-4 border-b border-kb-line/40 last:border-0 last:pb-0"
              >
                <!-- Range Slider with Dual Input & Presets -->
                <div v-if="f.t === 'range'" class="space-y-2">
                  <div class="flex items-center justify-between text-xs">
                    <label class="font-medium text-kb-body">{{ f.l }}</label>
                    <span class="font-bold text-kb-accent font-mono text-sm">
                      <template v-if="f.fmt === 'inr'">{{ inr(formState[activeId][f.k]) }}</template>
                      <template v-else-if="f.fmt === 'pct'">{{ formState[activeId][f.k] }}%</template>
                      <template v-else-if="f.fmt === 'yr'">{{ formState[activeId][f.k] }} years</template>
                      <template v-else>{{ formState[activeId][f.k] }}{{ f.unit || '' }}</template>
                    </span>
                  </div>
                  
                  <div class="flex items-center gap-2.5 sm:gap-3">
                    <input
                      type="range"
                      :min="f.min"
                      :max="f.max"
                      :step="f.step"
                      v-model.number="formState[activeId][f.k]"
                      :aria-label="f.l"
                      class="flex-1 accent-amber-400 bg-kb-surface-3 rounded-lg h-2 cursor-pointer min-w-0"
                    />
                    <div class="w-24 sm:w-28 shrink-0">
                      <input
                        type="number"
                        :min="f.min"
                        :max="f.max"
                        :step="f.step"
                        v-model.number="formState[activeId][f.k]"
                        :aria-label="`${f.l} — type exact value`"
                        class="w-full bg-kb-surface-3 border border-kb-line rounded-lg px-2.5 py-1 text-right font-mono text-xs text-kb-text font-semibold no-spin outline-none focus:border-kb-accent focus:bg-kb-surface-2 transition min-h-[36px]"
                        :title="`Type exact value for ${f.l}`"
                      />
                    </div>
                  </div>

                  <div class="flex justify-between text-[10px] text-kb-muted font-mono">
                    <span>{{ f.fmt === 'inr' ? shortInr(f.min) : f.min }}</span>
                    <span>{{ f.fmt === 'inr' ? shortInr(f.max) : f.max }}</span>
                  </div>

                  <!-- Quick Preset Buttons -->
                  <div v-if="presets[activeId]?.[f.k]" class="flex items-center gap-1.5 flex-wrap pt-1">
                    <span class="text-[10px] text-kb-muted uppercase tracking-wider mr-1">Presets:</span>
                    <button
                      v-for="pv in presets[activeId][f.k]"
                      :key="pv"
                      type="button"
                      @click="formState[activeId][f.k] = pv"
                      :class="[
                        'px-2 py-0.5 rounded text-[10px] font-mono font-medium transition',
                        formState[activeId][f.k] === pv
                          ? 'bg-kb-accent/25 text-kb-accent border border-kb-accent/50 font-bold'
                          : 'bg-kb-surface-3 text-kb-muted hover:text-kb-text hover:bg-kb-surface-2 border border-kb-hair',
                      ]"
                    >
                      {{ f.fmt === 'inr' ? shortInr(pv) : (f.fmt === 'yr' ? `${pv}y` : `${pv}%`) }}
                    </button>
                  </div>
                </div>

                <!-- Select Dropdown -->
                <div v-else-if="f.t === 'sel'" class="space-y-1">
                  <label class="text-xs font-medium text-kb-body block">{{ f.l }}</label>
                  <select
                    v-model="formState[activeId][f.k]"
                    class="w-full bg-kb-surface-3 border border-kb-line rounded-lg px-3 py-2 text-xs text-kb-text outline-none focus:border-kb-accent"
                  >
                    <option v-for="opt in f.opts" :key="opt[0]" :value="opt[0]">
                      {{ opt[1] }}
                    </option>
                  </select>
                </div>

                <!-- Checkbox -->
                <div v-else-if="f.t === 'chk'" class="flex items-center gap-3 py-1">
                  <input
                    type="checkbox"
                    :id="`chk-${f.k}`"
                    v-model="formState[activeId][f.k]"
                    class="w-4 h-4 rounded accent-amber-400"
                  />
                  <label :for="`chk-${f.k}`" class="text-xs text-kb-body cursor-pointer">{{ f.l }}</label>
                </div>
              </div>
            </div>

            <!-- Assumptions & Disclaimer -->
            <div class="pt-4 border-t border-kb-line/60 space-y-2 text-[11px] text-kb-muted">
              <div><strong>Assumptions:</strong> {{ currentCalc.assume }}</div>
              <div>{{ currentCalc.disc }}</div>
            </div>
          </div>

          <!-- Right: Real-time Output & Chart -->
          <div class="lg:col-span-6 space-y-6">
            <!-- Big Hero Result Card -->
            <div class="card-luxury p-5 sm:p-8 text-center space-y-3">
              <div class="text-xs font-semibold uppercase tracking-wider text-kb-muted">{{ result.cap }}</div>
              <div class="text-3xl sm:text-4xl lg:text-5xl font-bold font-display text-gold-gradient break-words">{{ result.big }}</div>
              <div class="text-xs text-kb-body font-mono">{{ result.sub }}</div>

              <!-- Output rows breakdown -->
              <div class="pt-6 mt-6 border-t border-kb-line space-y-2.5 text-left text-xs">
                <div
                  v-for="(row, idx) in result.rows"
                  :key="idx"
                  class="flex items-center justify-between py-1.5 border-b border-kb-line/30 last:border-0"
                >
                  <span class="text-kb-muted">{{ row[0] }}</span>
                  <span class="font-bold text-kb-text font-mono">{{ row[1] }}</span>
                </div>
              </div>
            </div>

            <!-- Visual Chart -->
            <SvgLineChart
              v-if="result.chartType === 'line'"
              :series="result.series"
              :xLabels="result.xLabels"
              :title="result.chartTitle"
            />

            <SvgDonutChart
              v-else-if="result.chartType === 'donut'"
              :parts="result.donutParts"
              :mid="result.mid"
              :sub="result.sub"
            />

            <!-- Milestone Projections Table for Compounding -->
            <div v-if="sipMilestones && sipMilestones.length" class="card-frame p-4 bg-kb-surface-2/60 border border-kb-line space-y-3">
              <div class="flex items-center justify-between text-xs font-bold text-kb-text">
                <span class="flex items-center gap-1.5">
                  <SvgIcon name="i-growth" className="w-4 h-4 text-kb-accent" />
                  <span>Year-by-Year Growth Milestones</span>
                </span>
                <span class="text-[10px] text-kb-muted uppercase font-mono">Compounding</span>
              </div>
              <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center">
                <div
                  v-for="ms in sipMilestones"
                  :key="ms.year"
                  class="p-2.5 rounded-xl bg-kb-surface-3/60 border border-kb-line/60"
                >
                  <div class="text-[10px] font-bold text-kb-muted uppercase tracking-wider">Year {{ ms.year }}</div>
                  <div class="text-sm font-bold text-gold-gradient font-mono mt-0.5">{{ ms.projected }}</div>
                  <div class="text-[10px] text-kb-body font-mono mt-0.5">Inv: {{ ms.invested }}</div>
                </div>
              </div>
            </div>

            <!-- Action Buttons Suite -->
            <div class="pt-2 space-y-2.5">
              <a
                :href="`https://wa.me/917973461669?text=${encodeURIComponent(result.wa)}`"
                target="_blank"
                rel="noopener"
                class="w-full py-3.5 px-4 rounded-xl font-bold text-xs uppercase tracking-wider bg-emerald-600 hover:bg-emerald-500 text-white transition flex items-center justify-center gap-2 shadow-lg shadow-emerald-950/40 active:scale-95 btn-shimmer"
              >
                <SvgIcon name="i-wa" className="w-4 h-4" />
                <span>Discuss these figures on WhatsApp</span>
              </a>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <Link
                  :href="bookConsultationUrl"
                  class="py-2.5 px-3 rounded-xl border border-kb-border text-kb-text hover:bg-kb-surface-2 transition flex items-center justify-center gap-1.5 text-xs font-semibold min-h-[44px]"
                >
                  <SvgIcon name="i-cal" className="w-3.5 h-3.5 text-kb-accent shrink-0" />
                  <span>Book Consultation on this Plan</span>
                </Link>

                <button
                  type="button"
                  @click="copyCalculationSummary"
                  class="py-2.5 px-3 rounded-xl border border-kb-line text-kb-body hover:text-kb-text hover:bg-kb-surface-2 transition flex items-center justify-center gap-1.5 text-xs font-semibold min-h-[44px]"
                >
                  <SvgIcon :name="copied ? 'i-check' : 'i-doc'" className="w-3.5 h-3.5 shrink-0" :class="copied ? 'text-emerald-400' : 'text-kb-muted'" />
                  <span>{{ copied ? 'Copied!' : 'Copy Summary' }}</span>
                </button>
              </div>

              <div class="text-center text-[10px] text-kb-muted pt-1">
                AMFI & RBI compliant models · Disclosures shared before any transaction
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
