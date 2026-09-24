<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import SvgIcon from './SvgIcon.vue';

const step = ref(1);

// Selected Goal
const selectedGoal = ref('wealth');

const goals = [
  {
    id: 'wealth',
    title: 'Grow Long-Term Wealth',
    desc: 'SIPs, Mutual Funds, PMS & Tax-Saving Portfolios',
    icon: 'i-growth',
    badge: 'Wealth & SIP',
  },
  {
    id: 'protect',
    title: 'Protect Family & Health',
    desc: 'Mediclaim with restoration & high-value Term Life',
    icon: 'i-shield',
    badge: 'Zero Co-Pay',
  },
  {
    id: 'loan',
    title: 'Fund Business or Property',
    desc: 'Collateral-free CGTMSE, LAP & Working Capital',
    icon: 'i-tax',
    badge: 'Up to ₹10 Cr',
  },
  {
    id: 'specialist',
    title: 'Specialist Advisory',
    desc: 'Unlisted/Pre-IPO opportunities & Stressed NPA resolution',
    icon: 'i-scale',
    badge: 'Confidential',
  },
];

// Step 2 parameters by goal
const wealthHorizon = ref(10);
const wealthMonthly = ref(15000);

const protectType = ref('family');
const protectCover = ref('25L');

const loanAmount = ref(5000000);
const loanType = ref('cgtmse');

const specialistType = ref('pe');

// Calculations for preview
const wealthProjection = computed(() => {
  const p = Number(wealthMonthly.value) || 10000;
  const y = Number(wealthHorizon.value) || 10;
  const r = 0.12 / 12;
  const n = y * 12;
  const invested = p * n;
  const fv = p * ((Math.pow(1 + r, n) - 1) / r) * (1 + r);
  return {
    invested: Math.round(invested),
    fv: Math.round(fv),
    gain: Math.round(fv - invested),
  };
});

function fmtInr(n) {
  if (!isFinite(n) || n <= 0) return '₹0';
  if (n >= 10000000) return '₹' + (n / 10000000).toFixed(2) + ' Cr';
  if (n >= 100000) return '₹' + (n / 100000).toFixed(1) + ' Lakh';
  return '₹' + Math.round(n).toLocaleString('en-IN');
}

