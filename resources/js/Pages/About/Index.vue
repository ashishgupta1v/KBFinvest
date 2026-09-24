<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SvgIcon from '@/Components/SvgIcon.vue';

const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
});

const paragraphs = computed(() => props.data.aboutParagraphs || []);
const registrations = computed(() => props.data.registrations || []);
const frameworks = computed(() => props.data.frameworks || []);
const vsTable = computed(() => props.data.vsTable || []);
const fees = computed(() => props.data.fees || []);
const never = computed(() => props.data.never || []);
</script>

<template>
  <AppLayout>
    <Head title="About — Kulwinder Singh & KB Finvest Ludhiana" />

    <!-- Hero Header with Ambient Glow -->
    <section class="relative py-16 lg:py-20 border-b border-kb-line bg-gradient-to-b from-kb-surface-2/60 via-kb-surface/40 to-kb-surface overflow-hidden">
      <div class="absolute -top-32 -left-20 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
      <div class="absolute -bottom-20 right-10 w-96 h-96 bg-amber-400/5 rounded-full blur-3xl pointer-events-none"></div>

      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl space-y-4">
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-xs font-semibold uppercase tracking-wider text-amber-300">
            <SvgIcon name="shield-check" class="w-3.5 h-3.5 text-amber-400" />
            <span>Independent & Fiduciary-Aligned Practice</span>
          </div>
          <h1 class="text-3xl sm:text-5xl lg:text-6xl font-bold font-display tracking-tight text-kb-text">
            The person behind <span class="italic text-gold-gradient font-serif">KB Finvest</span>
          </h1>
          <p class="text-base sm:text-lg text-kb-body leading-relaxed max-w-2xl">
            Founded by <strong>Kulwinder Singh</strong> in Central Town, Ludhiana. Built on the belief that families and businesses deserve unified financial guidance across investments, insurance, and lending without hidden agendas.
          </p>
        </div>
      </div>
    </section>

    <!-- Bio & Credentials Grid -->
    <section class="py-16 border-b border-kb-line">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
          <!-- Left: Narrative -->
          <div class="lg:col-span-7 space-y-8">
            <div>
              <span class="text-xs font-bold uppercase tracking-wider text-kb-accent">Our Journey & Philosophy</span>
              <h2 class="text-2xl sm:text-3xl font-bold font-display text-kb-text mt-1">
                Fiduciary Responsibility Over Product Push
              </h2>
            </div>

            <!-- Narrative Paragraphs -->
            <div class="space-y-4 text-sm sm:text-base text-kb-body leading-relaxed font-light">
              <p v-for="(p, idx) in paragraphs" :key="idx" v-html="p" class="p-4 rounded-xl bg-kb-surface-2/30 border border-kb-border/30 hover:border-kb-border/60 transition-colors"></p>
            </div>

            <!-- Regulatory Frameworks Grid -->
            <div class="pt-6 border-t border-kb-line space-y-4">
              <div>
                <span class="text-xs font-bold uppercase tracking-wider text-kb-accent">Regulatory Standards</span>
                <h3 class="text-xl font-bold font-display text-kb-text mt-1">The Rulebooks We Operate Under</h3>
              </div>
              <p class="text-xs sm:text-sm text-kb-muted leading-relaxed">
                Published so you can hold us accountable against them. Naming a regulator is a statement of jurisdiction and grievance escalation paths.
              </p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <a
                  v-for="(f, idx) in frameworks"
                  :key="idx"
                  :href="f.url"
                  target="_blank"
                  rel="noopener"
                  class="card-luxury p-5 hover:border-amber-400/50 hover:scale-[1.01] transition-all duration-300 group block"
                >
                  <div class="font-bold text-sm text-amber-300 font-display group-hover:text-amber-200 flex items-center justify-between">
                    <span>{{ f.b }}</span>
                    <SvgIcon name="i-arr" className="w-3.5 h-3.5 text-kb-muted group-hover:text-amber-400 group-hover:translate-x-0.5 transition-transform" />
                  </div>
                  <div class="text-xs text-kb-muted mt-2 leading-relaxed">{{ f.d }}</div>
                </a>
              </div>
            </div>
          </div>

          <!-- Right: Profile Card & Credentials -->
          <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-24">
            <!-- Profile Plate with card-luxury -->
            <div class="card-luxury p-6 sm:p-8 space-y-6 border-amber-500/30">
              <div class="flex items-center gap-4">
                <div class="relative w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-200 via-amber-400 to-amber-600 p-0.5 shadow-xl shrink-0 overflow-hidden">
                  <img
                    src="/images/kulwinder-avatar.png"
                    alt="Kulwinder Singh"
                    class="w-full h-full rounded-[14px] object-cover"
                  />
                  <div class="absolute -bottom-0.5 -right-0.5 w-5 h-5 rounded-full bg-emerald-500 border-2 border-slate-950 flex items-center justify-center shadow" title="Verified Practitioner">
                    <span class="text-[10px] text-white font-bold leading-none">✓</span>
                  </div>
                </div>
                <div>
                  <div class="flex items-center gap-2">
                    <h3 class="text-xl font-bold text-kb-text font-display">Kulwinder Singh</h3>
                    <span class="px-2 py-0.5 rounded-full bg-amber-500/15 text-[10px] font-bold text-amber-300 border border-amber-500/30">ARN-286477</span>
                  </div>
                  <p class="text-xs text-kb-accent font-medium mt-0.5">Founder · Mutual Fund Distributor</p>
                  <p class="text-xs text-kb-muted">Central Town, Ludhiana, Punjab</p>
                </div>
              </div>

              <!-- Registrations -->
              <div class="space-y-3 pt-4 border-t border-kb-line">
                <div class="text-xs font-bold uppercase tracking-wider text-kb-muted">Registrations & Compliance</div>
                <div class="space-y-2.5">
                  <div
                    v-for="(r, idx) in registrations"
                    :key="idx"
                    class="flex items-center justify-between pb-2.5 border-b border-kb-line/40 text-xs"
                  >
                    <span class="text-kb-muted">{{ r.k }}</span>
                    <span class="font-bold text-kb-text font-mono">{{ r.v }}</span>
                  </div>
                </div>
              </div>

              <!-- Office Card -->
              <div class="p-4 rounded-xl bg-kb-surface-2/60 border border-kb-border/50 text-xs space-y-2">
                <div class="font-bold text-kb-text flex items-center gap-2">
                  <SvgIcon name="i-pin" className="w-4 h-4 text-amber-400" />
                  <span>Central Town Desk & Consultation Room</span>
                </div>
                <p class="text-kb-muted leading-relaxed">
                  179-A, St. No. 1, Central Town, Near Hotel Keys, Ludhiana, Punjab.
                </p>
                <div class="pt-2">
                  <a
                    href="https://www.google.com/maps/search/?api=1&query=KB+Finvest+Central+Town+Ludhiana"
                    target="_blank"
                    rel="noopener"
                    class="text-amber-400 font-bold hover:underline inline-flex items-center gap-1.5"
                  >
                    <span>View on Google Maps</span>
                    <SvgIcon name="i-arr" className="w-3.5 h-3.5" />
                  </a>
                </div>
              </div>

              <Link
                href="/book"
                class="btn-shimmer w-full min-h-[44px] flex items-center justify-center text-center py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider bg-gold-gradient text-slate-950 block shadow-lg hover:brightness-110 transition"
              >
                Schedule an in-person meeting
              </Link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- THE DIFFERENCE: CONVENTIONAL VS HOW WE RUN -->
    <section class="py-16 border-b border-kb-line bg-gradient-to-b from-kb-surface via-kb-surface-2/30 to-kb-surface">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="max-w-3xl space-y-2">
          <span class="text-xs font-bold uppercase tracking-wider text-kb-accent">Ethical Standards</span>
          <h2 class="text-2xl sm:text-4xl font-bold font-display text-kb-text">
            The difference, <span class="italic text-gold-gradient font-serif">stated plainly</span>
          </h2>
          <p class="text-xs sm:text-sm text-kb-muted">
            No competitor is named here. This is simply how a product-led push usually runs, versus how this desk chooses to operate.
          </p>
        </div>

        <div class="card-luxury overflow-hidden border-kb-line p-0">
          <div class="grid grid-cols-1 md:grid-cols-2 text-xs divide-y md:divide-y-0 md:divide-x divide-kb-line">
            <div class="p-4 sm:p-5 bg-rose-500/10 font-bold uppercase tracking-wider text-rose-400 flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-rose-400"></span>
              <span>The Conventional Product-Led Pitch</span>
            </div>
            <div class="p-4 sm:p-5 bg-amber-500/15 font-bold uppercase tracking-wider text-amber-300 flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-amber-400"></span>
              <span>How KB Finvest Runs Instead</span>
            </div>
          </div>
          <div class="divide-y divide-kb-line/40">
            <div
              v-for="(row, idx) in vsTable"
              :key="idx"
              class="grid grid-cols-1 md:grid-cols-2 text-xs divide-y md:divide-y-0 md:divide-x divide-kb-line/40 hover:bg-kb-surface-2/40 transition-colors"
            >
              <div class="p-4 sm:p-5 text-kb-muted flex items-start gap-3">
                <span class="text-rose-400 font-bold text-sm leading-none shrink-0 mt-0.5">✕</span>
                <div>
                  <span class="md:hidden text-[10px] font-bold uppercase tracking-wider text-rose-400 block mb-0.5">Conventional Approach</span>
                  <span class="leading-relaxed">{{ row[0] }}</span>
                </div>
              </div>
              <div class="p-4 sm:p-5 text-kb-text font-medium flex items-start gap-3 bg-amber-500/5">
                <span class="text-emerald-400 font-bold text-sm leading-none shrink-0 mt-0.5">✓</span>
                <div>
                  <span class="md:hidden text-[10px] font-bold uppercase tracking-wider text-amber-300 block mb-0.5">KB Finvest Approach</span>
                  <span class="leading-relaxed">{{ row[1] }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CODE OF CONDUCT & WHAT WE NEVER DO -->
    <section class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <!-- Fee Policy -->
          <div class="card-luxury p-6 sm:p-8 space-y-5 border-emerald-500/20">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-emerald-500/15 flex items-center justify-center text-emerald-400">
                <SvgIcon name="i-coin" className="w-5 h-5" />
              </div>
              <div>
                <h3 class="text-lg font-bold font-display text-kb-text">What You Pay Us</h3>
                <p class="text-xs text-kb-muted">Transparent disclosure of remuneration</p>
              </div>
            </div>
            <ul class="space-y-3.5 text-xs sm:text-sm text-kb-body pt-2">
              <li v-for="(fee, idx) in fees" :key="idx" class="flex items-start gap-3">
                <SvgIcon name="i-check" className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                <span class="leading-relaxed">{{ fee }}</span>
              </li>
            </ul>
          </div>

          <!-- What We Never Do -->
          <div class="card-luxury p-6 sm:p-8 space-y-5 border-rose-500/20">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-rose-500/15 flex items-center justify-center text-rose-400">
                <SvgIcon name="i-lock" className="w-5 h-5" />
              </div>
              <div>
                <h3 class="text-lg font-bold font-display text-kb-text">What We Will Never Do</h3>
                <p class="text-xs text-kb-muted">Non-negotiable ethical red lines</p>
              </div>
            </div>
            <ul class="space-y-3.5 text-xs sm:text-sm text-kb-body pt-2">
              <li v-for="(n, idx) in never" :key="idx" class="flex items-start gap-3">
                <span class="text-rose-400 font-bold text-sm leading-none shrink-0 mt-0.5">✕</span>
                <span class="leading-relaxed">{{ n }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>

