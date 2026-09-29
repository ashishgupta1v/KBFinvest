<script setup>
import { ref, reactive, computed, watch, watchEffect } from 'vue';
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
const activeId = ref(props.initialCalc === 'prepay' ? 'emi' : props.initialCalc);
const copied = ref(false);

const CALC_TOPIC_MAP = {
  sip: 'SIP & Long-Term Wealth Creation',
  lump: 'Mutual Fund Portfolio Review',
  stepup: 'SIP & Long-Term Wealth Creation',
  swp: 'Retirement & Pension Planning',
  goal: 'Goal-Based Investing (Child Education/Wedding)',
  retire: 'Retirement & Pension Planning',
  emi: 'Home Loan Purchase / Transfer',
  elig: 'Home Loan Purchase / Transfer',
  lamf: 'Loan Against Property (LAP)',
  cgtmse: 'Collateral-Free CGTMSE Loan',
  hlv: 'Term Life Insurance (High Cover, Low Cost)',
  health: 'Comprehensive Health & Super Top-up Insurance',
  idv: 'Motor & Commercial Vehicle Fleet Insurance',
  risk: 'General 360° Financial Checkup',
};

function fallbackCopy(text, cb) {
  const el = document.createElement('textarea');
  el.value = text;
  el.setAttribute('readonly', '');
  el.style.position = 'absolute';
  el.style.left = '-9999px';
  document.body.appendChild(el);
  el.select();
  try {
    document.execCommand('copy');
    cb();
  } catch (err) {
    console.error('Fallback copy failed', err);
  } finally {
    document.body.removeChild(el);
  }
}

function copyCalculationSummary() {
  const c = currentCalc.value;
  const res = result.value;
  if (!c || !res) return;

  const rows = (res.rows || []).map(([lbl, val]) => `• ${lbl}: ${val}`).join('\n');
  const summaryText = `📊 ${c.title} — KB Finvest Calculation\nOutcome: ${res.big} (${res.sub || ''})\n${rows}\n\nCalculated at: https://kbfinvest.com/calculators\nBook Strategy Call: +91 79734 61669`;

  const onCopied = () => {
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2500);
  };

  if (navigator.clipboard && window.isSecureContext) {
    navigator.clipboard.writeText(summaryText)
      .then(onCopied)
      .catch(() => fallbackCopy(summaryText, onCopied));
  } else {
    fallbackCopy(summaryText, onCopied);
  }
}

// ─── Animated Counter for the hero result ───────────────────────────────────
const displayBig = ref('');
const bigGlowing = ref(false);
let _counterRaf = null;
let _counterFrom = 0;
let _counterTo = 0;
let _counterStr = '';

function _parseNumeric(str) {
  if (typeof str !== 'string') return NaN;
  // Extract leading numeric portion before any alpha/symbol suffix
  const m = str.match(/^[\d,\.]+/);
  return m ? parseFloat(m[0].replace(/,/g, '')) : NaN;
}

function animateBig(newBig) {
  const num = _parseNumeric(newBig);
  if (!isFinite(num)) {
    displayBig.value = newBig;
    return;
  }
  const suffix = newBig.replace(/^[\d,\.]+/, '');
  _counterStr = suffix;
  _counterTo = num;
  _counterFrom = _parseNumeric(displayBig.value) || 0;
  if (_counterRaf) cancelAnimationFrame(_counterRaf);
  let startTs = null;
  const duration = 280;
  function step(ts) {
    if (!startTs) startTs = ts;
    const progress = Math.min((ts - startTs) / duration, 1);
    const eased = 1 - Math.pow(1 - progress, 3);
    const cur = _counterFrom + (_counterTo - _counterFrom) * eased;
    displayBig.value = Math.round(cur).toLocaleString('en-IN') + _counterStr;
    if (progress < 1) {
      _counterRaf = requestAnimationFrame(step);
    } else {
      displayBig.value = newBig;
      bigGlowing.value = true;
      setTimeout(() => { bigGlowing.value = false; }, 700);
    }
  }
  _counterRaf = requestAnimationFrame(step);
}

// Watch result.big and trigger counter animation
watchEffect(() => {
  const newBig = result.value?.big;
  if (newBig !== undefined) animateBig(newBig);
});

// ─── Year-by-Year Schedule / Amortization Table Builder ─────────────────────
const scheduleExpanded = ref(false);

const scheduleTable = computed(() => {
  const id = activeId.value;
  const v = formState[id] || {};

  if (id === 'sip' || id === 'stepup') {
    const p = Math.max(0, Number(v.p) || 0);
    const r = Math.max(0, Number(v.r) || 12) / 1200;
    const totalY = Math.max(1, Number(v.y) || 10);
    const s = id === 'stepup' ? (Math.max(0, Number(v.s) || 0)) : 0;
    const rows = [];
    let bal = 0; let inv = 0; let currentP = p;
    for (let yr = 1; yr <= totalY; yr++) {
      const openBal = bal;
      let yearInv = 0;
      for (let m = 0; m < 12; m++) {
        bal = (bal + currentP) * (1 + r);
        inv += currentP;
        yearInv += currentP;
      }
      const growth = bal - openBal - yearInv;
      rows.push({ yr, open: openBal, invested: yearInv, growth, close: bal });
      currentP *= 1 + s / 100;
    }
    return { type: 'growth', rows, headers: ['Yr', 'Opening', 'Invested', 'Growth', 'Closing'] };
  }

  if (id === 'lump') {
    const p = Math.max(0, Number(v.p) || 0);
    const r = Math.max(0, Number(v.r) || 0) / 100;
    const totalY = Math.max(1, Number(v.y) || 10);
    const rows = [];
    let bal = p;
    for (let yr = 1; yr <= totalY; yr++) {
      const openBal = bal;
      const growth = bal * r;
      bal = bal + growth;
      rows.push({ yr, open: openBal, invested: 0, growth, close: bal });
    }
    return { type: 'growth', rows, headers: ['Yr', 'Opening', 'Principal', 'Growth', 'Closing'] };
  }

  if (id === 'emi') {
    const p = Math.max(0, Number(v.p) || 0);
    const r = Math.max(0, Number(v.r) || 0) / 1200;
    const y = Math.max(1, Number(v.y) || 1);
    const x = Math.max(0, Number(v.x) || 0);
    const n = y * 12;
    const emi = r === 0 ? p / n : (p * r * Math.pow(1 + r, n)) / (Math.pow(1 + r, n) - 1);
    const monthlyPay = emi + x;
    let bal = p;
    const rows = [];
    for (let yr = 1; yr <= y; yr++) {
      if (bal <= 0) break;
      const openBal = bal;
      let principal = 0; let interest = 0;
      for (let m = 0; m < 12; m++) {
        if (bal <= 0) break;
        const intMonth = bal * r;
        const prinMonth = Math.min(bal, monthlyPay - intMonth);
        interest += intMonth;
        principal += prinMonth;
        bal = Math.max(0, bal - prinMonth);
      }
      rows.push({ yr, open: openBal, invested: principal, growth: interest, close: bal });
    }
    return { type: 'amort', rows, headers: ['Yr', 'Opening Balance', 'Principal Paid', 'Interest Paid', 'Closing Balance'] };
  }

  if (id === 'swp') {
    const c = Math.max(0, Number(v.c) || 0);
    const w = Math.max(0, Number(v.w) || 0);
    const r = Math.max(0, Number(v.r) || 0) / 1200;
    const y = Math.max(1, Number(v.y) || 1);
    let bal = c;
    const rows = [];
    for (let yr = 1; yr <= y; yr++) {
      if (bal <= 0) break;
      const openBal = bal;
      let withdrawn = 0; let growth = 0;
      for (let m = 0; m < 12; m++) {
        const gr = bal * r;
        growth += gr;
        bal = bal + gr - w;
        withdrawn += w;
        if (bal <= 0) { bal = 0; break; }
      }
      rows.push({ yr, open: openBal, invested: withdrawn, growth, close: Math.max(0, bal) });
    }
    return { type: 'swp', rows, headers: ['Yr', 'Opening Balance', 'Withdrawn', 'Growth Earned', 'Closing Balance'] };
  }

  return null;
});

