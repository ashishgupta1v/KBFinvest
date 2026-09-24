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
    default: 'lap',
  },
});

const activeTab = ref(props.initialTab);
const services = computed(() => props.data.services || []);
const currentService = computed(() => services.value.find((s) => s.id === activeTab.value) || services.value[0]);
</script>

<template>
  <AppLayout>
    <Head title="Loans & Business Finance — CGTMSE, LAP, Home & MSME in Ludhiana" />

    <!-- Hero Header -->
    <section class="py-12 border-b border-kb-line bg-kb-surface/30">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl space-y-3">
          <div class="inline-block px-3 py-1 rounded-full bg-kb-surface-2 text-xs font-semibold uppercase tracking-wider text-kb-accent border border-kb-border/40">
            Credit & Lending Facilitation
          </div>
          <h1 class="text-3xl sm:text-5xl font-bold font-display">
            Funding that fits the <span class="italic text-gold-gradient font-serif">plan</span>, not the pitch
          </h1>
          <p class="text-base text-kb-body leading-relaxed">
            Home, property, business, working capital, loans against securities, and collateral-free MSE finance. We prepare the file properly so the lender says yes the first time — and we tell you when borrowing is the wrong answer.
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
        <!-- Lead Banner -->
        <div class="p-5 sm:p-8 rounded-2xl card-luxury space-y-4">
          <div class="flex flex-wrap items-center justify-between gap-4">
            <span class="px-3 py-1 rounded-full bg-kb-surface-3 text-xs font-bold text-kb-accent border border-kb-border/30 uppercase tracking-wider">
              {{ currentService.tag }}
            </span>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center w-full sm:w-auto gap-2.5">
              <Link
                href="/calculators?calc=emi"
                class="w-full sm:w-auto min-h-[44px] justify-center px-4 py-2.5 rounded-xl font-semibold text-xs border border-kb-border text-kb-accent hover:bg-kb-surface-2 transition flex items-center gap-1.5"
              >
                <SvgIcon name="i-calc" className="w-4 h-4" />
                <span>Calculate EMI</span>
              </Link>
              <Link
                :href="`/book?topic=${encodeURIComponent(currentService.title)}`"
                class="btn-shimmer w-full sm:w-auto min-h-[44px] justify-center px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider bg-gold-gradient text-black hover:opacity-95 transition flex items-center gap-2 active:scale-95 shadow-lg shadow-amber-500/15"
              >
                <SvgIcon name="i-cal" className="w-4 h-4" />
                <span>Check Eligibility</span>
              </Link>
            </div>
          </div>
          <p class="text-lg text-kb-text font-medium leading-relaxed">
            {{ currentService.lead }}
          </p>
        </div>

        <!-- Two Columns: Who & What -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
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

          <div class="card-luxury p-6 space-y-4">
            <h3 class="text-base font-bold text-kb-text flex items-center gap-2">
              <SvgIcon name="i-bank" className="w-4 h-4 text-kb-accent" />
              <span>Lending Guidelines & Structure</span>
            </h3>
            <ul class="space-y-2.5 text-xs text-kb-body">
              <li v-for="(item, idx) in currentService.what" :key="idx" class="flex items-start gap-2.5">
                <span class="w-1.5 h-1.5 rounded-full bg-kb-accent shrink-0 mt-1.5"></span>
                <span>{{ item }}</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Regulatory Table (e.g. CGTMSE fee slabs or RBI securities ceilings) -->
        <div v-if="currentService.table" class="card-luxury p-6 space-y-4">
          <h3 class="text-sm font-bold uppercase tracking-wider text-kb-accent">
            {{ currentService.table.cap }}
          </h3>
          <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
              <thead>
                <tr class="border-b border-kb-line text-kb-muted uppercase tracking-wider text-[11px]">
                  <th v-for="(h, idx) in currentService.table.head" :key="idx" class="py-2.5 px-3">
                    {{ h }}
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-kb-line/40">
                <tr v-for="(row, idx) in currentService.table.rows" :key="idx" class="hover:bg-kb-surface-2/40">
                  <td v-for="(cell, cIdx) in row" :key="cIdx" class="py-2.5 px-3 text-kb-body font-medium">
                    {{ cell }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Facts, Docs, Steps -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="card-frame p-6 space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-kb-muted">Loan Facts</h3>
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

          <div class="card-frame p-6 space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-kb-muted">Required Documentation</h3>
            <ul class="space-y-2 text-xs text-kb-body">
              <li v-for="(doc, idx) in currentService.docs" :key="idx" class="flex items-start gap-2">
                <SvgIcon name="i-doc" className="w-3.5 h-3.5 text-kb-accent shrink-0 mt-0.5" />
                <span>{{ doc }}</span>
              </li>
            </ul>
          </div>

          <div class="card-frame p-6 space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-kb-muted">Process to Sanction</h3>
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

        <!-- Note & Disclaimers -->
        <div class="p-4 rounded-xl bg-kb-surface-2 border border-kb-border/50 text-xs leading-relaxed space-y-2">
          <div class="flex items-start gap-2 text-kb-text font-medium">
            <SvgIcon name="i-alert" className="w-4 h-4 text-amber-400 shrink-0 mt-0.5" />
            <span>{{ currentService.note }}</span>
          </div>
          <div class="text-[11px] text-kb-muted pl-6">
            {{ currentService.disc }}
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
