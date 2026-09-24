<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SvgIcon from '@/Components/SvgIcon.vue';

import SolutionFinderWizard from '@/Components/SolutionFinderWizard.vue';

const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
});

const proof = computed(() => props.data.proof || []);
const figures = computed(() => props.data.figures || []);
const pillars = computed(() => props.data.pillars || []);
const flow = computed(() => props.data.flow || []);
const safety = computed(() => props.data.safety || []);
const flowNote = computed(() => props.data.flowNote || {});

// Partner Institutions Ecosystem
const partnerInstitutions = [
  { id: 'hdfc', name: 'HDFC Mutual Fund', type: 'Asset Management', badge: 'AMC Partner' },
  { id: 'icici', name: 'ICICI Prudential', type: 'Mutual Fund & Life', badge: 'AMC Partner' },
  { id: 'sbi', name: 'SBI Mutual Fund', type: 'Asset Management', badge: 'AMC Partner' },
  { id: 'nippon', name: 'Nippon India MF', type: 'Asset Management', badge: 'AMC Partner' },
  { id: 'bajaj', name: 'Bajaj Finance', type: 'Lending & Fixed Dep', badge: 'Lender' },
  { id: 'tata', name: 'Tata Capital', type: 'Commercial & MSME', badge: 'Lender' },
  { id: 'axis', name: 'Axis Bank', type: 'Banking & CGTMSE', badge: 'Bank Facilitation' },
  { id: 'kotak', name: 'Kotak Mahindra', type: 'Funds & Insurance', badge: 'Partner' },
];

// Interactive Comparison Matrix Data
const activeComparisonTab = ref('fiduciary');

const comparisonFeatures = [
  {
    id: 'fiduciary',
    label: 'Advisory Incentive',
    kb: 'Long-term goal alignment & relationship. We earn disclosed AMFI distributor commissions only while you stay invested.',
    bank: 'Aggressive quarterly sales quotas. High RM turnover means your account manager changes every 12–18 months.',
    robo: 'Automated algorithms with zero human accountability when markets dip or during stressful life events.',
  },
  {
    id: 'presence',
    label: 'Physical Presence',
    kb: 'Walk-in office at 179-A, Central Town, Ludhiana. Sit down with Kulwinder Singh directly for coffee and a portfolio review.',
    bank: 'Long branch queues with shifting staff who push high-margin in-house ULIPs and insurance products.',
    robo: 'Support chatbots and automated ticket queues with no local human to speak with.',
  },
  {
    id: 'claims',
    label: 'Claims & Crisis Support',
    kb: 'Personal in-hospital and insurance claim advocacy. Confidential resolution roadmap for stressed business loans/NPA.',
    bank: 'Branches direct you to a toll-free 1800 customer care queue. Zero local claim paperwork assistance.',
    robo: 'Zero claim assistance. You are left on your own dealing with insurance third-party administrators (TPAs).',
  },
  {
    id: 'transparency',
    label: 'Regulatory Compliance',
    kb: 'Strict SEBI, AMFI & IRDAI compliance. 100% transparent commission disclosures with zero hidden wrap fees.',
    bank: 'Heavily front-loaded commissions and cross-selling quotas prioritized over portfolio risk tolerance.',
    robo: 'Monetized payment-for-order-flows or monthly account maintenance charges.',
  },
];

// Realistic Client Scenario Vignettes
const clientVignettes = [
  {
    tag: 'MSME Growth · Focal Point, Ludhiana',
    title: '₹1.8 Cr CGTMSE Facility for Auto-Parts Exporter',
    desc: 'An auto-component manufacturer needed expansion capital for CNC machinery. The lender initially insisted on factory collateral. We structured their project report and audited financials under the revised CGTMSE framework, securing a 100% collateral-free sanction at competitive interest rates.',
    metric: '₹1.8 Cr Collateral-Free',
    ic: 'i-tax',
    badge: 'Manufacturing',
  },
  {
    tag: 'Family Wealth · Civil Lines, Ludhiana',
    title: '15-Year Step-Up SIP for Overseas Education',
    desc: 'Starting with a ₹15,000 monthly allocation stepped up by 10% annually, a local family built a disciplined multi-cap portfolio that protected against rupee depreciation without locking funds into illiquid endowment policies.',
    metric: 'Disciplined Compounding',
    ic: 'i-growth',
    badge: 'Education Fund',
  },
  {
    tag: 'Retirement Security · Model Town, Ludhiana',
    title: 'Tax-Efficient SWP Pension of ₹45,000/Month',
    desc: 'A retiring industrialist consolidated fragmented fixed deposits into equity-savings and conservative hybrid funds, generating predictable monthly cash flow with a fraction of the tax liability of standard bank FD interest.',
    metric: 'Tax-Smart Monthly Income',
    ic: 'i-coin',
    badge: 'Retirement SWP',
  },
];

// Interactive Hero Teaser Calculator State
const heroCalcTab = ref('sip'); // 'sip' | 'emi'
const heroSipMonthly = ref(15000);
const heroSipYears = ref(10);
const heroSipRate = ref(12);

const heroEmiAmount = ref(3500000);
const heroEmiYears = ref(15);
const heroEmiRate = ref(8.75);

const heroSipResults = computed(() => {
  const p = Number(heroSipMonthly.value) || 0;
  const y = Number(heroSipYears.value) || 1;
  const r = (Number(heroSipRate.value) / 100) / 12;
  const n = y * 12;
  const invested = p * n;
  const fv = r > 0 ? p * ((Math.pow(1 + r, n) - 1) / r) * (1 + r) : invested;
  const gain = Math.max(0, fv - invested);
  return {
    invested: Math.round(invested),
    fv: Math.round(fv),
    gain: Math.round(gain),
  };
});

const heroEmiResults = computed(() => {
  const p = Number(heroEmiAmount.value) || 0;
  const y = Number(heroEmiYears.value) || 1;
  const r = (Number(heroEmiRate.value) / 100) / 12;
  const n = y * 12;
  const emi = (p * r * Math.pow(1 + r, n)) / (Math.pow(1 + r, n) - 1);
  const total = emi * n;
  const interest = total - p;
  return {
    emi: Math.round(emi),
    total: Math.round(total),
    interest: Math.round(interest),
  };
});