// Result Configuration
const recommendation = computed(() => {
  if (selectedGoal.value === 'wealth') {
    return {
      title: 'Goal-Linked Equity & Hybrid Mutual Fund Portfolio',
      category: 'Investments & Wealth',
      topic: 'Mutual Funds & SIPs',
      lead: `By investing ${fmtInr(wealthMonthly.value)}/month for ${wealthHorizon.value} years, your projected portfolio reaches ~${fmtInr(wealthProjection.value.fv)} (at an indicative 12% CAGR) against ${fmtInr(wealthProjection.value.invested)} invested.`,
      highlights: [
        'Diversified asset allocation between Large, Mid & Flexi-cap schemes',
        'Direct AMFI ARN distribution — no advisory or account maintenance fee',
        'Annual portfolio rebalancing and tax-loss harvesting review in Central Town',
      ],
      bookUrl: `/book?topic=Mutual%20Funds%20%26%20SIPs&amount=${wealthMonthly.value}&duration=${wealthHorizon.value}y`,
      waText: `Hello Kulwinder, I ran the Solution Finder for an SIP of ${fmtInr(wealthMonthly.value)}/mo over ${wealthHorizon.value} years (target ~${fmtInr(wealthProjection.value.fv)}). Let us discuss suitability.`,
    };
  }

  if (selectedGoal.value === 'protect') {
    const coverText = protectCover.value === '1Cr' ? '₹1 Crore' : (protectCover.value === '50L' ? '₹50 Lakh' : '₹25 Lakh');
    return {
      title: 'Comprehensive Health Cover with Unlimited Restoration',
      category: 'Protection & Insurance',
      topic: 'Health Insurance',
      lead: `Optimal family health plan designed for ${protectType.value === 'family' ? 'Family (2 Adults + Kids)' : 'Individual'} with ${coverText} sum insured and zero room-rent capping.`,
      highlights: [
        'Zero deduction on room rent, ICU charges, and proportionate limits',
        'Pre-existing disease (PED) reduction rider to 1-2 years',
        'Dedicated claim support desk in Ludhiana to liaise with the insurer',
      ],
      bookUrl: `/book?topic=Health%20Insurance&cover=${protectCover.value}&type=${protectType.value}`,
      waText: `Hello Kulwinder, I used the Solution Finder for ${protectType.value} health insurance with ${coverText} cover. Please share scheme comparisons.`,
    };
  }

  if (selectedGoal.value === 'loan') {
    return {
      title: loanType.value === 'cgtmse' ? 'CGTMSE Collateral-Free MSME Credit' : 'Loan Against Property (LAP)',
      category: 'Business & Commercial Lending',
      topic: loanType.value === 'cgtmse' ? 'CGTMSE Loan' : 'Loan Against Property',
      lead: `Facilitation of ${fmtInr(loanAmount.value)} ${loanType.value === 'cgtmse' ? 'collateral-free credit under the CGTMSE framework' : 'long-tenure property mortgage'} through regulated banks.`,
      highlights: [
        loanType.value === 'cgtmse' ? 'No third-party guarantee or immovable property collateral required' : 'Low interest rates with tenures extending up to 15 years',
        'Full document preparation & project report financial modeling',
        'Direct coordination with scheduled commercial banks in Punjab',
      ],
      bookUrl: `/book?topic=${loanType.value === 'cgtmse' ? 'CGTMSE%20Loan' : 'Loan%20Against%20Property'}&amount=${loanAmount.value}`,
      waText: `Hello Kulwinder, I am seeking a loan of ${fmtInr(loanAmount.value)} for ${loanType.value === 'cgtmse' ? 'CGTMSE collateral-free MSME credit' : 'Loan Against Property'}. Can we discuss eligibility?`,
    };
  }

  return {
    title: specialistType.value === 'pe' ? 'Pre-IPO & Unlisted High-Growth Equity' : 'Stressed Loan & NPA Restructuring Support',
    category: 'Specialist Situations',
    topic: specialistType.value === 'pe' ? 'General Financial Planning' : 'Stressed Loan Advisory',
    lead: specialistType.value === 'pe'
      ? 'Access to pre-IPO equity allocations for accredited, knowledgeable investors.'
      : 'Calm, confidential advisory to navigate bank restructuring, SMA stages, and OTS negotiations.',
    highlights: [
      specialistType.value === 'pe' ? 'Carefully audited corporate cap tables & valuation multiples' : '100% confidential discussion directly with Kulwinder Singh',
      specialistType.value === 'pe' ? 'Demat transfer directly to your NSDL/CDSL account' : 'Clear roadmap before SARFAESI or DRT escalation',
      'One-on-one consultation in Central Town office or private phone call',
    ],
    bookUrl: `/book?topic=${specialistType.value === 'pe' ? 'General%20Financial%20Planning' : 'Stressed%20Loan%20Advisory'}`,
    waText: `Hello Kulwinder, I would like to book a confidential discussion regarding ${specialistType.value === 'pe' ? 'Pre-IPO opportunities' : 'Stressed account / NPA resolution'}.`,
  };
});
</script>