// ─── Print / PDF Report Modal ─────────────────────────────────────────────────
const showPrintModal = ref(false);
const reportClientName = ref('');
const reportNotes = ref('');

function openPrintModal() {
  showPrintModal.value = true;
}

function closePrintModal() {
  showPrintModal.value = false;
}

function triggerPrint() {
  window.print();
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

watch(activeCategory, (newCat) => {
  if (newCat === 'all') return;
  const cat = categories.find((c) => c.id === newCat);
  if (cat && cat.ids && !cat.ids.includes(activeId.value)) {
    activeId.value = cat.ids[0];
  }
});

watch(activeId, (newId) => {
  if (activeCategory.value !== 'all') {
    const cat = categories.find((c) => c.ids && c.ids.includes(newId));
    if (cat && cat.id !== activeCategory.value) {
      activeCategory.value = cat.id;
    }
  }
  if (typeof window !== 'undefined' && window.history && window.history.replaceState) {
    const url = new URL(window.location.href);
    url.searchParams.set('calc', newId);
    window.history.replaceState({}, '', url.toString());
  }
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
  goal: {
    g: [1000000, 2500000, 5000000, 10000000],
    y: [5, 10, 15, 20],
  },
  swp: {
    c: [2500000, 5000000, 10000000],
    w: [20000, 35000, 50000],
  },
  retire: {
    e: [30000, 50000, 100000],
    ra: [55, 58, 60],
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

if (props.initialCalc === 'prepay') {
  formState.emi.x = 5000;
}

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
  const v = formState[id] || {};

  if (id === 'sip') {
    const p = Math.max(0, Number(v.p) || 0);
    const y = Math.max(1, Number(v.y) || 1);
    const r = Math.max(0, Number(v.r) || 0);
    const i = r / 1200;
    const n = y * 12;
    const fv = fvSip(p, i, n);
    const inv = p * n;
    const returns = Math.max(0, fv - inv);
    const returnPct = inv > 0 ? ((returns / inv) * 100).toFixed(1) : 0;
    return {
      cap: `Estimated Maturity Value After ${y} Years`,
      big: shortInr(fv),
      sub: inr(fv),
      highlightCards: [
        { label: 'Total Invested', val: inr(inv), short: shortInr(inv), sub: `${n} monthly SIPs`, cls: 'text-kb-text', bg: 'bg-kb-surface-3/70' },
        { label: 'Estimated Returns', val: inr(returns), short: shortInr(returns), badge: `+${returnPct}%`, cls: 'text-emerald-400', bg: 'bg-emerald-950/30 border-emerald-500/40' },
        { label: 'Total Maturity Value', val: inr(fv), short: shortInr(fv), sub: `@ ${r}% expected return`, cls: 'text-amber-400', bg: 'bg-amber-950/30 border-amber-500/40' },
      ],
      rows: [
        ['Total Invested Amount', inr(inv)],
        ['Estimated Returns / Profit', inr(returns)],
        ['Total Maturity Value', inr(fv)],
        ['Monthly Instalment', inr(p)],
        ['Assumed Annual Return', `${r}%`],
        ['Instalments Paid', `${n} months (${y} years)`],
      ],
      chartType: 'line',
      chartTitle: 'Projected SIP Growth (Gold) vs Amount Invested (Blue)',
      xLabels: ['Now', `Yr ${Math.round(y / 2)}`, `Yr ${y}`],
      series: [
        { cls: 'ln-a', vals: Array.from({ length: 25 }, (_, k) => fvSip(p, i, (n * k) / 24)) },
        { cls: 'ln-b', vals: Array.from({ length: 25 }, (_, k) => p * ((n * k) / 24)) },
      ],
      wa: `I used the KB Finvest SIP calculator: ${inr(p)} a month for ${y} years at ${r}%. Estimated returns: ${inr(returns)} (Total Value: ${shortInr(fv)}). Can we discuss this?`,
    };
  }

  if (id === 'lump') {
    const p = Math.max(0, Number(v.p) || 0);
    const y = Math.max(1, Number(v.y) || 1);
    const r = Math.max(0, Number(v.r) || 0);
    const fv = p * Math.pow(1 + r / 100, y);
    const returns = Math.max(0, fv - p);
    const returnPct = p > 0 ? ((returns / p) * 100).toFixed(1) : 0;
    return {
      cap: `Estimated Maturity Value After ${y} Years`,
      big: shortInr(fv),
      sub: inr(fv),
      highlightCards: [
        { label: 'Principal Invested', val: inr(p), short: shortInr(p), sub: 'One-time lump sum', cls: 'text-kb-text', bg: 'bg-kb-surface-3/70' },
        { label: 'Estimated Returns', val: inr(returns), short: shortInr(returns), badge: `+${returnPct}%`, cls: 'text-emerald-400', bg: 'bg-emerald-950/30 border-emerald-500/40' },
        { label: 'Total Maturity Value', val: inr(fv), short: shortInr(fv), sub: `${p > 0 ? (fv / p).toFixed(2) : 0}x capital multiplier`, cls: 'text-amber-400', bg: 'bg-amber-950/30 border-amber-500/40' },
      ],
      rows: [
        ['Principal Invested', inr(p)],
        ['Estimated Returns / Profit', inr(returns)],
        ['Total Maturity Value', inr(fv)],
        ['Assumed Annual Return', `${r}%`],
        ['Multiple of Capital', p > 0 ? `${(fv / p).toFixed(2)}x` : '0.00x'],
      ],
      chartType: 'line',
      chartTitle: 'Lump Sum Growth (Gold) vs Principal (Blue)',
      xLabels: ['Now', `Yr ${Math.round(y / 2)}`, `Yr ${y}`],
      series: [
        { cls: 'ln-a', vals: Array.from({ length: 25 }, (_, k) => p * Math.pow(1 + r / 100, (y * k) / 24)) },
        { cls: 'ln-b', vals: Array.from({ length: 25 }, () => p) },
      ],
      wa: `I used the lump sum calculator: ${inr(p)} for ${y} years at ${r}%. Estimated returns: ${inr(returns)}.`,
    };
  }

  if (id === 'stepup') {
    const p = Math.max(0, Number(v.p) || 0);
    const s = Math.max(0, Number(v.s) || 0);
    const y = Math.max(1, Number(v.y) || 1);
    const r = Math.max(0, Number(v.r) || 0);
    const i = r / 1200;
    let bal = 0;
    let inv = 0;
    let currentP = p;
    const B = [0];
    const I = [0];
    for (let yr = 0; yr < y; yr++) {
      for (let m = 0; m < 12; m++) {
        bal = (bal + currentP) * (1 + i);
        inv += currentP;
      }
      B.push(bal);
      I.push(inv);
      currentP *= 1 + s / 100;
    }
    const returns = Math.max(0, bal - inv);
    const returnPct = inv > 0 ? ((returns / inv) * 100).toFixed(1) : 0;
    return {
      cap: `Estimated Maturity Value After ${y} Years (${s}% Step-Up)`,
      big: shortInr(bal),
      sub: inr(bal),
      highlightCards: [
        { label: 'Total Invested', val: inr(inv), short: shortInr(inv), sub: `Stepping up ${s}%/yr`, cls: 'text-kb-text', bg: 'bg-kb-surface-3/70' },
        { label: 'Estimated Returns', val: inr(returns), short: shortInr(returns), badge: `+${returnPct}%`, cls: 'text-emerald-400', bg: 'bg-emerald-950/30 border-emerald-500/40' },
        { label: 'Total Maturity Value', val: inr(bal), short: shortInr(bal), sub: `@ ${r}% expected return`, cls: 'text-amber-400', bg: 'bg-amber-950/30 border-amber-500/40' },
      ],
      rows: [
        ['Total Invested Amount', inr(inv)],
        ['Estimated Returns / Profit', inr(returns)],
        ['Total Maturity Value', inr(bal)],
        ['Starting Monthly SIP', inr(p)],
        ['Annual Step-Up Rate', `${s}%`],
        ['Assumed Annual Return', `${r}%`],
        ['Extra vs Flat SIP', inr(bal - fvSip(p, i, y * 12))],
      ],
      chartType: 'line',
      chartTitle: 'Step-up SIP Corpus (Gold) vs Total Invested (Blue)',
      xLabels: ['Now', `Yr ${Math.round(y / 2)}`, `Yr ${y}`],
      series: [
        { cls: 'ln-a', vals: B },
        { cls: 'ln-b', vals: I },
      ],
      wa: `I used the step-up SIP calculator: starting ${inr(p)}/mo rising ${s}% yearly. Estimated value: ${shortInr(bal)}.`,
    };
  }

  if (id === 'swp') {
    const c = Math.max(0, Number(v.c) || 0);
    const w = Math.max(0, Number(v.w) || 0);
    const r = Math.max(0, Number(v.r) || 0);
    const y = Math.max(1, Number(v.y) || 1);

    const i = r / 1200;
    let bal = c;
    const months = y * 12;
    let drawn = 0;
    let out = -1;
    const A = [bal];
    for (let k = 0; k < months; k++) {
      bal = bal * (1 + i) - w;
      if (bal <= 0 && out === -1) {
        out = k + 1;
        drawn += w + bal;
        bal = 0;
      } else if (bal > 0) {
        drawn += w;
      }
      if (k % Math.max(1, Math.ceil(months / 24)) === 0) A.push(Math.max(0, bal));
    }
    const lasts = out === -1 ? `Beyond ${y} years` : `${Math.floor(out / 12)} yrs ${out % 12} mo`;
    return {
      cap: 'Your corpus lasts',
      big: lasts,
      sub: out === -1 ? `Still has ${inr(bal)} left at end of ${y} years` : `Corpus runs out in month ${out}`,
      rows: [
        ['Corpus at start', inr(c)],
        ['Monthly withdrawal', inr(w)],
        ['Total withdrawn', inr(drawn)],
        ['Safe monthly draw at this return', inr(c * i)],
      ],
      chartType: 'line',
      chartTitle: 'Corpus Balance Trajectory Over Time',
      xLabels: ['Now', `Yr ${Math.round(y / 2)}`, `Yr ${y}`],
      series: [{ cls: 'ln-a', vals: A }],
      wa: `I used the SWP calculator: corpus ${shortInr(c)}, drawing ${inr(w)} monthly.`,
    };
  }

  if (id === 'goal') {
    const g = Math.max(0, Number(v.g) || 0);
    const y = Math.max(1, Number(v.y) || 1);
    const f = Math.max(0, Number(v.f) || 0);
    const r = Math.max(0, Number(v.r) || 0);
    const e = Math.max(0, Number(v.e) || 0);

    const futureCost = g * Math.pow(1 + f / 100, y);
    const existingFv = e * Math.pow(1 + r / 100, y);
    const netGoal = Math.max(0, futureCost - existingFv);

    const n = y * 12;
    const i = (r / 100) / 12;
    const monthlySip = netGoal <= 0 ? 0 : (i === 0 ? netGoal / n : netGoal / (((Math.pow(1 + i, n) - 1) / i) * (1 + i)));

    const inflationAddition = Math.max(0, futureCost - g);
    const savingsCoverage = Math.min(futureCost, existingFv);

    return {
      cap: 'Required Monthly Saving',
      big: shortInr(monthlySip) + (monthlySip > 0 ? '/mo' : ''),
      sub: monthlySip > 0 ? `To accumulate ${shortInr(futureCost)} in ${y} years` : 'Existing savings will exceed target cost!',
      rows: [
        [`Target goal cost in ${y} years`, inr(futureCost)],
        ["Goal cost in today's terms", inr(g)],
        [`Inflation increase (${f}% p.a.)`, `+ ${inr(inflationAddition)}`],
        ['Existing savings grown to target date', inr(existingFv)],
        ['Net shortfall to accumulate', inr(netGoal)],
        ['Suggested monthly SIP', inr(monthlySip)],
      ],
      chartType: 'donut',
      mid: shortInr(futureCost),
      donutSub: 'Goal Cost',
      donutParts: [
        { l: 'Base cost today', v: g, c: '#3B7BD0', t: inr(g) },
        { l: 'Inflation impact', v: inflationAddition, c: '#D4A537', t: inr(inflationAddition) },
        ...(existingFv > 0 ? [{ l: 'Existing savings grown', v: savingsCoverage, c: '#34B07F', t: inr(savingsCoverage) }] : []),
      ],
      wa: `I used the Goal Planner: my ${inr(g)} goal in ${y} years will cost ${shortInr(futureCost)} with ${f}% inflation. Suggested SIP is ${inr(monthlySip)}/mo at ${r}%.`,
    };
  }

  if (id === 'retire') {
    const a = Math.max(18, Number(v.a) || 35);
    const ra = Math.max(a + 1, Number(v.ra) || 60);
    const le = Math.max(ra + 1, Number(v.le) || 85);
    const e = Math.max(0, Number(v.e) || 50000);
    const f = Math.max(0, Number(v.f) || 6);
    const r1 = Math.max(0, Number(v.r1) || 11);
    const r2 = Math.max(0, Number(v.r2) || 7);
    const s = Math.max(0, Number(v.s) || 0);

    const yearsToRetire = ra - a;
    const yearsInRetire = le - ra;

    // Monthly expenses when entering retirement
    const monthlyExpAtRetire = e * Math.pow(1 + f / 100, yearsToRetire);
    const annualExpAtRetire = monthlyExpAtRetire * 12;

    // Real rate of return after retirement
    const realRate = ((1 + r2 / 100) / (1 + f / 100)) - 1;

    // Present value of retirement expenses (Corpus required at age ra)
    let corpusRequired = 0;
    if (Math.abs(realRate) < 0.0001) {
      corpusRequired = annualExpAtRetire * yearsInRetire;
    } else {
      corpusRequired = (annualExpAtRetire * (1 - Math.pow(1 + realRate, -yearsInRetire)) / realRate) * (1 + realRate);
    }
    corpusRequired = Math.max(0, corpusRequired);

    // Existing savings grown to retirement age
    const fvSavings = s * Math.pow(1 + r1 / 100, yearsToRetire);
    const shortfall = Math.max(0, corpusRequired - fvSavings);

    // Monthly SIP needed during accumulation years
    const n = yearsToRetire * 12;
    const i = (r1 / 100) / 12;
    const monthlySip = shortfall <= 0 ? 0 : (i === 0 ? shortfall / n : shortfall / (((Math.pow(1 + i, n) - 1) / i) * (1 + i)));

    return {
      cap: 'Required Retirement Corpus',
      big: shortInr(corpusRequired),
      sub: shortfall > 0 ? `Requires saving ${inr(monthlySip)}/month for ${yearsToRetire} years` : 'Existing savings are sufficient for retirement!',
      rows: [
        ['Monthly expenses today', inr(e)],
        [`Monthly expenses at age ${ra}`, inr(monthlyExpAtRetire)],
        [`Target corpus needed at age ${ra}`, inr(corpusRequired)],
        [`Existing savings grown to age ${ra}`, inr(fvSavings)],
        ['Net corpus shortfall', inr(shortfall)],
        [`Suggested monthly SIP until age ${ra}`, inr(monthlySip)],
      ],
      chartType: 'donut',
      mid: shortInr(corpusRequired),
      donutSub: 'Corpus',
      donutParts: [
        ...(fvSavings > 0 ? [{ l: 'Existing savings grown', v: Math.min(fvSavings, corpusRequired), c: '#34B07F', t: inr(Math.min(fvSavings, corpusRequired)) }] : []),
        { l: 'Corpus shortfall to accumulate', v: shortfall, c: '#D4A537', t: inr(shortfall) },
      ],
      wa: `I used the Retirement Planner: targeting a retirement corpus of ${shortInr(corpusRequired)} at age ${ra} (requires ${inr(monthlySip)}/mo SIP).`,
    };
  }

  if (id === 'emi') {
    const p = Math.max(0, Number(v.p) || 0);
    const r = Math.max(0, Number(v.r) || 0);
    const y = Math.max(1, Number(v.y) || 1);
    const x = Math.max(0, Number(v.x) || 0);

    const i = r / 1200;
    const n = y * 12;
    const emi = emiOf(p, i, n);
    const regularTotal = emi * n;
    const regularInterest = Math.max(0, regularTotal - p);

    let bal = p;
    let cumInt = 0;
    let payoffMonths = n;
    const monthlyPay = emi + x;
    const B = [p];
    const C = [0];

    for (let k = 0; k < n; k++) {
      if (bal <= 0) {
        if (payoffMonths === n) payoffMonths = k;
        bal = 0;
      } else {
        const int = bal * i;
        cumInt += int;
        bal = Math.max(0, bal + int - monthlyPay);
        if (bal === 0 && payoffMonths === n) payoffMonths = k + 1;
      }
      if (k % Math.max(1, Math.ceil(n / 24)) === 0) {
        B.push(bal);
        C.push(cumInt);
      }
    }

    const interestSaved = Math.max(0, regularInterest - cumInt);
    const monthsSaved = Math.max(0, n - payoffMonths);

    const rows = [
      ['Principal borrowed', inr(p)],
      ['Base monthly EMI', inr(emi)],
      ...(x > 0 ? [['Extra monthly prepayment', `+ ${inr(x)}`]] : []),
      ['Total interest payable', inr(cumInt)],
      ['Total loan outflow (P + I)', inr(p + cumInt)],
      ...(x > 0 ? [
        ['Interest saved via prepayments', inr(interestSaved)],
        ['Tenure shortened by', `${Math.floor(monthsSaved / 12)} yrs ${monthsSaved % 12} mo`],
      ] : []),
    ];

    return {
      cap: 'Monthly EMI instalment',
      big: inr(emi),
      sub: x > 0 ? `With ${inr(x)} extra/mo, loan finishes ${monthsSaved} months early` : `Over ${n} instalments at ${r}%`,
      rows,
      chartType: 'line',
      chartTitle: 'Outstanding Principal (Gold) vs Cumulative Interest Paid (Blue)',
      xLabels: ['Now', `Yr ${Math.round(y / 2)}`, `Yr ${y}`],
      series: [
        { cls: 'ln-a', vals: B },
        { cls: 'ln-b', vals: C },
      ],
      wa: `I used the EMI calculator: loan ${shortInr(p)} at ${r}% for ${y} years. Base EMI is ${inr(emi)}${x > 0 ? ` (+ ${inr(x)} extra monthly)` : ''}.`,
    };
  }

  if (id === 'elig') {
    const inc = Math.max(0, Number(v.i) || 0);
    const foir = Math.max(10, Math.min(100, Number(v.foir) || 55));
    const out = Math.max(0, Number(v.o) || 0);
    const r = Math.max(0, Number(v.r) || 0);
    const y = Math.max(1, Number(v.y) || 1);

    const cap = (inc * foir) / 100;
    const room = Math.max(0, cap - out);
    const i = r / 1200;
    const n = y * 12;
    const loan = i === 0 ? room * n : room * (Math.pow(1 + i, n) - 1) / (i * Math.pow(1 + i, n));
    return {
      cap: 'Indicative loan amount',
      big: shortInr(loan),
      sub: inr(Math.floor(loan / 1000) * 1000),
      rows: [
        ['Net monthly income', inr(inc)],
        ['Maximum total EMI allowance', inr(cap)],
        ['Existing commitments', inr(out)],
        ['Available EMI capacity', inr(room)],
      ],
      chartType: 'donut',
      mid: `${foir}%`,
      donutSub: 'FOIR Cap',
      donutParts: [
        { l: 'Existing EMIs', v: Math.min(out, cap), c: '#64748B', t: inr(Math.min(out, cap)) },
        { l: 'Available room', v: room, c: '#D4A537', t: inr(room) },
        { l: 'Living expenses', v: Math.max(0, inc - cap), c: '#34B07F', t: inr(Math.max(0, inc - cap)) },
      ],
      wa: `I checked loan eligibility: income ${inr(inc)}, indicative eligibility ${shortInr(loan)}.`,
    };
  }

  if (id === 'lamf') {
    const sh = Math.max(0, Number(v.sh) || 0);
    const eq = Math.max(0, Number(v.eq) || 0);
    const db = Math.max(0, Number(v.db) || 0);

    const a = sh * 0.6;
    const b = eq * 0.75;
    const c = db * 0.85;
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
        ['Portfolio pledged', inr(sh + eq + db)],
      ],
      chartType: 'donut',
      mid: shortInr(capped),
      donutSub: 'Limit',
      donutParts: [
        { l: 'Shares (60%)', v: a, c: '#3B7BD0', t: inr(a) },
        { l: 'Equity MF (75%)', v: b, c: '#D4A537', t: inr(b) },
        { l: 'Debt MF (85%)', v: c, c: '#34B07F', t: inr(c) },
      ],
      wa: `I checked loan against securities: indicative limit ${shortInr(capped)}.`,
    };
  }

  if (id === 'hlv') {
    const inc = Math.max(0, Number(v.inc) || 0);
    const yrs = Math.max(1, Number(v.yrs) || 1);
    const deb = Math.max(0, Number(v.deb) || 0);
    const goal = Math.max(0, Number(v.goal) || 0);
    const ass = Math.max(0, Number(v.ass) || 0);
    const cov = Math.max(0, Number(v.cov) || 0);

    const pv = pvAnnuityDue(inc, 0.03, yrs);
    const need = Math.max(0, pv + deb + goal - ass - cov);
    const rounded = Math.ceil(need / 500000) * 500000;
    return {
      cap: 'Suggested additional life cover',
      big: shortInr(rounded),
      sub: inr(need),
      rows: [
        [`Income replacement (${yrs} yrs)`, inr(pv)],
        ['Liabilities to clear', inr(deb)],
        ['Future goals to fund', inr(goal)],
        ['Less assets & cover held', `− ${inr(ass + cov)}`],
      ],
      chartType: 'donut',
      mid: `${yrs}y`,
      donutSub: 'Support',
      donutParts: [
        { l: 'Income needed', v: pv, c: '#D4A537', t: shortInr(pv) },
        { l: 'Liabilities', v: deb, c: '#3B7BD0', t: shortInr(deb) },
        { l: 'Future goals', v: goal, c: '#34B07F', t: shortInr(goal) },
        { l: 'Existing assets', v: ass + cov, c: '#64748B', t: shortInr(ass + cov) },
      ],
      wa: `I used the life cover calculator: suggested cover is ${shortInr(rounded)}.`,
    };
  }

  if (id === 'health') {
    const adults = Math.max(1, Number(v.adults) || 2);
    const kids = Math.max(0, Number(v.kids) || 0);
    const age = Math.max(18, Number(v.age) || 40);
    const cur = Math.max(0, Number(v.cur) || 0);

    // City tier base cost (Ludhiana/Chandigarh = tier2)
    let base = v.city === 'metro' ? 1500000 : (v.city === 'tier2' ? 1000000 : 750000);
    base += (adults - 1) * 300000;
    base += kids * 200000;

    // Age loading
    if (age >= 60) {
      base *= 1.5;
    } else if (age >= 50) {
      base *= 1.3;
    } else if (age >= 40) {
      base *= 1.15;
    }

    // Pre-existing condition buffer
    if (v.ped === 'yes') {
      base *= 1.25;
    }

    const recCover = Math.min(5000000, Math.max(1000000, Math.ceil(base / 500000) * 500000));
    const gap = Math.max(0, recCover - cur);

    const strategy = recCover > 1000000 
      ? '₹10 Lakhs Base Policy + ₹25L–50L Super Top-Up' 
      : 'Comprehensive Base Mediclaim with Restoration';

    return {
      cap: 'Recommended Family Health Cover',
      big: shortInr(recCover),
      sub: cur >= recCover ? 'Your current cover meets adequacy guidelines' : `Protection gap: ${inr(gap)} shortfall`,
      rows: [
        ['Current active cover', inr(cur)],
        ['Recommended total sum insured', inr(recCover)],
        ['Coverage shortfall', inr(gap)],
        ['Recommended structure', strategy],
        ['Family composition', `${adults} Adult${adults > 1 ? 's' : ''}${kids > 0 ? ` + ${kids} Child${kids > 1 ? 'ren' : ''}` : ''} (eldest ${age}y)`],
      ],
      chartType: 'donut',
      mid: shortInr(recCover),
      donutSub: 'Health SI',
      donutParts: [
        { l: 'Current cover held', v: Math.min(cur, recCover), c: '#34B07F', t: inr(Math.min(cur, recCover)) },
        ...(gap > 0 ? [{ l: 'Unprotected health gap', v: gap, c: '#E11D48', t: inr(gap) }] : []),
      ],
      wa: `I checked health insurance adequacy: our family needs an estimated ${shortInr(recCover)} cover (current cover: ${inr(cur)}). Can we discuss top-up options?`,
    };
  }

  if (id === 'idv') {
    const price = Math.max(0, Number(v.price) || 0);
    const acc = Math.max(0, Number(v.acc) || 0);

    const depMap = { '0': 5, '0.5': 15, '1': 20, '2': 30, '3': 40, '4': 50, '5': 60 };
    const ncbMap = { '0': 0, '1': 20, '2': 25, '3': 35, '4': 45, '5': 50 };

    const depPct = depMap[String(v.age)] ?? 30;
    const ncbPct = ncbMap[String(v.ncb)] ?? 25;

    const vehicleIdv = Math.round(price * (1 - depPct / 100));
    const accIdv = Math.round(acc * (1 - depPct / 100));
    const totalIdv = vehicleIdv + accIdv;
    const depAmt = price - vehicleIdv;

    return {
      cap: 'Indicative Insured Declared Value (IDV)',
      big: shortInr(totalIdv),
      sub: `Based on standard IRDAI ${depPct}% vehicle depreciation`,
      rows: [
        ['Ex-showroom vehicle price', inr(price)],
        [`IRDAI depreciation (${depPct}%)`, `− ${inr(depAmt)}`],
        ['Accessories IDV added', inr(accIdv)],
        ['Recommended renewal IDV', inr(totalIdv)],
        ['Eligible NCB on Own Damage', `${ncbPct}% discount`],
      ],
      chartType: 'donut',
      mid: `${100 - depPct}%`,
      donutSub: 'Retained',
      donutParts: [
        { l: 'Depreciated vehicle IDV', v: vehicleIdv, c: '#D4A537', t: inr(vehicleIdv) },
        ...(accIdv > 0 ? [{ l: 'Accessories covered', v: accIdv, c: '#34B07F', t: inr(accIdv) }] : []),
        { l: 'Depreciation applied', v: depAmt, c: '#64748B', t: inr(depAmt) },
      ],
      wa: `I checked motor IDV: for ex-showroom ₹${shortInr(price)}, calculated IDV is ${shortInr(totalIdv)} with ${ncbPct}% NCB.`,
    };
  }

  if (id === 'cgtmse') {
    const amt = Math.max(0, Number(v.amt) || 0);
    const udyam = Boolean(v.udyam);
    const type = Boolean(v.type);
    const nocol = Boolean(v.nocol);
    const lender = Boolean(v.lender);
    const clean = Boolean(v.clean);
    const women = Boolean(v.women);

    const conditionsMet = [udyam, type, nocol, lender, clean].filter(Boolean).length;
    const schemeCeiling = 50000000; // ₹5 Crore max under CGTMSE
    const eligibleAmount = Math.min(amt, schemeCeiling);
    
    // Coverage %: Women-led or Micro loans get 85%, standard get 75%
    const guaranteePct = women ? 85 : 75;
    const guaranteeAmount = eligibleAmount * (guaranteePct / 100);
    const uncoveredAmount = eligibleAmount - guaranteeAmount;

    let statusText = 'Fully Eligible';
    let subText = `Guarantee cover up to ${shortInr(guaranteeAmount)} (${guaranteePct}%)`;

    if (amt > schemeCeiling) {
      statusText = 'Cap Applied: ₹5 Cr';
      subText = `Scheme limit is ₹5 Cr. Excess ₹${shortInr(amt - schemeCeiling)} requires alternate structure.`;
    } else if (conditionsMet < 5) {
      statusText = `${conditionsMet}/5 Criteria Met`;
      subText = 'Action required on non-compliant checklist items below.';
    }

    return {
      cap: 'CGTMSE Collateral-Free Eligibility',
      big: statusText,
      sub: subText,
      rows: [
        ['Requested credit facility', inr(amt)],
        ['Scheme guarantee limit', inr(eligibleAmount)],
        [`Trust guarantee coverage (${guaranteePct}%)`, inr(guaranteeAmount)],
        [`Bank retained risk (${100 - guaranteePct}%)`, inr(uncoveredAmount)],
        ['Indicative Annual Guarantee Fee (AGF)', women ? '~0.45%–0.70% (10% women concession)' : '~0.55%–0.85% p.a.'],
        ['Collateral requirement', '₹0 (100% Collateral-Free)'],
      ],
      chartType: 'donut',
      mid: `${guaranteePct}%`,
      donutSub: 'Guarantee',
      donutParts: [
        { l: 'CGTMSE trust guarantee', v: guaranteeAmount, c: '#D4A537', t: inr(guaranteeAmount) },
        { l: 'Bank retained risk', v: uncoveredAmount, c: '#3B7BD0', t: inr(uncoveredAmount) },
      ],
      wa: `I checked CGTMSE eligibility for a ₹${shortInr(amt)} credit facility. Can KB Finvest assist with project report and bank facilitation?`,
    };
  }

  if (id === 'risk') {
    const ageScore = Number(v.age) || 4;
    const hzScore = Number(v.hz) || 4;
    const dropScore = Number(v.drop) || 4;
    const incScore = Number(v.inc) || 4;
    const expScore = Number(v.exp) || 2;

    const totalScore = ageScore + hzScore + dropScore + incScore + expScore;

    let profile = {
      name: 'Moderate / Balanced',
      desc: 'Balanced risk appetite focused on beating inflation with manageable volatility.',
      equity: 50,
      debt: 40,
      gold: 10,
    };

    if (totalScore <= 10) {
      profile = {
        name: 'Conservative',
        desc: 'Focus on capital preservation and predictable regular liquidity.',
        equity: 20,
        debt: 70,
        gold: 10,
      };
    } else if (totalScore <= 16) {
      profile = {
        name: 'Balanced Growth',
        desc: 'Focus on inflation-beating wealth accumulation with moderate stability.',
        equity: 50,
        debt: 40,
        gold: 10,
      };
    } else if (totalScore <= 21) {
      profile = {
        name: 'Growth Focused',
        desc: 'High comfort with market volatility to maximize long-term compounding.',
        equity: 70,
        debt: 20,
        gold: 10,
      };
    } else {
      profile = {
        name: 'Aggressive Growth',
        desc: 'Maximum wealth generation with extended horizon and high risk tolerance.',
        equity: 85,
        debt: 10,
        gold: 5,
      };
    }

    return {
      cap: 'Your Risk Profile',
      big: profile.name,
      sub: `Risk Score: ${totalScore} / 25 · ${profile.desc}`,
      rows: [
        ['Overall risk score', `${totalScore} of 25 points`],
        ['Recommended equity allocation', `${profile.equity}% (Flexicap / Large & Midcap)`],
        ['Recommended debt allocation', `${profile.debt}% (High-grade bonds / Short duration)`],
        ['Recommended gold / hedge', `${profile.gold}% (Sovereign Gold / Multi-Asset)`],
        ['Review & rebalancing', 'Annually or on ±5% asset drift'],
      ],
      chartType: 'donut',
      mid: `${profile.equity}%`,
      donutSub: 'Equity',
      donutParts: [
        { l: 'Equity funds', v: profile.equity, c: '#D4A537', t: `${profile.equity}%` },
        { l: 'Debt & fixed income', v: profile.debt, c: '#3B7BD0', t: `${profile.debt}%` },
        { l: 'Gold & alternates', v: profile.gold, c: '#34B07F', t: `${profile.gold}%` },
      ],
      wa: `I completed the Risk Comfort Check: score is ${totalScore}/25 (${profile.name} profile). Can we discuss an asset allocation strategy?`,
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

  const mappedTopic = CALC_TOPIC_MAP[activeId.value] || c.title;

  const params = new URLSearchParams();
  params.set('topic', mappedTopic);
  params.set('calc', activeId.value);
  if (res.big) params.set('result', res.big);

  const st = formState[activeId.value];
  if (st) {
    if (st.p) params.set('amount', st.p);
    if (st.g) params.set('amount', st.g);
    if (st.c) params.set('amount', st.c);
    if (st.price) params.set('amount', st.price);
    if (st.amt) params.set('amount', st.amt);
    if (st.cur) params.set('amount', st.cur);
    if (st.inc) params.set('amount', st.inc);
    if (st.i) params.set('amount', st.i);
    if (st.y) params.set('years', st.y);
    if (st.yrs) params.set('years', st.yrs);
    if (st.r) params.set('rate', st.r);
    if (st.r1) params.set('rate', st.r1);
  }
  return `/book?${params.toString()}`;
});

// Milestone projections for compounding tools
const sipMilestones = computed(() => {
  if (activeId.value !== 'sip' && activeId.value !== 'stepup') return null;
  const v = formState[activeId.value];
  if (!v) return null;
  const p = Number(v.p) || 10000;
  const r = (Number(v.r) || 12) / 1200;
  const totalY = Number(v.y) || 10;
  const s = activeId.value === 'stepup' ? (Number(v.s) || 0) : 0;
  
  const years = [3, 5, 10, totalY].filter((y, idx, arr) => arr.indexOf(y) === idx && y > 0 && y <= totalY).sort((a, b) => a - b);
  
  return years.map((y) => {
    let bal = 0;
    let inv = 0;
    let currentP = p;
    for (let yr = 0; yr < y; yr++) {
      for (let m = 0; m < 12; m++) {
        bal = (bal + currentP) * (1 + r);
        inv += currentP;
      }
      currentP *= 1 + s / 100;
    }
    return {
      year: y,
      invested: shortInr(inv),
      projected: shortInr(bal),
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
              <!-- Animated counter with gold glow -->
              <div
                class="text-3xl sm:text-4xl lg:text-5xl font-bold font-display text-gold-gradient break-words transition-all duration-300"
                :class="{ 'drop-shadow-[0_0_18px_rgba(212,165,55,0.7)]': bigGlowing }"
              >{{ displayBig || result.big }}</div>
              <div class="text-xs text-kb-body font-mono">{{ result.sub }}</div>

              <!-- 3-Box Highlight Cards (Total Invested, Estimated Returns, Total Maturity Value) -->
              <div v-if="result.highlightCards && result.highlightCards.length" class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-3 text-left">
                <div
                  v-for="(card, cIdx) in result.highlightCards"
                  :key="cIdx"
                  :class="['p-3 rounded-xl border border-kb-line/70 flex flex-col justify-between transition-all shadow-sm', card.bg || 'bg-kb-surface-3/60']"
                >
                  <div class="flex items-center justify-between gap-1">
                    <span class="text-[10px] uppercase font-bold text-kb-muted tracking-wider">{{ card.label }}</span>
                    <span v-if="card.badge" class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 font-mono">{{ card.badge }}</span>
                  </div>
                  <div :class="['text-base sm:text-lg font-bold font-mono mt-1', card.cls]">{{ card.short }}</div>
                  <div class="text-[10px] text-kb-muted font-mono truncate">{{ card.val }}</div>
                  <div v-if="card.sub" class="text-[9px] text-kb-body/70 mt-0.5">{{ card.sub }}</div>
                </div>
              </div>

              <!-- Output rows breakdown -->
              <div class="pt-5 mt-5 border-t border-kb-line space-y-2.5 text-left text-xs">
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
              :sub="result.donutSub || result.sub"
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

            <!-- Year-by-Year Schedule / Amortization Accordion -->
            <div v-if="scheduleTable" class="card-frame border border-kb-line rounded-xl overflow-hidden">
              <button
                type="button"
                @click="scheduleExpanded = !scheduleExpanded"
                class="w-full flex items-center justify-between px-4 py-3 text-xs font-bold text-kb-text hover:bg-kb-surface-2/60 transition cursor-pointer"
              >
                <span class="flex items-center gap-2">
                  <SvgIcon name="i-growth" className="w-4 h-4 text-kb-accent" />
                  <span>{{ scheduleTable.type === 'amort' ? 'Year-by-Year Amortization Schedule' : scheduleTable.type === 'swp' ? 'Annual Drawdown Schedule' : 'Annual Growth Schedule' }}</span>
                </span>
                <span class="text-kb-muted flex items-center gap-1 font-normal text-[10px]">
                  {{ scheduleExpanded ? 'Collapse' : 'View Full Breakdown' }}
                  <svg :class="['w-3 h-3 transition-transform duration-200', scheduleExpanded ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </span>
              </button>
              <Transition
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="opacity-0 max-h-0"
                enter-to-class="opacity-100 max-h-[480px]"
                leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="opacity-100 max-h-[480px]"
                leave-to-class="opacity-0 max-h-0"
              >
                <div v-if="scheduleExpanded" class="overflow-auto max-h-[480px]">
                  <table class="w-full text-[10px] font-mono border-collapse">
                    <thead class="sticky top-0 z-10">
                      <tr class="bg-kb-surface-3 text-kb-muted">
                        <th v-for="h in scheduleTable.headers" :key="h" class="px-3 py-2 text-left font-semibold uppercase tracking-wider border-b border-kb-line">{{ h }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="row in scheduleTable.rows"
                        :key="row.yr"
                        class="border-b border-kb-line/30 hover:bg-kb-surface-2/40 transition"
                      >
                        <td class="px-3 py-2 text-kb-accent font-bold">{{ row.yr }}</td>
                        <td class="px-3 py-2 text-kb-body">{{ shortInr(row.open) }}</td>
                        <td class="px-3 py-2 text-kb-primary">{{ shortInr(row.invested) }}</td>
                        <td class="px-3 py-2" :class="scheduleTable.type === 'amort' ? 'text-red-400' : 'text-emerald-400'">{{ shortInr(row.growth) }}</td>
                        <td class="px-3 py-2 font-bold text-kb-text">{{ shortInr(row.close) }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </Transition>
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
                  class="py-2.5 px-3 rounded-xl border border-amber-400/40 bg-amber-500/10 hover:bg-amber-500/20 text-amber-200 transition flex items-center justify-center gap-1.5 text-xs font-semibold min-h-[44px] shadow-sm active:scale-95"
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

                <!-- Print / PDF Advisory Report -->
                <button
                  type="button"
                  @click="openPrintModal"
                  class="py-2.5 px-3 rounded-xl border border-kb-border/50 text-kb-accent hover:bg-kb-accent/10 hover:border-kb-accent/60 transition flex items-center justify-center gap-1.5 text-xs font-semibold min-h-[44px]"
                >
                  <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                  <span>Print / Save PDF</span>
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

    <!-- ── Print / PDF Report Modal ──────────────────────────────────────── -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="showPrintModal"
          class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4"
          @click.self="closePrintModal"
        >
          <!-- Backdrop -->
          <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="closePrintModal" />

          <!-- Modal Card -->
          <div class="relative z-10 w-full max-w-lg card-luxury p-6 sm:p-8 space-y-5">
            <!-- Header -->
            <div class="flex items-start justify-between">
              <div>
                <div class="text-xs font-semibold uppercase tracking-wider text-kb-accent mb-1">Advisory Report</div>
                <h3 class="text-lg font-bold font-display text-kb-text">Print / Save as PDF</h3>
                <p class="text-xs text-kb-muted mt-0.5">Optionally personalise the report before printing.</p>
              </div>
              <button type="button" @click="closePrintModal" class="text-kb-muted hover:text-kb-text transition p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
              </button>
            </div>

            <!-- Preview Card (printed letterhead) -->
            <div id="kb-print-report" class="rounded-xl border border-kb-border/50 bg-kb-surface-2/40 p-4 space-y-3">
              <!-- Letterhead -->
              <div class="flex items-center justify-between border-b border-kb-line pb-3">
                <div>
                  <div class="text-base font-bold font-display text-gold-gradient">KB Finvest Advisory</div>
                  <div class="text-[10px] text-kb-muted font-mono">AMFI-Registered · IRDAI-Licensed · CGTMSE Facilitation</div>
                </div>
                <div class="text-right text-[10px] text-kb-muted font-mono">
                  <div>{{ new Date().toLocaleDateString('en-IN', { day:'2-digit', month:'short', year:'numeric' }) }}</div>
                  <div class="text-kb-accent">kbfinvest.com</div>
                </div>
              </div>

              <!-- Calculator name & outcome -->
              <div class="text-center py-2">
                <div class="text-[10px] uppercase tracking-widest text-kb-muted mb-1">{{ result.cap }}</div>
                <div class="text-2xl font-bold font-display text-gold-gradient">{{ result.big }}</div>
                <div class="text-[11px] text-kb-body font-mono mt-0.5">{{ result.sub }}</div>
              </div>

              <!-- Breakdown rows -->
              <div class="space-y-1 text-[11px]">
                <div v-for="(row, idx) in result.rows" :key="idx" class="flex justify-between py-1 border-b border-kb-line/20">
                  <span class="text-kb-muted">{{ row[0] }}</span>
                  <span class="font-mono font-bold text-kb-text">{{ row[1] }}</span>
                </div>
              </div>

              <!-- Optional client name -->
              <div v-if="reportClientName" class="pt-2 border-t border-kb-line/30 text-[10px] text-kb-body">
                Prepared for: <strong class="text-kb-text">{{ reportClientName }}</strong>
              </div>
              <div v-if="reportNotes" class="text-[10px] text-kb-muted italic">{{ reportNotes }}</div>

              <!-- Footer disclaimer -->
              <div class="text-[9px] text-kb-muted pt-2 border-t border-kb-line/30 leading-relaxed">
                This is an illustrative estimate only — not a quote, sanction, or financial advice. All figures are subject to market conditions and regulatory norms. Contact KB Finvest before making financial decisions.
              </div>
            </div>

            <!-- Optional personalization fields -->
            <div class="space-y-3">
              <div>
                <label class="text-xs font-medium text-kb-body block mb-1" for="report-client">Client Name <span class="text-kb-muted">(optional)</span></label>
                <input
                  id="report-client"
                  v-model="reportClientName"
                  type="text"
                  placeholder="e.g., Mr. Rajesh Sharma"
                  class="w-full bg-kb-surface-3 border border-kb-line rounded-lg px-3 py-2 text-xs text-kb-text placeholder:text-kb-muted outline-none focus:border-kb-accent transition"
                />
              </div>
              <div>
                <label class="text-xs font-medium text-kb-body block mb-1" for="report-notes">Advisor Notes <span class="text-kb-muted">(optional)</span></label>
                <textarea
                  id="report-notes"
                  v-model="reportNotes"
                  rows="2"
                  placeholder="Add context, next steps, or remarks..."
                  class="w-full bg-kb-surface-3 border border-kb-line rounded-lg px-3 py-2 text-xs text-kb-text placeholder:text-kb-muted outline-none focus:border-kb-accent transition resize-none"
                />
              </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-2 pt-1">
              <button
                type="button"
                @click="closePrintModal"
                class="flex-1 py-2.5 rounded-xl border border-kb-line text-kb-body hover:bg-kb-surface-2 transition text-xs font-semibold"
              >
                Cancel
              </button>
              <button
                type="button"
                @click="triggerPrint"
                class="flex-1 py-2.5 rounded-xl bg-kb-accent text-black hover:bg-kb-accent-hi transition text-xs font-bold flex items-center justify-center gap-2 shadow-lg shadow-amber-900/20"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                Print / Save PDF
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </AppLayout>
</template>
