<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SvgIcon from '@/Components/SvgIcon.vue';

const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
  initialTab: {
    type: String,
    default: 'mf',
  },
});

const activeTab = ref(props.initialTab);
const services = computed(() => props.data.services || []);
const ladder = computed(() => props.data.ladder || []);
const currentService = computed(() => services.value.find((s) => s.id === activeTab.value) || services.value[0]);
const onboarding = computed(() => props.data.onboarding || {});

const calculatorLink = computed(() => {
  if (activeTab.value === 'bonds') {
    return { url: '/calculators?calc=lump', label: 'Fixed Return Yield Calc' };
  }
  if (activeTab.value === 'pms') {
    return { url: '/calculators?calc=lump', label: 'PMS Lump Sum Growth' };
  }
  if (activeTab.value === 'mkt') {
    return { url: '/calculators?calc=goal', label: 'Model Target Goal' };
  }
  return { url: '/calculators?calc=sip', label: 'Calculate SIP Returns' };
});
</script>

<template>
  <AppLayout>
    <Head title="Investments — Mutual Funds, PMS & Bonds in Ludhiana" />

    <!-- Hero Header -->
    <section class="py-12 border-b border-kb-line bg-kb-surface/30">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl space-y-3">
          <div class="inline-block px-3 py-1 rounded-full bg-kb-surface-2 text-xs font-semibold uppercase tracking-wider text-kb-accent border border-kb-border/40">
            Investments & Wealth Creation
          </div>
          <h1 class="text-3xl sm:text-5xl font-bold font-display">
            Grow what you earn, <span class="italic text-gold-gradient font-serif">on purpose</span>
          </h1>
          <p class="text-base text-kb-body leading-relaxed">
            Goal first, product second. We map the money you need and when you need it, then use mutual funds, professionally managed portfolios, or market instruments to get there.
          </p>
        </div>

        <!-- Service Tabs -->
        <div class="flex items-center gap-2 mt-8 overflow-x-auto no-scrollbar pb-2 pt-1">
          <button
            v-for="svc in services"
            :key="svc.id"
            @click="activeTab = svc.id"
            :class="[
              'px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition-all whitespace-nowrap flex items-center gap-2 border min-h-[44px] cursor-pointer',
              activeTab === svc.id
                ? 'bg-kb-accent text-black border-kb-accent shadow-md shadow-amber-500/15'
                : 'bg-kb-surface text-kb-body border-kb-line hover:border-kb-border hover:text-kb-text',
            ]"
          >
            <SvgIcon :name="svc.ic" className="w-4 h-4" />
            <span>{{ svc.title }}</span>
          </button>
        </div>
      </div>
    </section>

    <!-- Active Service Detail -->
    <section v-if="currentService" class="py-12 border-b border-kb-line">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <!-- Lead & Tag -->
        <div class="p-5 sm:p-8 rounded-2xl card-luxury space-y-4">
          <div class="flex flex-wrap items-center justify-between gap-4">
            <span class="px-3 py-1 rounded-full bg-kb-surface-3 text-xs font-bold text-kb-accent border border-kb-border/30 uppercase tracking-wider">
              {{ currentService.tag }}
            </span>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center w-full sm:w-auto gap-2.5">
              <a
                v-if="activeTab === 'mf' && onboarding.url"
                :href="onboarding.url"
                target="_blank"
                rel="noopener"
                class="w-full sm:w-auto min-h-[44px] justify-center px-4 py-2 rounded-xl font-bold text-xs bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-500/30 transition flex items-center gap-1.5 active:scale-95 shadow-sm"
                title="Open paperless account & start SIP online via NJ Wealth"
              >
                <SvgIcon name="i-rocket" className="w-4 h-4 text-emerald-400" />
                <span>Invest Online (e-KYC)</span>
              </a>
              <Link
                :href="calculatorLink.url"
                class="w-full sm:w-auto min-h-[44px] justify-center px-4 py-2 rounded-xl font-semibold text-xs border border-kb-border text-kb-accent hover:bg-kb-surface-2 transition flex items-center gap-1.5"
              >
                <SvgIcon name="i-calc" className="w-4 h-4" />
                <span>{{ calculatorLink.label }}</span>
              </Link>
              <Link
                :href="`/book?topic=${encodeURIComponent(currentService.title)}`"
                class="btn-shimmer w-full sm:w-auto min-h-[44px] justify-center px-5 py-2 rounded-xl font-bold text-xs uppercase tracking-wider bg-gold-gradient text-black hover:opacity-95 transition flex items-center gap-2 active:scale-95 shadow-lg shadow-amber-500/15"
              >
                <SvgIcon name="i-cal" className="w-4 h-4" />
                <span>Book consultation</span>
              </Link>
            </div>
          </div>
          <p class="text-lg text-kb-text font-medium leading-relaxed">
            {{ currentService.lead }}
          </p>
        </div>

        <!-- Two Columns: Who is it for & What we do -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <!-- Who is it for -->
          <div class="card-luxury p-6 space-y-4">
            <h3 class="text-base font-bold text-kb-text flex items-center gap-2">
              <SvgIcon name="i-users" className="w-4 h-4 text-kb-accent" />
              <span>Who this is for</span>
            </h3>
            <ul class="space-y-2.5 text-xs text-kb-body">
              <li v-for="(w, idx) in currentService.who" :key="idx" class="flex items-start gap-2.5">
                <SvgIcon name="i-check" className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                <span>{{ w }}</span>
              </li>
            </ul>
          </div>

          <!-- What we do -->
          <div class="card-luxury p-6 space-y-4">
            <h3 class="text-base font-bold text-kb-text flex items-center gap-2">
              <SvgIcon name="i-target" className="w-4 h-4 text-kb-accent" />
              <span>How we handle it</span>
            </h3>
            <ul class="space-y-2.5 text-xs text-kb-body">
              <li v-for="(item, idx) in currentService.what" :key="idx" class="flex items-start gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-kb-accent shrink-0 mt-1.5"></span>
                <span>{{ item }}</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Facts, Documents & Steps Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Key Facts -->
          <div class="card-frame p-6 space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-kb-muted">Key Facts</h3>
            <div class="space-y-3">
              <div
                v-for="(f, idx) in currentService.facts"
                :key="idx"
                class="flex items-center justify-between pb-2 border-b border-kb-line/40 text-xs"
              >
                <span class="text-kb-muted">{{ f[0] }}</span>
                <span class="font-bold text-kb-text">{{ f[1] }}</span>
              </div>
            </div>
          </div>

          <!-- Documents Checklist -->
          <div class="card-frame p-6 space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-kb-muted">Documents Needed</h3>
            <ul class="space-y-2 text-xs text-kb-body">
              <li v-for="(doc, idx) in currentService.docs" :key="idx" class="flex items-start gap-2">
                <SvgIcon name="i-doc" className="w-3.5 h-3.5 text-kb-accent shrink-0 mt-0.5" />
                <span>{{ doc }}</span>
              </li>
            </ul>
          </div>

          <!-- Process Steps -->
          <div class="card-frame p-6 space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-kb-muted">Step-by-step Process</h3>
            <ol class="space-y-2.5 text-xs text-kb-body">
              <li v-for="(step, idx) in currentService.steps" :key="idx" class="flex items-start gap-2.5">
                <span class="w-5 h-5 rounded-full bg-kb-surface-3 border border-kb-line flex items-center justify-center text-[10px] font-bold text-kb-accent shrink-0">
                  {{ idx + 1 }}
                </span>
                <span>{{ step }}</span>
              </li>
            </ol>
          </div>
        </div>

        <!-- DEDICATED NJ WEALTH DIGITAL ONBOARDING DESK CARD -->
        <div
          v-if="activeTab === 'mf' && onboarding.url"
          v-reveal
          class="card-prestige p-6 sm:p-10 border-2 border-emerald-500/35 bg-gradient-to-br from-emerald-950/25 via-kb-surface-2 to-kb-surface-3/90 shadow-2xl relative overflow-hidden rounded-3xl"
        >
          <!-- Background ambient glow -->
          <div class="absolute -top-24 -right-24 w-88 h-88 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

          <!-- Top Badge & Identity Header -->
          <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 pb-6 border-b border-kb-line/60 relative z-10">
            <div class="space-y-2">
              <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-xs font-bold text-emerald-400 uppercase tracking-wider">
                <SvgIcon name="i-rocket" className="w-3.5 h-3.5 text-emerald-400" />
                <span>{{ onboarding.badge }}</span>
              </div>
              <h3 class="text-2xl sm:text-3xl font-bold font-display text-kb-text tracking-tight">
                {{ onboarding.title }}
              </h3>
              <p class="text-xs sm:text-sm text-kb-muted max-w-2xl leading-relaxed">
                {{ onboarding.subtitle }}
              </p>
            </div>

            <!-- Partner Trust Pill -->
            <div class="p-3.5 rounded-2xl bg-kb-surface/90 border border-kb-border/60 text-left lg:text-right shrink-0 shadow-sm">
              <div class="text-[10px] uppercase font-bold tracking-wider text-kb-accent">Official Distributor Desk</div>
              <div class="text-sm font-bold text-kb-text">Kulwinder Singh</div>
              <div class="text-[11px] text-kb-muted font-mono mt-0.5">AMFI ARN-178400 · Partner: {{ onboarding.partnerCode }}</div>
            </div>
          </div>

          <!-- 4 Features Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 py-8 relative z-10">
            <div
              v-for="(f, idx) in onboarding.features"
              :key="idx"
              class="p-4 rounded-2xl bg-kb-surface-2/80 border border-kb-line hover:border-emerald-500/40 transition-all space-y-2.5 shadow-sm"
            >
              <div class="w-9 h-9 rounded-xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                <SvgIcon :name="f.ic" className="w-4 h-4" />
              </div>
              <h4 class="font-bold text-xs text-kb-text">{{ f.t }}</h4>
              <p class="text-[11px] text-kb-muted leading-relaxed">{{ f.d }}</p>
            </div>
          </div>

          <!-- Action CTA Bar -->
          <div class="pt-6 border-t border-kb-line/60 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 relative z-10">
            <div class="text-xs text-kb-muted flex items-center gap-2">
              <SvgIcon name="i-lock" className="w-4 h-4 text-emerald-400 shrink-0" />
              <span>100% Regulated & Secure. Money moves directly between your bank and SEBI-registered AMCs.</span>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
              <Link
                href="/book?topic=Mutual%20Funds%20%26%20SIPs"
                class="px-5 py-3 rounded-xl border border-kb-line hover:border-kb-accent text-xs font-bold text-kb-text text-center transition min-h-[44px] flex items-center justify-center"
              >
                <span>Prefer In-Person? Book Desk Meeting</span>
              </Link>
              <a
                :href="onboarding.url"
                target="_blank"
                rel="noopener"
                class="btn-shimmer px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-wider bg-gold-gradient text-black hover:brightness-105 transition flex items-center justify-center gap-2 shadow-lg shadow-amber-500/20 active:scale-95 min-h-[44px]"
              >
                <span>Start Paperless Onboarding (5 mins)</span>
                <SvgIcon name="i-arr" className="w-4 h-4" />
              </a>
            </div>
          </div>
        </div>

        <!-- LOAN AGAINST MUTUAL FUNDS (LAMF) PROMINENT FEATURE CARD -->
        <div
          v-reveal
          class="card-prestige p-6 sm:p-8 border border-amber-500/35 bg-gradient-to-r from-amber-950/20 via-kb-surface-2 to-kb-surface-3/80 shadow-xl rounded-3xl relative overflow-hidden"
        >
          <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
              <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full badge-gold text-xs font-bold uppercase tracking-wider">
                <SvgIcon name="i-coin" className="w-3.5 h-3.5 text-kb-accent" />
                <span>Instant Liquidity · Zero Redemption Loss</span>
              </div>
              <h3 class="text-xl sm:text-2xl font-bold font-display text-kb-text">
                Need Funds Urgently? <span class="text-gold-gradient italic font-serif">Never Break Your Compounding SIPs</span>
              </h3>
              <p class="text-xs sm:text-sm text-kb-muted leading-relaxed">
                Avail a fast <strong>Loan Against Mutual Funds (LAMF)</strong> with an overdraft credit line up to 75% of your portfolio value. Your units remain lien-marked and continue compounding in the market with zero capital gains tax triggered.
              </p>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0 w-full lg:w-auto">
              <Link
                href="/loans?tab=lamf"
                class="btn-shimmer px-5 py-3 rounded-xl font-bold text-xs uppercase tracking-wider bg-gold-gradient text-black hover:brightness-105 transition flex items-center justify-center gap-2 shadow-md shadow-amber-500/15 min-h-[44px]"
              >
                <span>Explore Loan Against MF</span>
                <SvgIcon name="i-arr" className="w-4 h-4" />
              </Link>
            </div>
          </div>
        </div>

        <!-- Note & Disclaimers -->
        <div class="p-4 rounded-xl bg-kb-surface-2 border border-kb-border/50 text-xs leading-relaxed space-y-2">
          <div class="flex items-start gap-2 text-kb-text font-medium">
            <SvgIcon name="i-info" className="w-4 h-4 text-kb-accent shrink-0 mt-0.5" />
            <span>{{ currentService.note }}</span>
          </div>
          <div class="text-[11px] text-kb-muted pl-6">
            {{ currentService.disc }}
          </div>
        </div>
      </div>
    </section>

    <!-- INVESTMENT RISK LADDER -->
    <section class="py-16 bg-kb-surface/30">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="max-w-3xl space-y-2">
          <h2 class="text-2xl sm:text-3xl font-bold font-display">
            Match the instrument to the <span class="italic text-gold-gradient font-serif">time you have</span>
          </h2>
          <p class="text-sm text-kb-muted leading-relaxed">
            Risk is not a personality trait, it is a function of when you need the money. This is the ladder every conversation here starts from.
          </p>
        </div>

        <div class="space-y-3">
          <div
            v-for="(item, idx) in ladder"
            :key="idx"
            class="card-frame p-4 flex flex-col md:flex-row md:items-center justify-between gap-4"
          >
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-kb-surface-3 flex items-center justify-center font-mono text-xs font-bold text-kb-accent">
                {{ idx + 1 }}
              </div>
              <div>
                <div class="text-sm font-bold text-kb-text">{{ item.nm }}</div>
                <div class="text-xs text-kb-muted mt-0.5">{{ item.hz }}</div>
              </div>
            </div>

            <!-- Risk visual meter -->
            <div class="flex items-center gap-3 w-full md:w-56 shrink-0">
              <div class="flex-1 bg-kb-surface-3 rounded-full h-2 overflow-hidden border border-kb-line">
                <div
                  class="h-full bg-gradient-to-r from-emerald-500 via-amber-400 to-rose-500 rounded-full"
                  :style="{ width: `${item.risk}%` }"
                ></div>
              </div>
              <span class="text-xs font-mono font-bold text-kb-muted w-10 text-right">{{ item.risk }}%</span>
            </div>
          </div>
        </div>

        <p class="text-[11px] text-kb-muted">
          Categories are illustrative and generic. They are not recommendations, and actual risk varies by scheme, issuer, and market conditions. Read all scheme-related documents carefully.
        </p>
      </div>
    </section>
  </AppLayout>
</template>