<template>
  <div class="card-prestige p-6 sm:p-10 border border-kb-border/50 shadow-2xl relative overflow-hidden">
    <!-- Ambient top glow -->
    <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-96 h-48 bg-amber-500/10 blur-3xl pointer-events-none rounded-full"></div>

    <!-- Header & Step Indicator -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-8 border-b border-kb-line/60 relative z-10">
      <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full badge-gold text-xs font-semibold uppercase tracking-wider mb-2">
          <SvgIcon name="i-search" className="w-3.5 h-3.5" />
          <span>Interactive Solution Finder</span>
        </div>
        <h3 class="text-2xl sm:text-3xl font-bold font-display text-kb-text">
          Find your personalized financial roadmap in <span class="italic text-gold-gradient font-serif">30 seconds</span>
        </h3>
      </div>

      <!-- Step Counter Pills -->
      <div class="flex items-center gap-2 shrink-0">
        <button
          v-for="s in [1, 2, 3]"
          :key="s"
          type="button"
          @click="s < step ? step = s : null"
          :class="[
            'w-8 h-8 rounded-full font-bold text-xs flex items-center justify-center transition-all',
            step === s
              ? 'bg-kb-accent text-black shadow-md shadow-amber-500/20 scale-105'
              : (step > s ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 cursor-pointer' : 'bg-kb-surface-3 text-kb-muted border border-kb-line cursor-default')
          ]"
        >
          <span v-if="step > s">✓</span>
          <span v-else>{{ s }}</span>
        </button>
      </div>
    </div>

    <!-- STEP 1: Select Primary Goal -->
    <div v-if="step === 1" class="py-8 space-y-6 relative z-10">
      <div class="text-center sm:text-left">
        <h4 class="text-lg font-bold text-kb-text font-display">Step 1: What is your primary financial focus today?</h4>
        <p class="text-xs text-kb-muted mt-1">Select the area where you need guidance. You can explore others anytime.</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <button
          v-for="g in goals"
          :key="g.id"
          type="button"
          @click="selectedGoal = g.id; step = 2"
          :class="[
            'p-5 rounded-2xl border text-left transition-all relative group flex flex-col justify-between gap-4',
            selectedGoal === g.id
              ? 'bg-kb-surface-2 border-kb-accent shadow-lg shadow-amber-500/10'
              : 'bg-kb-surface-2/40 border-kb-line hover:border-kb-border hover:bg-kb-surface-2/80'
          ]"
        >
          <div>
            <div class="flex items-center justify-between mb-3">
              <div class="w-10 h-10 rounded-xl bg-kb-surface-3 border border-kb-border/40 flex items-center justify-center text-kb-accent group-hover:scale-110 transition">
                <SvgIcon :name="g.icon" className="w-5 h-5" />
              </div>
              <span class="text-[10px] font-bold px-2 py-0.5 rounded badge-gold">{{ g.badge }}</span>
            </div>
            <div class="font-bold text-sm text-kb-text group-hover:text-kb-accent transition">{{ g.title }}</div>
            <p class="text-xs text-kb-muted mt-1 leading-relaxed">{{ g.desc }}</p>
          </div>
          <div class="flex items-center gap-1.5 text-xs font-semibold text-kb-accent mt-2 pt-2 border-t border-kb-line/40">
            <span>Continue</span>
            <SvgIcon name="i-arr" className="w-3.5 h-3.5 group-hover:translate-x-1 transition" />
          </div>
        </button>
      </div>
    </div>

    <!-- STEP 2: Parameters & Horizon -->
    <div v-else-if="step === 2" class="py-8 space-y-6 relative z-10">
      <div class="flex items-center justify-between">
        <div>
          <h4 class="text-lg font-bold text-kb-text font-display">Step 2: Tell us your scope & preferences</h4>
          <p class="text-xs text-kb-muted mt-1">Adjust the numbers to see your tailored strategy.</p>
        </div>
        <button
          type="button"
          @click="step = 1"
          class="text-xs text-kb-muted hover:text-kb-text flex items-center gap-1 underline underline-offset-4"
        >
          ← Change Focus
        </button>
      </div>

      <!-- Parameters for Wealth -->
      <div v-if="selectedGoal === 'wealth'" class="space-y-6 max-w-2xl bg-kb-surface-2/50 p-6 rounded-2xl border border-kb-line">
        <div>
          <div class="flex justify-between items-center text-xs font-semibold mb-2">
            <span class="text-kb-body">Monthly SIP Amount</span>
            <span class="text-kb-accent text-base font-bold font-mono">{{ fmtInr(wealthMonthly) }} / month</span>
          </div>
          <input
            type="range"
            min="1000"
            max="100000"
            step="1000"
            v-model="wealthMonthly"
            aria-label="Monthly SIP Amount"
            class="w-full"
          />
          <div class="flex justify-between text-[11px] text-kb-muted mt-1">
            <span>₹1,000</span>
            <span>₹25,000</span>
            <span>₹50,000</span>
            <span>₹1,00,000+</span>
          </div>
        </div>

        <div>
          <div class="flex justify-between items-center text-xs font-semibold mb-2">
            <span class="text-kb-body">Investment Horizon</span>
            <span class="text-kb-accent text-base font-bold font-mono">{{ wealthHorizon }} Years</span>
          </div>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
            <button
              v-for="y in [3, 5, 10, 15]"
              :key="y"
              type="button"
              @click="wealthHorizon = y"
              :class="[
                'py-2.5 rounded-xl text-xs font-bold transition border min-h-[44px] cursor-pointer',
                wealthHorizon === y
                  ? 'bg-kb-accent text-black border-kb-accent'
                  : 'bg-kb-surface text-kb-body border-kb-line hover:text-kb-text'
              ]"
            >
              {{ y }} Years
            </button>
          </div>
        </div>
      </div>

      <!-- Parameters for Protection -->
      <div v-else-if="selectedGoal === 'protect'" class="space-y-6 max-w-2xl bg-kb-surface-2/50 p-6 rounded-2xl border border-kb-line">
        <div>
          <label class="text-xs font-semibold text-kb-body block mb-2">Who are you covering?</label>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
            <button
              v-for="t in [
                { id: 'family', label: 'Self + Spouse + Kids' },
                { id: 'individual', label: 'Self (Individual)' },
                { id: 'parents', label: 'Parents / Seniors' },
              ]"
              :key="t.id"
              type="button"
              @click="protectType = t.id"
              :class="[
                'py-2.5 px-3 rounded-xl text-xs font-bold transition border text-center min-h-[44px] cursor-pointer flex items-center justify-center',
                protectType === t.id
                  ? 'bg-kb-accent text-black border-kb-accent'
                  : 'bg-kb-surface text-kb-body border-kb-line hover:text-kb-text'
              ]"
            >
              {{ t.label }}
            </button>
          </div>
        </div>

        <div>
          <label class="text-xs font-semibold text-kb-body block mb-2">Target Sum Insured</label>
          <div class="grid grid-cols-3 gap-2">
            <button
              v-for="cov in [
                { id: '25L', label: '₹25 Lakh' },
                { id: '50L', label: '₹50 Lakh' },
                { id: '1Cr', label: '₹1 Crore' },
              ]"
              :key="cov.id"
              type="button"
              @click="protectCover = cov.id"
              :class="[
                'py-2.5 rounded-xl text-xs font-bold transition border text-center min-h-[44px] cursor-pointer flex items-center justify-center',
                protectCover === cov.id
                  ? 'bg-kb-accent text-black border-kb-accent'
                  : 'bg-kb-surface text-kb-body border-kb-line hover:text-kb-text'
              ]"
            >
              {{ cov.label }}
            </button>
          </div>
        </div>
      </div>

      <!-- Parameters for Loan -->
      <div v-else-if="selectedGoal === 'loan'" class="space-y-6 max-w-2xl bg-kb-surface-2/50 p-6 rounded-2xl border border-kb-line">
        <div>
          <label class="text-xs font-semibold text-kb-body block mb-2">Preferred Facility Type</label>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <button
              type="button"
              @click="loanType = 'cgtmse'"
              :class="[
                'py-2.5 px-3 rounded-xl text-xs font-bold transition border text-center min-h-[44px] cursor-pointer flex items-center justify-center',
                loanType === 'cgtmse'
                  ? 'bg-kb-accent text-black border-kb-accent'
                  : 'bg-kb-surface text-kb-body border-kb-line hover:text-kb-text'
              ]"
            >
              CGTMSE (Collateral-Free)
            </button>
            <button
              type="button"
              @click="loanType = 'lap'"
              :class="[
                'py-2.5 px-3 rounded-xl text-xs font-bold transition border text-center min-h-[44px] cursor-pointer flex items-center justify-center',
                loanType === 'lap'
                  ? 'bg-kb-accent text-black border-kb-accent'
                  : 'bg-kb-surface text-kb-body border-kb-line hover:text-kb-text'
              ]"
            >
              Loan Against Property (LAP)
            </button>
          </div>
        </div>

        <div>
          <div class="flex justify-between items-center text-xs font-semibold mb-2">
            <span class="text-kb-body">Required Financing Amount</span>
            <span class="text-kb-accent text-base font-bold font-mono">{{ fmtInr(loanAmount) }}</span>
          </div>
          <input
            type="range"
            min="1000000"
            max="50000000"
            step="1000000"
            v-model="loanAmount"
            aria-label="Required Financing Amount"
            class="w-full"
          />
          <div class="flex justify-between text-[11px] text-kb-muted mt-1">
            <span>₹10 Lakh</span>
            <span>₹1 Crore</span>
            <span>₹3 Crore</span>
            <span>₹5 Crore+</span>
          </div>
        </div>
      </div>

      <!-- Parameters for Specialist -->
      <div v-else class="space-y-6 max-w-2xl bg-kb-surface-2/50 p-6 rounded-2xl border border-kb-line">
        <div>
          <label class="text-xs font-semibold text-kb-body block mb-2">Select Advisory Domain</label>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <button
              type="button"
              @click="specialistType = 'pe'"
              :class="[
                'py-2.5 px-3 rounded-xl text-xs font-bold transition border text-center min-h-[44px] cursor-pointer flex items-center justify-center',
                specialistType === 'pe'
                  ? 'bg-kb-accent text-black border-kb-accent'
                  : 'bg-kb-surface text-kb-body border-kb-line hover:text-kb-text'
              ]"
            >
              Pre-IPO & Unlisted Shares
            </button>
            <button
              type="button"
              @click="specialistType = 'stress'"
              :class="[
                'py-2.5 px-3 rounded-xl text-xs font-bold transition border text-center min-h-[44px] cursor-pointer flex items-center justify-center',
                specialistType === 'stress'
                  ? 'bg-kb-accent text-black border-kb-accent'
                  : 'bg-kb-surface text-kb-body border-kb-line hover:text-kb-text'
              ]"
            >
              Stressed Account / NPA Relief
            </button>
          </div>
        </div>
      </div>

      <div class="pt-2 flex flex-col sm:flex-row justify-end items-stretch sm:items-center">
        <button
          type="button"
          @click="step = 3"
          class="btn-shimmer w-full sm:w-auto min-h-[44px] justify-center px-6 py-3 rounded-xl bg-gold-gradient text-black font-bold text-xs uppercase tracking-wider hover:brightness-105 transition flex items-center gap-2 shadow-lg shadow-amber-500/15 active:scale-95 cursor-pointer"
        >
          <span>Generate Roadmap</span>
          <SvgIcon name="i-arr" className="w-4 h-4" />
        </button>
      </div>
    </div>

    <!-- STEP 3: Recommended Strategy & Direct Conversion -->
    <div v-else-if="step === 3" class="py-8 space-y-6 relative z-10 animate-in fade-in">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold uppercase tracking-wider text-kb-accent flex items-center gap-1.5">
          <SvgIcon name="i-check" className="w-4 h-4 text-emerald-400" />
          <span>Recommended Strategy for You</span>
        </span>
        <button
          type="button"
          @click="step = 2"
          class="text-xs text-kb-muted hover:text-kb-text flex items-center gap-1 underline underline-offset-4"
        >
          ← Adjust Inputs
        </button>
      </div>

      <div class="bg-gradient-to-br from-kb-surface-2 to-kb-surface border border-kb-border/50 rounded-2xl p-6 sm:p-8 space-y-6 shadow-xl">
        <div class="space-y-2">
          <span class="text-[11px] font-mono uppercase px-2.5 py-1 rounded bg-kb-surface-3 border border-kb-line text-kb-muted">
            {{ recommendation.category }}
          </span>
          <h4 class="text-xl sm:text-2xl font-bold text-kb-text font-display">
            {{ recommendation.title }}
          </h4>
          <p class="text-sm text-kb-body leading-relaxed max-w-3xl">
            {{ recommendation.lead }}
          </p>
        </div>

        <!-- Key Strategic Pillars -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2">
          <div
            v-for="(hl, idx) in recommendation.highlights"
            :key="idx"
            class="p-3.5 rounded-xl bg-kb-surface-3/50 border border-kb-line/60 text-xs text-kb-body flex items-start gap-2.5"
          >
            <SvgIcon name="i-check" className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
            <span>{{ hl }}</span>
          </div>
        </div>

        <!-- Conversion Action Buttons -->
        <div class="pt-4 border-t border-kb-line flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
          <Link
            :href="recommendation.bookUrl"
            class="btn-shimmer w-full sm:w-auto min-h-[44px] justify-center px-6 py-3.5 rounded-xl bg-gold-gradient text-black font-bold text-xs uppercase tracking-wider hover:brightness-105 transition flex items-center gap-2 shadow-lg shadow-amber-500/20 active:scale-95 text-center"
          >
            <SvgIcon name="i-cal" className="w-4 h-4" />
            <span>Book Consultation on this Plan</span>
          </Link>
          <a
            :href="`https://wa.me/917973461669?text=${encodeURIComponent(recommendation.waText)}`"
            target="_blank"
            rel="noopener"
            class="w-full sm:w-auto min-h-[44px] justify-center px-6 py-3.5 rounded-xl border border-emerald-500/40 text-emerald-400 bg-emerald-950/30 hover:bg-emerald-950/50 transition flex items-center gap-2 text-xs font-bold active:scale-95 text-center"
          >
            <SvgIcon name="i-wa" className="w-4 h-4" />
            <span>Discuss on WhatsApp</span>
          </a>
          <button
            type="button"
            @click="step = 1"
            class="w-full sm:w-auto min-h-[44px] justify-center px-4 py-3.5 text-xs text-kb-muted hover:text-kb-text transition cursor-pointer text-center"
          >
            Start Over
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
