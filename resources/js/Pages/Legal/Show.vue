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

const currentDoc = computed(() => props.data.currentDoc || {});
const availableDocs = computed(() => props.data.availableDocs || []);
const activeSlug = computed(() => props.data.slug || 'disclosures');
</script>

<template>
  <AppLayout>
    <Head :title="`${currentDoc.title} — KB Finvest Regulatory & Compliance`" />

    <!-- Hero Header with Ambient Glow -->
    <section class="relative py-16 lg:py-20 border-b border-kb-line bg-gradient-to-b from-kb-surface-2/60 via-kb-surface/40 to-kb-surface overflow-hidden">
      <div class="absolute -top-32 -left-20 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
      <div class="absolute -bottom-20 right-10 w-96 h-96 bg-amber-400/5 rounded-full blur-3xl pointer-events-none"></div>

      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl space-y-4">
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-xs font-semibold uppercase tracking-wider text-amber-300">
            <SvgIcon name="shield-check" class="w-3.5 h-3.5 text-amber-400" />
            <span>Governance, DPDP & Disclosures</span>
          </div>
          <h1 class="text-3xl sm:text-5xl lg:text-6xl font-bold font-display tracking-tight text-kb-text">
            Compliance & <span class="italic text-gold-gradient font-serif">Legal Framework</span>
          </h1>
          <p class="text-base sm:text-lg text-kb-body leading-relaxed max-w-2xl">
            Transparent disclosures, statutory compliance under SEBI, IRDAI, RBI frameworks, and customer data privacy under the Digital Personal Data Protection Act, 2023.
          </p>
        </div>
      </div>
    </section>

    <!-- Main Content & Navigation Tabs -->
    <section class="py-16 border-b border-kb-line">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          <!-- Sidebar Tabs (Inertia Links) -->
          <aside class="lg:col-span-4 lg:sticky lg:top-24">
            <div class="card-luxury p-5 sm:p-6 space-y-5">
              <h2 class="text-xs font-bold uppercase tracking-wider text-kb-muted px-2">
                Legal Documents & Disclosures
              </h2>
              <nav class="space-y-1.5" aria-label="Legal document tabs">
                <Link
                  v-for="item in availableDocs"
                  :key="item.slug"
                  :href="`/legal/${item.slug}`"
                  class="flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all"
                  :class="[
                    activeSlug === item.slug
                      ? 'bg-amber-500/15 text-amber-300 font-semibold border border-amber-500/30 shadow-sm'
                      : 'text-kb-body hover:text-kb-text hover:bg-kb-surface-2/60 border border-transparent'
                  ]"
                >
                  <span>{{ item.title }}</span>
                  <SvgIcon
                    v-if="activeSlug === item.slug"
                    name="arrow-right"
                    class="w-4 h-4 text-amber-400"
                  />
                </Link>
              </nav>

              <!-- Practice Quick Details -->
              <div class="pt-5 border-t border-kb-line text-xs text-kb-muted space-y-2.5">
                <div class="flex items-center justify-between">
                  <span class="text-kb-muted">Practice:</span>
                  <span class="font-bold text-kb-text">KB Finvest (Kulwinder Singh)</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-kb-muted">ARN Number:</span>
                  <span class="font-bold text-amber-300 font-mono">ARN-286477</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-kb-muted">Jurisdiction:</span>
                  <span class="font-medium text-kb-text">Ludhiana, Punjab (India)</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-kb-muted">Operating Mode:</span>
                  <span class="font-medium text-kb-text">MFD & Facilitator</span>
                </div>
              </div>
            </div>
          </aside>

          <!-- Document Body -->
          <main class="lg:col-span-8">
            <!-- Mobile Horizontal Document Tabs -->
            <div class="lg:hidden flex items-center gap-2 overflow-x-auto no-scrollbar pb-3 mb-6 border-b border-kb-line">
              <Link
                v-for="item in availableDocs"
                :key="item.slug"
                :href="`/legal/${item.slug}`"
                class="px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap border min-h-[38px] flex items-center transition-all"
                :class="[
                  activeSlug === item.slug
                    ? 'bg-amber-500/15 text-amber-300 border-amber-500/30 font-bold shadow-sm'
                    : 'bg-kb-surface-2/60 text-kb-body border-kb-line hover:text-kb-text'
                ]"
              >
                {{ item.title }}
              </Link>
            </div>

            <article class="card-luxury p-6 sm:p-10 space-y-8">
              <div class="border-b border-kb-line pb-6">
                <span class="text-xs uppercase tracking-wider text-amber-400 font-bold">Official Document</span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold font-display text-kb-text mt-1">
                  {{ currentDoc.title }}
                </h2>
                <p class="text-xs text-kb-muted mt-2">
                  Last updated & reviewed for statutory compliance: January 2026.
                </p>
              </div>

              <!-- Sections -->
              <div class="space-y-6">
                <div
                  v-for="(sec, idx) in currentDoc.sections"
                  :key="idx"
                  class="p-5 sm:p-6 rounded-2xl border border-kb-border/40 bg-kb-surface-2/40 hover:border-kb-border/80 transition-colors space-y-3"
                >
                  <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-amber-500/15 text-amber-300 text-xs font-bold font-mono border border-amber-500/20 shrink-0">
                      {{ idx + 1 }}
                    </span>
                    <h3 class="text-base sm:text-lg font-bold font-display text-kb-text">
                      {{ sec.heading }}
                    </h3>
                  </div>
                  <p class="text-xs sm:text-base text-kb-body leading-relaxed pl-0 sm:pl-10">
                    {{ sec.content }}
                  </p>
                </div>
              </div>

              <!-- Statutory Disclaimer Banner -->
              <div class="p-5 rounded-2xl border border-amber-500/30 bg-amber-500/10 text-xs sm:text-sm text-amber-200/90 leading-relaxed flex gap-3.5 items-start">
                <SvgIcon name="shield-alert" class="w-5 h-5 flex-shrink-0 text-amber-400 mt-0.5" />
                <div>
                  <strong>Statutory Notice:</strong> Mutual fund investments are subject to market risks, read all scheme related documents carefully. KB Finvest does not provide portfolio management or stock advisory under SEBI RIA regulations. All financial figures shown on this site are indicative illustrations.
                </div>
              </div>

              <!-- Quick Action Help -->
              <div class="pt-6 border-t border-kb-line flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                <div>
                  <p class="text-sm font-semibold text-kb-text">Questions about our policies or disclosures?</p>
                  <p class="text-xs text-kb-muted">Speak directly with Kulwinder Singh or book a confidential review.</p>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
                  <Link
                    href="/contact"
                    class="w-full sm:w-auto min-h-[44px] flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-semibold text-kb-text border border-kb-line hover:bg-kb-surface-2 transition-colors text-center"
                  >
                    Contact Grievance Desk
                  </Link>
                  <Link
                    href="/book"
                    class="btn-shimmer w-full sm:w-auto min-h-[44px] flex items-center justify-center px-5 py-2.5 rounded-xl text-xs font-bold bg-gold-gradient text-slate-950 hover:brightness-110 transition-all shadow-md text-center"
                  >
                    Book Consultation
                  </Link>
                </div>
              </div>
            </article>
          </main>
        </div>
      </div>
    </section>
  </AppLayout>
</template>