function fmtInr(n) {
  if (!isFinite(n) || n <= 0) return '₹0';
  if (n >= 10000000) return '₹' + (n / 10000000).toFixed(2) + ' Cr';
  if (n >= 100000) return '₹' + (n / 100000).toFixed(2) + ' Lakh';
  return '₹' + Math.round(n).toLocaleString('en-IN');
}
</script>

<template>
  <AppLayout>
    <Head title="Invest Smart. Grow Secure. — Mutual Funds, Insurance & Loans in Ludhiana" />

    <!-- HERO SECTION -->
    <section class="relative overflow-hidden pt-12 pb-20 lg:pt-24 lg:pb-32 border-b border-kb-line">
      <!-- Background elements & ambient glows -->
      <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[900px] h-[550px] bg-gradient-to-b from-blue-600/15 via-amber-500/8 to-transparent blur-3xl rounded-full"></div>
        <div class="absolute -bottom-20 right-10 w-96 h-96 bg-amber-500/5 blur-3xl rounded-full"></div>
        <div class="absolute right-4 top-10 opacity-10 text-kb-accent w-96 h-96 pointer-events-none">
          <SvgIcon name="i-guilloche" className="w-full h-full" />
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
          <!-- Left Column -->
          <div class="lg:col-span-7 space-y-6">
            <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-kb-surface-2/90 border border-kb-border/60 text-xs font-semibold text-kb-accent shadow-sm backdrop-blur-md">
              <div class="relative w-6 h-6 rounded-full overflow-hidden border border-amber-400/60 shadow-sm shrink-0">
                <img
                  src="/images/kulwinder-avatar.png"
                  alt="Kulwinder Singh"
                  class="w-full h-full object-cover"
                />
              </div>
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
              <span class="text-kb-text font-bold">Kulwinder Singh</span>
              <span class="text-kb-muted font-normal hidden sm:inline">· Central Town, Ludhiana</span>
              <span class="text-kb-muted font-normal">| Mutual Funds · Insurance · Loans</span>
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold font-display tracking-tight leading-[1.12]">
              Smart Investments.<br />
              <span class="text-gold-gradient font-serif italic">Secure Future.</span>
            </h1>

            <p class="text-lg sm:text-xl text-kb-body leading-relaxed max-w-2xl font-light">
              One trusted relationship for the money decisions that matter — where to invest, what to protect, and how to fund growth. Explained in plain language by <strong>Kulwinder Singh</strong>, from an office you can walk into on Central Town, Ludhiana.
            </p>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4 pt-2">
              <Link
                href="/book"
                class="w-full sm:w-auto min-h-[44px] justify-center px-6 py-3.5 rounded-xl font-bold text-sm bg-gold-gradient text-black hover:brightness-105 transition shadow-lg shadow-amber-500/15 flex items-center gap-2 active:scale-95 btn-shimmer"
              >
                <SvgIcon name="i-cal" className="w-4 h-4" />
                <span>Book a free consultation</span>
              </Link>
              <a
                href="https://wa.me/917973461669?text=Hello%20KB%20Finvest,%20I%20would%20like%20to%20consult%20you."
                target="_blank"
                rel="noopener"
                class="w-full sm:w-auto min-h-[44px] justify-center px-6 py-3.5 rounded-xl font-semibold text-sm border border-emerald-500/40 text-emerald-400 bg-emerald-950/30 hover:bg-emerald-950/50 transition flex items-center gap-2 active:scale-95"
              >
                <SvgIcon name="i-wa" className="w-4 h-4" />
                <span>WhatsApp us</span>
              </a>
            </div>

            <!-- Trust points -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-6 border-t border-kb-line/60 text-xs text-kb-body">
              <div class="flex items-center gap-2.5 p-2 rounded-lg bg-kb-surface/40 border border-kb-hair">
                <SvgIcon name="i-lock" className="w-4 h-4 text-kb-accent shrink-0" />
                <span>No cold calls, ever</span>
              </div>
              <div class="flex items-center gap-2.5 p-2 rounded-lg bg-kb-surface/40 border border-kb-hair">
                <SvgIcon name="i-doc" className="w-4 h-4 text-kb-accent shrink-0" />
                <span>Everything in writing</span>
              </div>
              <div class="flex items-center gap-2.5 p-2 rounded-lg bg-kb-surface/40 border border-kb-hair">
                <SvgIcon name="i-refresh" className="w-4 h-4 text-kb-accent shrink-0" />
                <span>Present at claim time</span>
              </div>
            </div>
          </div>

          <!-- Right Column: Interactive Wealth Teaser Widget & Advisor Credentials -->
          <div class="lg:col-span-5 space-y-4">
            <!-- Interactive Live Teaser Widget -->
            <div class="card-luxury p-5 sm:p-6 relative border border-kb-border/60 shadow-2xl">
              <!-- Top Header & Tabs -->
              <div class="flex items-center justify-between pb-3 border-b border-kb-line">
                <div class="flex items-center gap-1.5 p-1 rounded-xl bg-kb-surface-2 border border-kb-line/60">
                  <button
                    type="button"
                    @click="heroCalcTab = 'sip'"
                    :class="[
                      'px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5',
                      heroCalcTab === 'sip'
                        ? 'bg-kb-surface text-kb-accent shadow-sm border border-kb-border/50'
                        : 'text-kb-muted hover:text-kb-text',
                    ]"
                  >
                    <SvgIcon name="i-growth" className="w-3.5 h-3.5" />
                    <span>SIP Growth</span>
                  </button>
                  <button
                    type="button"
                    @click="heroCalcTab = 'emi'"
                    :class="[
                      'px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5',
                      heroCalcTab === 'emi'
                        ? 'bg-kb-surface text-kb-accent shadow-sm border border-kb-border/50'
                        : 'text-kb-muted hover:text-kb-text',
                    ]"
                  >
                    <SvgIcon name="i-home" className="w-3.5 h-3.5" />
                    <span>Loan EMI</span>
                  </button>
                </div>
                <span class="text-[11px] font-mono text-kb-muted uppercase tracking-wider hidden sm:inline">Live Preview</span>
              </div>

              <!-- Tab 1: SIP Calculator -->
              <div v-if="heroCalcTab === 'sip'" class="pt-4 space-y-4">
                <!-- Slider 1: Monthly Investment -->
                <div class="space-y-1.5">
                  <div class="flex items-center justify-between text-xs">
                    <span class="text-kb-muted">Monthly Investment</span>
                    <span class="font-bold text-kb-text font-mono text-sm">{{ fmtInr(heroSipMonthly) }}<span class="text-[10px] text-kb-muted font-normal">/mo</span></span>
                  </div>
                  <input
                    type="range"
                    v-model.number="heroSipMonthly"
                    min="2000"
                    max="100000"
                    step="1000"
                    aria-label="Monthly Investment Amount"
                    class="w-full"
                  />
                  <div class="flex justify-between text-[10px] text-kb-muted">
                    <span>₹2,000</span>
                    <span>₹50,000</span>
                    <span>₹1,00,000</span>
                  </div>
                </div>

                <!-- Slider 2: Horizon -->
                <div class="space-y-1.5">
                  <div class="flex items-center justify-between text-xs">
                    <span class="text-kb-muted">Investment Horizon</span>
                    <span class="font-bold text-kb-text font-mono text-sm">{{ heroSipYears }} Years</span>
                  </div>
                  <input
                    type="range"
                    v-model.number="heroSipYears"
                    min="3"
                    max="25"
                    step="1"
                    aria-label="Investment Horizon in Years"
                    class="w-full"
                  />
                  <div class="flex justify-between text-[10px] text-kb-muted">
                    <span>3 Years</span>
                    <span>10 Years</span>
                    <span>25 Years</span>
                  </div>
                </div>

                <!-- Live Results Box -->
                <div class="p-3.5 rounded-xl bg-kb-surface-2/90 border border-kb-border/40 grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3 items-center">
                  <div>
                    <div class="text-[10px] font-semibold text-kb-muted uppercase tracking-wider">Projected Wealth</div>
                    <div class="text-xl sm:text-2xl font-black text-gold-gradient font-mono tracking-tight mt-0.5">
                      {{ fmtInr(heroSipResults.fv) }}
                    </div>
                  </div>
                  <div class="text-left sm:text-right space-y-0.5 border-t sm:border-t-0 sm:border-l border-kb-line/60 pt-2 sm:pt-0 sm:pl-3">
                    <div class="text-[11px] text-kb-body flex justify-between">
                      <span class="text-kb-muted">Invested:</span>
                      <span class="font-medium font-mono">{{ fmtInr(heroSipResults.invested) }}</span>
                    </div>
                    <div class="text-[11px] text-emerald-400 font-semibold flex justify-between">
                      <span>Gain:</span>
                      <span class="font-mono">+{{ fmtInr(heroSipResults.gain) }}</span>
                    </div>
                  </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-2 text-xs pt-1">
                  <Link
                    :href="`/calculators?calc=sip`"
                    class="font-bold text-kb-accent hover:underline flex items-center gap-1 text-[11px]"
                  >
                    <span>Full SIP & Step-Up Suite</span>
                    <SvgIcon name="i-arr" className="w-3 h-3" />
                  </Link>
                  <a
                    :href="`https://wa.me/917973461669?text=Hello%20Kulwinder,%20I%20am%20planning%20an%20SIP%20of%20${fmtInr(heroSipMonthly)}/month%20for%20${heroSipYears}%20years.%20Can%20we%20discuss%20portfolio%20recommendations?`"
                    target="_blank"
                    class="text-emerald-400 hover:text-emerald-300 font-semibold flex items-center gap-1 text-[11px]"
                  >
                    <SvgIcon name="i-wa" className="w-3.5 h-3.5" />
                    <span>Discuss on WhatsApp</span>
                  </a>
                </div>
              </div>

              <!-- Tab 2: Loan EMI Calculator -->
              <div v-else class="pt-4 space-y-4">
                <!-- Slider 1: Loan Amount -->
                <div class="space-y-1.5">
                  <div class="flex items-center justify-between text-xs">
                    <span class="text-kb-muted">Loan Amount</span>
                    <span class="font-bold text-kb-text font-mono text-sm">{{ fmtInr(heroEmiAmount) }}</span>
                  </div>
                  <input
                    type="range"
                    v-model.number="heroEmiAmount"
                    min="500000"
                    max="20000000"
                    step="250000"
                    aria-label="Loan Amount"
                    class="w-full"
                  />
                  <div class="flex justify-between text-[10px] text-kb-muted">
                    <span>₹5 Lakh</span>
                    <span>₹1 Crore</span>
                    <span>₹2 Crore</span>
                  </div>
                </div>

                <!-- Slider 2: Tenure -->
                <div class="space-y-1.5">
                  <div class="flex items-center justify-between text-xs">
                    <span class="text-kb-muted">Tenure (Years)</span>
                    <span class="font-bold text-kb-text font-mono text-sm">{{ heroEmiYears }} Years</span>
                  </div>
                  <input
                    type="range"
                    v-model.number="heroEmiYears"
                    min="5"
                    max="25"
                    step="1"
                    aria-label="Loan Tenure in Years"
                    class="w-full"
                  />
                  <div class="flex justify-between text-[10px] text-kb-muted">
                    <span>5 Yrs</span>
                    <span>15 Yrs</span>
                    <span>25 Yrs</span>
                  </div>
                </div>

                <!-- Results Box -->
                <div class="p-3.5 rounded-xl bg-kb-surface-2/90 border border-kb-border/40 grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3 items-center">
                  <div>
                    <div class="text-[10px] font-semibold text-kb-muted uppercase tracking-wider">Estimated Monthly EMI</div>
                    <div class="text-xl sm:text-2xl font-black text-gold-gradient font-mono tracking-tight mt-0.5">
                      {{ fmtInr(heroEmiResults.emi) }}<span class="text-xs text-kb-muted font-normal">/mo</span>
                    </div>
                  </div>
                  <div class="text-left sm:text-right space-y-0.5 border-t sm:border-t-0 sm:border-l border-kb-line/60 pt-2 sm:pt-0 sm:pl-3">
                    <div class="text-[11px] text-kb-body flex justify-between">
                      <span class="text-kb-muted">Interest:</span>
                      <span class="font-medium font-mono">{{ fmtInr(heroEmiResults.interest) }}</span>
                    </div>
                    <div class="text-[11px] text-emerald-400 font-semibold flex justify-between">
                      <span>Rate:</span>
                      <span class="font-mono">8.75% p.a.</span>
                    </div>
                  </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-2 text-xs pt-1">
                  <Link
                    :href="`/calculators?calc=emi`"
                    class="font-bold text-kb-accent hover:underline flex items-center gap-1 text-[11px]"
                  >
                    <span>Full Loan & CGTMSE Suite</span>
                    <SvgIcon name="i-arr" className="w-3 h-3" />
                  </Link>
                  <a
                    :href="`https://wa.me/917973461669?text=Hello%20Kulwinder,%20I%20am%20inquiring%20about%20a%20loan%20of%20${fmtInr(heroEmiAmount)}%20for%20${heroEmiYears}%20years.%20Can%20we%20check%20bank%20eligibility?`"
                    target="_blank"
                    class="text-emerald-400 hover:text-emerald-300 font-semibold flex items-center gap-1 text-[11px]"
                  >
                    <SvgIcon name="i-wa" className="w-3.5 h-3.5" />
                    <span>Check Eligibility</span>
                  </a>
                </div>
              </div>
            </div>

            <!-- Founder & Office Trust Card -->
            <div class="card-frame p-4 bg-kb-surface/70 border border-kb-border/40 rounded-2xl flex items-center justify-between gap-4 shadow-lg hover:border-kb-accent/40 transition">
              <div class="flex items-center gap-3">
                <div class="relative w-12 h-12 rounded-xl bg-gradient-to-br from-amber-300 via-amber-500 to-amber-700 p-0.5 shadow shrink-0">
                  <img
                    src="/images/kulwinder-avatar.png"
                    alt="Kulwinder Singh"
                    class="w-full h-full rounded-[10px] object-cover"
                  />
                  <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-400 border-2 border-kb-surface rounded-full shadow" title="Available for Consultation"></span>
                </div>
                <div>
                  <div class="flex items-center gap-1.5">
                    <h4 class="font-bold text-sm text-kb-text">Kulwinder Singh</h4>
                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">Founder & Advisor</span>
                  </div>
                  <p class="text-[11px] text-kb-muted">Central Town, Ludhiana · AMFI Registered</p>
                </div>
              </div>
              <a
                href="https://wa.me/917973461669?text=Hello%20Kulwinder%20ji,%20I%20would%20like%20to%20review%20my%20portfolio%20with%20you."
                target="_blank"
                class="shrink-0 text-xs px-3 py-1.5 rounded-lg border border-emerald-500/30 text-emerald-400 hover:bg-emerald-950/40 transition flex items-center gap-1.5"
                title="Send existing portfolio for free review"
              >
                <SvgIcon name="i-wa" className="w-3.5 h-3.5" />
                <span class="hidden sm:inline font-semibold">Free Audit</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Proof Bar -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-16 pt-12 border-t border-kb-line">
          <div
            v-for="(p, idx) in proof"
            :key="idx"
            class="card-frame p-4 flex items-start gap-3.5 hover:border-kb-border transition"
          >
            <div class="w-9 h-9 rounded-xl bg-kb-surface-3 flex items-center justify-center text-kb-accent shrink-0 shadow-inner">
              <SvgIcon :name="p.ic" className="w-4 h-4" />
            </div>
            <div>
              <div class="text-sm font-bold text-kb-text">{{ p.t }}</div>
              <div class="text-xs text-kb-muted mt-0.5 leading-relaxed">{{ p.d }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- INTERACTIVE SOLUTION FINDER WIZARD -->
    <section class="py-16 border-b border-kb-line bg-gradient-to-b from-kb-bg via-kb-surface/30 to-kb-bg relative">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <SolutionFinderWizard />
      </div>
    </section>

    <!-- INSTITUTIONAL PARTNERS & REGULATED ECOSYSTEM -->
    <section class="py-12 border-b border-kb-line bg-kb-surface-2/20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
          <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded badge-gold text-[10px] font-bold uppercase tracking-wider mb-1">
              <span>Ecosystem Access</span>
            </div>
            <h3 class="text-base sm:text-lg font-bold text-kb-text">Regulated Distribution with India's Premier Financial Institutions</h3>
          </div>
          <span class="text-xs text-kb-muted">Client folios, policies, and contracts held directly with AMCs and lenders</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
          <div
            v-for="inst in partnerInstitutions"
            :key="inst.name"
            class="p-3 rounded-2xl bg-kb-surface/80 border border-kb-line/80 flex flex-col items-center justify-center text-center gap-1.5 hover:border-kb-accent/50 hover:bg-kb-surface-2/60 transition-all duration-300 shadow-sm group"
          >
            <!-- Authentic Institutional Brand Emblem -->
            <div class="w-12 h-12 rounded-xl bg-gradient-to-b from-white to-slate-100 p-2 shadow-sm border border-white/20 flex items-center justify-center transition-all duration-300 group-hover:scale-105 group-hover:shadow-md">
              <img
                :src="'/images/partners/' + inst.id + '.svg'"
                :alt="inst.name + ' Official Logo'"
                class="w-full h-full object-contain filter drop-shadow-sm transition-transform duration-300 group-hover:scale-105"
                loading="lazy"
              />
            </div>

            <div class="text-xs font-bold text-kb-text group-hover:text-kb-accent transition truncate w-full">{{ inst.name }}</div>
            <div class="text-[10px] text-kb-muted">{{ inst.type }}</div>
            <span class="text-[9px] font-semibold px-1.5 py-0.2 rounded bg-kb-surface-2 text-kb-accent mt-0.5 border border-kb-border/20">{{ inst.badge }}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- SIX FIGURES THAT QUIETLY DECIDE WHAT YOU CAN DO -->
    <section class="py-20 bg-kb-surface/30 border-b border-kb-line">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
          <div class="inline-block px-3 py-1 rounded-full bg-kb-surface-2 text-xs font-semibold uppercase tracking-wider text-kb-accent border border-kb-border/40">
            The rules, in numbers
          </div>
          <h2 class="text-3xl sm:text-4xl font-bold font-display">
            Six figures that quietly decide <span class="italic text-gold-gradient font-serif">what you can do</span>
          </h2>
          <p class="text-sm text-kb-body leading-relaxed">
            Not marketing claims — the actual thresholds written into financial regulations. Most families encounter them only after a bank or insurer has already said no.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="(fig, idx) in figures"
            :key="idx"
            class="card-luxury p-6 relative group"
          >
            <div class="text-3xl sm:text-4xl font-bold text-gold-gradient font-display mb-2 group-hover:scale-105 transition-transform origin-left">
              {{ fig.v }}
            </div>
            <h3 class="text-base font-bold text-kb-text mb-2">{{ fig.l }}</h3>
            <p class="text-xs text-kb-muted leading-relaxed">{{ fig.d }}</p>
          </div>
        </div>

        <p class="text-[11px] text-kb-muted/80 text-center mt-8 max-w-4xl mx-auto">
          {{ data.figuresSrc }}
        </p>
      </div>
    </section>

    <!-- FIVE PILLARS OVERVIEW -->
    <section class="py-20 border-b border-kb-line">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
          <div class="inline-block px-3 py-1 rounded-full bg-kb-surface-2 text-xs font-semibold uppercase tracking-wider text-kb-accent border border-kb-border/40">
            Our Services
          </div>
          <h2 class="text-3xl sm:text-4xl font-bold font-display">
            Five pillars, one <span class="italic text-gold-gradient font-serif">trusted desk</span>
          </h2>
          <p class="text-sm text-kb-body leading-relaxed">
            Most families end up with a fund agent, an insurance agent, and a loan broker who never speak to each other. KB Finvest sits across all three so your entire financial picture holds together.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <Link
            v-for="(pillar, idx) in pillars"
            :key="idx"
            :href="pillar.url"
            class="card-luxury p-6 group hover:border-kb-accent transition block"
          >
            <div class="w-11 h-11 rounded-xl bg-kb-surface-3 flex items-center justify-center text-kb-accent mb-4 group-hover:scale-110 group-hover:bg-kb-accent group-hover:text-black transition shadow-md">
              <SvgIcon :name="pillar.ic" className="w-5 h-5" />
            </div>
            <h3 class="text-lg font-bold text-kb-text group-hover:text-kb-accent transition mb-2 flex items-center justify-between">
              <span>{{ pillar.t }}</span>
              <SvgIcon name="i-arr" className="w-4 h-4 text-kb-muted group-hover:text-kb-accent group-hover:translate-x-1.5 transition" />
            </h3>
            <p class="text-xs text-kb-body leading-relaxed">{{ pillar.d }}</p>
          </Link>
        </div>

        <!-- 30-min Review Ribbon -->
        <div class="mt-12 p-8 rounded-2xl bg-gradient-to-r from-kb-surface-3 via-kb-surface-2 to-kb-surface border border-kb-border flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl card-luxury">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-400/10 border border-amber-400/30 flex items-center justify-center text-kb-accent shrink-0 shadow-inner">
              <SvgIcon name="i-shield" className="w-6 h-6" />
            </div>
            <div>
              <h3 class="text-xl font-bold text-kb-text font-display">
                Plan Today. <span class="italic text-gold-gradient font-serif">Prosper Tomorrow.</span>
              </h3>
              <p class="text-xs text-kb-muted mt-1">
                A 30-minute review of what you already hold — mutual funds, policies, EMIs — at no charge and no obligation.
              </p>
            </div>
          </div>
          <Link
            href="/book"
            class="btn-shimmer w-full md:w-auto min-h-[44px] justify-center px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-wider bg-gold-gradient text-black hover:opacity-95 transition shrink-0 flex items-center gap-2 active:scale-95 shadow-lg shadow-amber-500/15"
          >
            <span>Start the review</span>
            <SvgIcon name="i-arr" className="w-4 h-4" />
          </Link>
        </div>
      </div>
    </section>

    <!-- MEET KULWINDER SINGH — FOUNDER & INVESTMENT ADVISOR SPOTLIGHT -->
    <section class="py-20 lg:py-28 border-b border-kb-line relative overflow-hidden bg-gradient-to-b from-kb-surface/60 via-kb-surface-2/30 to-kb-bg">
      <!-- Ambient light gradients -->
      <div class="absolute top-1/2 -left-20 -translate-y-1/2 w-[500px] h-[500px] bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
      <div class="absolute top-1/3 -right-20 w-[450px] h-[450px] bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full badge-gold text-xs font-semibold uppercase tracking-wider">
            <SvgIcon name="i-shield" className="w-3.5 h-3.5 text-kb-accent" />
            <span>Personal Leadership & Local Accountability</span>
          </div>
          <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold font-display text-kb-text">
            Meet <span class="italic text-gold-gradient font-serif">Kulwinder Singh</span>
          </h2>
          <p class="text-sm sm:text-base text-kb-body leading-relaxed max-w-2xl mx-auto">
            Founder & Principal Investment Advisor at KB Finvest. Your dedicated, relationship-led guide for wealth creation, family insurance, and business credit in Ludhiana.
          </p>
        </div>

        <!-- Main Spotlight Card -->
        <div class="card-prestige p-6 sm:p-10 lg:p-12 border border-kb-border/70 rounded-3xl shadow-2xl relative overflow-hidden bg-kb-surface/90 backdrop-blur-xl">
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
            
            <!-- Left Column: High-Res Framed Portrait with Prestige Gold Border -->
            <div class="lg:col-span-5 flex flex-col items-center">
              <div class="relative group w-full max-w-sm">
                <!-- Ambient outer glow behind portrait -->
                <div class="absolute -inset-1.5 rounded-3xl bg-gradient-to-tr from-amber-500/40 via-amber-300/25 to-blue-500/35 blur-xl opacity-75 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <!-- Portrait container -->
                <div class="relative rounded-2xl overflow-hidden border-2 border-amber-400/50 bg-gradient-to-b from-slate-900 via-kb-surface to-slate-950 shadow-2xl">
                  <img
                    src="/images/kulwinder-singh.png"
                    alt="Kulwinder Singh - Founder & Investment Advisor"
                    class="w-full h-auto object-cover object-top transition-transform duration-500 group-hover:scale-[1.02]"
                  />
                  
                  <!-- Bottom Nameplate Overlay matching flyer design -->
                  <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/95 via-slate-950/85 to-transparent pt-12 pb-5 px-5 text-center border-t border-amber-500/30">
                    <h3 class="text-xl sm:text-2xl font-bold font-display text-white tracking-wide">
                      Kulwinder Singh
                    </h3>
                    <p class="text-xs sm:text-sm text-kb-accent font-medium mt-0.5 font-serif italic">
                      Founder & Investment Advisor
                    </p>
                    <div class="flex items-center justify-center gap-2 mt-2">
                      <span class="text-[10px] uppercase font-bold tracking-wider px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40">
                        AMFI Registered · ARN-286477
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Floating Top Credential Badge -->
                <div class="absolute -top-3 -right-3 sm:-right-4 px-3.5 py-1.5 rounded-xl bg-kb-surface-2/95 border border-amber-400/60 text-xs font-bold text-kb-accent shadow-xl backdrop-blur-md flex items-center gap-1.5">
                  <SvgIcon name="i-cert" className="w-4 h-4 text-kb-accent" />
                  <span>Verified Practitioner</span>
                </div>
              </div>
            </div>

            <!-- Right Column: Founder Story, The 4 Flyer Pillars & Direct Contact -->
            <div class="lg:col-span-7 space-y-6">
              <!-- Banner Quote matching flyer -->
              <div class="p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-kb-text space-y-1.5 relative">
                <div class="text-[11px] font-bold uppercase tracking-widest text-kb-accent flex items-center gap-1.5">
                  <SvgIcon name="i-star" className="w-3.5 h-3.5 text-amber-400" />
                  <span>The KB Finvest Commitment</span>
                </div>
                <div class="text-xl sm:text-2xl font-display font-bold text-white tracking-tight">
                  "Smart Investments. Secure Future."
                </div>
                <p class="text-xs sm:text-sm text-kb-body leading-relaxed italic">
                  Your trusted partner in building wealth and achieving financial freedom with confidence.
                </p>
              </div>

              <!-- Personal Statement -->
              <p class="text-sm text-kb-body leading-relaxed">
                When you entrust your investments or family protection to KB Finvest, you don't talk to a revolving bank sales representative or an automated algorithm. You deal directly with me. Every portfolio review is conducted with complete fiduciary transparency, zero hidden charges, and personal advocacy when you need it most.
              </p>

              <!-- The 4 Core Pillars from the Poster -->
              <div class="space-y-2.5">
                <div class="text-xs font-bold uppercase tracking-wider text-kb-accent flex items-center gap-1.5">
                  <SvgIcon name="i-check" className="w-3.5 h-3.5 text-emerald-400" />
                  <span>Why Choose KB Finvest?</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                  <div class="p-3.5 rounded-xl bg-kb-surface-2/80 border border-kb-border/50 hover:border-amber-400/50 transition shadow-sm">
                    <div class="font-bold text-xs text-kb-text flex items-center gap-1.5">
                      <span class="text-emerald-400 text-sm font-black">✓</span>
                      <span>Personalized Financial Solutions</span>
                    </div>
                    <p class="text-[11px] text-kb-muted mt-1 leading-snug">
                      Custom portfolios tailored to your specific cash flow, horizon, and family milestones.
                    </p>
                  </div>
                  <div class="p-3.5 rounded-xl bg-kb-surface-2/80 border border-kb-border/50 hover:border-amber-400/50 transition shadow-sm">
                    <div class="font-bold text-xs text-kb-text flex items-center gap-1.5">
                      <span class="text-emerald-400 text-sm font-black">✓</span>
                      <span>Expert Guidance & Market Insights</span>
                    </div>
                    <p class="text-[11px] text-kb-muted mt-1 leading-snug">
                      Unbiased access across 40+ AMCs, top insurers, and banks without sales quotas.
                    </p>
                  </div>
                  <div class="p-3.5 rounded-xl bg-kb-surface-2/80 border border-kb-border/50 hover:border-amber-400/50 transition shadow-sm">
                    <div class="font-bold text-xs text-kb-text flex items-center gap-1.5">
                      <span class="text-emerald-400 text-sm font-black">✓</span>
                      <span>Transparent & Trustworthy Service</span>
                    </div>
                    <p class="text-[11px] text-kb-muted mt-1 leading-snug">
                      Zero hidden fees. Direct transfers to regulated AMCs with upfront disclosures.
                    </p>
                  </div>
                  <div class="p-3.5 rounded-xl bg-kb-surface-2/80 border border-kb-border/50 hover:border-amber-400/50 transition shadow-sm">
                    <div class="font-bold text-xs text-kb-text flex items-center gap-1.5">
                      <span class="text-emerald-400 text-sm font-black">✓</span>
                      <span>Client-Centric Approach</span>
                    </div>
                    <p class="text-[11px] text-kb-muted mt-1 leading-snug">
                      In-person office on Central Town. We stand beside your family during claims.
                    </p>
                  </div>
                </div>
              </div>

              <!-- Direct Contact Box matching flyer -->
              <div class="p-4 sm:p-5 rounded-2xl bg-kb-surface-3/70 border border-kb-border/70 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="space-y-1.5">
                  <div class="text-[10px] uppercase font-bold tracking-wider text-kb-accent flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Let's Grow Your Wealth Together</span>
                  </div>
                  <div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs text-kb-body">
                    <a href="tel:7973461669" class="hover:text-kb-accent transition font-mono font-bold flex items-center gap-1.5">
                      <SvgIcon name="i-phone" className="w-3.5 h-3.5 text-kb-accent" />
                      <span>79734-61669</span>
                    </a>
                    <a href="mailto:ks_sandhu07@yahoo.co.in" class="hover:text-kb-accent transition flex items-center gap-1.5">
                      <SvgIcon name="i-mail" className="w-3.5 h-3.5 text-kb-accent" />
                      <span>ks_sandhu07@yahoo.co.in</span>
                    </a>
                    <a href="https://www.instagram.com/kb_finvest_" target="_blank" rel="noopener" class="hover:text-pink-400 transition flex items-center gap-1.5 text-pink-400 font-medium">
                      <SvgIcon name="instagram" className="w-3.5 h-3.5 text-pink-400" />
                      <span>@kb_finvest_</span>
                    </a>
                  </div>
                  <div class="text-[11px] text-kb-muted flex items-center gap-1.5 pt-0.5">
                    <SvgIcon name="i-pin" className="w-3.5 h-3.5 text-kb-accent shrink-0" />
                    <span>179-A, St. No. 1, Central Town, Near Hotel Keys, Ludhiana, 142021</span>
                  </div>
                </div>

                <!-- Action CTAs -->
                <div class="flex items-center gap-2.5 shrink-0 w-full sm:w-auto">
                  <Link
                    href="/book"
                    class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-gold-gradient text-black font-bold text-xs uppercase tracking-wider hover:brightness-105 transition shadow-lg shadow-amber-500/15 flex items-center justify-center gap-1.5 btn-shimmer active:scale-95"
                  >
                    <SvgIcon name="i-cal" className="w-4 h-4" />
                    <span>Book 30m</span>
                  </Link>
                  <a
                    href="https://wa.me/917973461669?text=Hello%20Kulwinder%20ji,%20I%20saw%20your%20profile%20on%20KB%20Finvest%20and%20would%20like%20to%20consult%20you."
                    target="_blank"
                    rel="noopener"
                    class="px-4 py-2.5 rounded-xl border border-emerald-500/40 text-emerald-400 bg-emerald-950/40 hover:bg-emerald-950/60 transition flex items-center justify-center gap-1.5 text-xs font-semibold"
                  >
                    <SvgIcon name="i-wa" className="w-4 h-4" />
                    <span>WhatsApp</span>
                  </a>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </section>

    <!-- INTERACTIVE COMPARISON MATRIX: WHY KB FINVEST -->
    <section class="py-20 border-b border-kb-line bg-kb-surface/20 relative overflow-hidden">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
          <div class="inline-block px-3 py-1 rounded-full badge-gold text-xs font-semibold uppercase tracking-wider">
            Unbiased Comparison
          </div>
          <h2 class="text-3xl sm:text-4xl font-bold font-display">
            Why families choose KB Finvest over <span class="italic text-gold-gradient font-serif">Bank RMs or Apps</span>
          </h2>
          <p class="text-sm text-kb-body leading-relaxed">
            Financial decisions shouldn't be governed by a call center algorithm or a bank relationship manager trying to meet a monthly sales quota.
          </p>
        </div>

        <!-- Desktop Comparison Table / Cards -->
        <div class="card-prestige p-6 sm:p-8 border border-kb-border/40 shadow-2xl overflow-hidden">
          <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-stretch">
            <!-- Header Column -->
            <div class="hidden lg:flex flex-col justify-between py-2 border-r border-kb-line/60 pr-6">
              <div class="space-y-2">
                <span class="text-[10px] uppercase font-bold tracking-widest text-kb-muted">Dimension</span>
                <h4 class="text-xl font-bold font-display text-kb-text">Fiduciary Quality</h4>
                <p class="text-xs text-kb-muted leading-relaxed">
                  How advisory incentives, presence, and crisis assistance differ across distribution models in India.
                </p>
              </div>
              <div class="p-3 rounded-xl bg-kb-surface-3/40 border border-kb-line/50 text-[11px] text-kb-muted">
                🛡️ Regulated by SEBI & AMFI. No upfront advisory fees.
              </div>
            </div>

            <!-- KB Finvest Column (Highlighted) -->
            <div class="p-6 rounded-2xl bg-gradient-to-b from-amber-500/10 via-kb-surface-2 to-kb-surface-3/80 border-2 border-kb-accent/60 shadow-xl relative flex flex-col justify-between gap-4">
              <div class="absolute -top-3 left-6 px-3 py-0.5 rounded-full bg-gold-gradient text-black text-[10px] font-bold uppercase tracking-wider shadow">
                KB Finvest (Kulwinder Singh)
              </div>
              <div class="space-y-4 pt-2">
                <div class="space-y-1">
                  <div class="text-[11px] font-bold uppercase tracking-wider text-kb-accent">Your Advantage</div>
                  <div class="text-base font-bold text-kb-text">Lifelong Fiduciary Relationship</div>
                </div>
                <div class="space-y-3 text-xs text-kb-body">
                  <div class="flex items-start gap-2.5">
                    <SvgIcon name="i-check" className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                    <span><strong>100% Retained Advisor:</strong> Deal directly with Kulwinder Singh — no rotating staff or junior hand-offs.</span>
                  </div>
                  <div class="flex items-start gap-2.5">
                    <SvgIcon name="i-check" className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                    <span><strong>Walk-in Office:</strong> 179-A, Central Town, Ludhiana. Always accessible for meetings.</span>
                  </div>
                  <div class="flex items-start gap-2.5">
                    <SvgIcon name="i-check" className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                    <span><strong>Active Crisis Advocacy:</strong> Claim processing assistance and confidential loan debt resolution.</span>
                  </div>
                </div>
              </div>
              <Link
                href="/book"
                class="w-full py-2.5 rounded-xl bg-gold-gradient text-black font-bold text-xs uppercase tracking-wider text-center block hover:brightness-105 transition shadow btn-shimmer"
              >
                Schedule Meeting
              </Link>
            </div>

            <!-- Commercial Bank RM Column -->
            <div class="p-6 rounded-2xl bg-kb-surface-2/40 border border-kb-line/80 flex flex-col justify-between gap-4">
              <div class="space-y-4">
                <div class="space-y-1">
                  <div class="text-[11px] font-bold uppercase tracking-wider text-kb-muted">Traditional Approach</div>
                  <div class="text-base font-bold text-kb-text">Commercial Bank RMs</div>
                </div>
                <div class="space-y-3 text-xs text-kb-muted">
                  <div class="flex items-start gap-2.5">
                    <span class="text-rose-400 text-sm leading-none shrink-0 mt-0.5">✕</span>
                    <span>Frequent RM turnover — new managers every 12 to 18 months reset your history.</span>
                  </div>
                  <div class="flex items-start gap-2.5">
                    <span class="text-rose-400 text-sm leading-none shrink-0 mt-0.5">✕</span>
                    <span>Driven by internal quarterly revenue targets, pushing high-commission ULIPs.</span>
                  </div>
                  <div class="flex items-start gap-2.5">
                    <span class="text-rose-400 text-sm leading-none shrink-0 mt-0.5">✕</span>
                    <span>Branch queues; claims are directed to distant call centers.</span>
                  </div>
                </div>
              </div>
              <span class="text-[10px] text-center text-kb-muted py-2 border-t border-kb-line/40">Transactional Focus</span>
            </div>

            <!-- Generic Automated Apps Column -->
            <div class="p-6 rounded-2xl bg-kb-surface-2/40 border border-kb-line/80 flex flex-col justify-between gap-4">
              <div class="space-y-4">
                <div class="space-y-1">
                  <div class="text-[11px] font-bold uppercase tracking-wider text-kb-muted">Self-Serve</div>
                  <div class="text-base font-bold text-kb-text">Discount / Robo Apps</div>
                </div>
                <div class="space-y-3 text-xs text-kb-muted">
                  <div class="flex items-start gap-2.5">
                    <span class="text-rose-400 text-sm leading-none shrink-0 mt-0.5">✕</span>
                    <span>Zero behavioral coaching when market volatility or panic strikes.</span>
                  </div>
                  <div class="flex items-start gap-2.5">
                    <span class="text-rose-400 text-sm leading-none shrink-0 mt-0.5">✕</span>
                    <span>No local human presence or walk-in desk in Ludhiana.</span>
                  </div>
                  <div class="flex items-start gap-2.5">
                    <span class="text-rose-400 text-sm leading-none shrink-0 mt-0.5">✕</span>
                    <span>Zero assistance during complex medical claims or bank loan restructuring.</span>
                  </div>
                </div>
              </div>
              <span class="text-[10px] text-center text-kb-muted py-2 border-t border-kb-line/40">DIY Without Support</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- REALISTIC CLIENT SCENARIO VIGNETTES -->
    <section class="py-20 border-b border-kb-line">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
          <div class="inline-block px-3 py-1 rounded-full badge-emerald text-xs font-semibold uppercase tracking-wider">
            Proven Outcomes
          </div>
          <h2 class="text-3xl sm:text-4xl font-bold font-display">
            Real financial solutions for <span class="italic text-gold-gradient font-serif">Punjab businesses & families</span>
          </h2>
          <p class="text-sm text-kb-body leading-relaxed">
            Representative case scenarios showing how regulatory mastery and personalized guidance deliver measurable results.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div
            v-for="(vig, idx) in clientVignettes"
            :key="idx"
            class="card-luxury p-6 flex flex-col justify-between gap-6 relative group"
          >
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded badge-gold">
                  {{ vig.badge }}
                </span>
                <span class="text-[11px] font-mono font-bold text-emerald-400">{{ vig.metric }}</span>
              </div>
              <div class="text-[11px] text-kb-muted font-medium">{{ vig.tag }}</div>
              <h3 class="text-base font-bold text-kb-text group-hover:text-kb-accent transition">{{ vig.title }}</h3>
              <p class="text-xs text-kb-body leading-relaxed">{{ vig.desc }}</p>
            </div>
            <div class="pt-4 border-t border-kb-line/60 flex items-center justify-between">
              <Link
                href="/book"
                class="text-xs font-bold text-kb-accent hover:underline flex items-center gap-1"
              >
                <span>Discuss Similar Scenario</span>
                <SvgIcon name="i-arr" className="w-3.5 h-3.5" />
              </Link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- MONEY FLOW SAFETY (IT NEVER PASSES THROUGH OUR HANDS) -->
    <section class="py-20 bg-kb-surface/30">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
          <div class="inline-block px-3 py-1 rounded-full bg-kb-surface-2 text-xs font-semibold uppercase tracking-wider text-kb-accent border border-kb-border/40">
            Where your money actually goes
          </div>
          <h2 class="text-3xl sm:text-4xl font-bold font-display">
            It never passes through <span class="italic text-gold-gradient font-serif">our hands</span>
          </h2>
          <p class="text-sm text-kb-body leading-relaxed">
            The most common financial fraud in India starts with "transfer it to my account and I will invest it for you". Here is the only path your money takes with us.
          </p>
        </div>

        <!-- 3 Steps Flow Diagram -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative">
          <div
            v-for="(step, idx) in flow"
            :key="idx"
            :class="[
              'p-6 rounded-2xl border relative transition-all duration-300',
              step.mid
                ? 'bg-kb-surface-2 border-kb-border shadow-xl shadow-amber-500/5 card-luxury'
                : 'bg-kb-surface border-kb-line card-frame',
            ]"
          >
            <div class="w-10 h-10 rounded-xl bg-kb-surface-3 flex items-center justify-center text-kb-accent mb-4 shadow-inner">
              <SvgIcon :name="step.ic" className="w-5 h-5" />
            </div>
            <h3 class="text-base font-bold text-kb-text mb-2">{{ step.t }}</h3>
            <p class="text-xs text-kb-body leading-relaxed">{{ step.d }}</p>
          </div>
        </div>

        <!-- Warning Callout -->
        <div class="mt-8 p-4 rounded-xl bg-amber-950/30 border border-amber-500/40 text-xs text-amber-200/90 leading-relaxed flex items-start gap-3 backdrop-blur-md">
          <SvgIcon name="i-alert" className="w-5 h-5 text-amber-400 shrink-0 mt-0.5" />
          <div>
            <strong>{{ flowNote.t }}</strong> {{ flowNote.d }}
          </div>
        </div>

        <!-- Safety Rules Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
          <div
            v-for="(s, idx) in safety"
            :key="idx"
            class="p-4 rounded-xl bg-kb-surface-2/60 border border-kb-line text-xs card-frame"
          >
            <div class="flex items-center gap-2 font-bold text-kb-text mb-1">
              <SvgIcon :name="s.ic" className="w-4 h-4 text-kb-accent" />
              <span>{{ s.t }}</span>
            </div>
            <p class="text-kb-muted leading-relaxed">{{ s.d }}</p>
          </div>
        </div>

        <!-- Central Town Ludhiana Walk-in & Regulatory Seals Banner -->
        <div class="mt-14 card-prestige p-6 sm:p-8 border border-kb-border/50 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
          <div class="space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full badge-emerald text-xs font-semibold">
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
              <span>AMFI & IRDAI Verified Practice</span>
            </div>
            <h3 class="text-xl font-bold font-display text-kb-text">
              179-A, St. No. 1, Central Town, Ludhiana
            </h3>
            <p class="text-xs text-kb-body leading-relaxed max-w-2xl">
              Physical office open Monday through Saturday. Walk in with your existing mutual fund folios, insurance policies, or loan sanction letters for an honest, confidential second opinion.
            </p>
          </div>
          <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0 w-full lg:w-auto">
            <a
              href="https://maps.google.com/?q=179-A,+St.+No.+1,+Central+Town,+Ludhiana,+Punjab"
              target="_blank"
              rel="noopener"
              class="w-full sm:w-auto min-h-[44px] justify-center px-5 py-3 rounded-xl bg-kb-surface-2 hover:bg-kb-surface-3 border border-kb-line hover:border-kb-accent text-xs font-bold text-kb-text transition flex items-center gap-2"
            >
              <SvgIcon name="i-pin" className="w-4 h-4 text-kb-accent" />
              <span>Get Directions</span>
            </a>
            <Link
              href="/book"
              class="btn-shimmer w-full sm:w-auto min-h-[44px] justify-center px-5 py-3 rounded-xl bg-gold-gradient text-black font-bold text-xs uppercase tracking-wider hover:brightness-105 transition shadow-lg shadow-amber-500/15 flex items-center gap-2 active:scale-95"
            >
              <SvgIcon name="i-cal" className="w-4 h-4" />
              <span>Book Appointment</span>
            </Link>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
