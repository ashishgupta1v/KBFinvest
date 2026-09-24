<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SvgIcon from '@/Components/SvgIcon.vue';

const props = defineProps({
  initialService: {
    type: String,
    default: 'mutual_funds',
  },
});

const form = useForm({
  name: '',
  phone: '',
  email: '',
  city: 'Ludhiana',
  service_type: props.initialService,
  details: '',
  consent_given: true,
  website_url: '',
});

const submitted = ref(false);

function submitInquiry() {
  form.post('/contact', {
    onSuccess: () => {
      submitted.value = true;
    },
  });
}
</script>

<template>
  <AppLayout>
    <Head title="Contact Us — Office Location & Enquiries in Ludhiana" />

    <!-- Hero Header -->
    <section class="py-12 border-b border-kb-line bg-kb-surface/30">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl space-y-3">
          <div class="inline-block px-3 py-1 rounded-full bg-kb-surface-2 text-xs font-semibold uppercase tracking-wider text-kb-accent border border-kb-border/40">
            Get in Touch
          </div>
          <h1 class="text-3xl sm:text-5xl font-bold font-display">
            Tell us what you need — <span class="italic text-gold-gradient font-serif">we will call back</span>
          </h1>
          <p class="text-base text-kb-body leading-relaxed">
            Choose the area you want help with. Everything you send is used only to answer your specific financial enquiry.
          </p>
        </div>
      </div>
    </section>

    <!-- Content Workspace -->
    <section class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
          <!-- Left: Contact / Lead Form -->
          <div class="lg:col-span-7 card-luxury p-6 sm:p-8 space-y-6">
            <div v-if="submitted" class="p-8 rounded-2xl bg-emerald-950/40 border border-emerald-500/40 text-center space-y-4 backdrop-blur-md">
              <div class="w-16 h-16 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center mx-auto shadow-lg shadow-emerald-900/30">
                <SvgIcon name="i-check" className="w-8 h-8" />
              </div>
              <h3 class="text-2xl font-bold text-emerald-300 font-display">Enquiry Submitted!</h3>
              <p class="text-xs text-emerald-200/90 max-w-md mx-auto leading-relaxed">
                Thank you, <strong>{{ form.name }}</strong>. Kulwinder Singh will review your query and respond via phone or WhatsApp.
              </p>
              <div class="pt-2">
                <a
                  :href="`https://wa.me/917973461669?text=${encodeURIComponent('Hello Kulwinder, I just submitted an inquiry regarding ' + form.service_type)}`"
                  target="_blank"
                  class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold active:scale-95 transition shadow-lg shadow-emerald-950/40"
                >
                  <SvgIcon name="i-wa" className="w-4 h-4" />
                  <span>Send Follow-up on WhatsApp</span>
                </a>
              </div>
            </div>

            <form v-else @submit.prevent="submitInquiry" class="space-y-5">
              <!-- Anti-bot honeypot -->
              <input
                type="text"
                name="website_url"
                v-model="form.website_url"
                class="hidden opacity-0 absolute -left-[9999px] pointer-events-none"
                tabindex="-1"
                autocomplete="off"
              />

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                  <label class="text-xs font-medium text-kb-body">Your Full Name *</label>
                  <input
                    type="text"
                    v-model="form.name"
                    required
                    placeholder="e.g. Gurpreet Singh"
                    class="w-full bg-kb-surface-3 border border-kb-line rounded-xl px-4 py-3 text-sm text-kb-text outline-none focus:border-kb-accent focus:bg-kb-surface-2 transition"
                  />
                  <div v-if="form.errors.name" class="text-xs text-rose-400">{{ form.errors.name }}</div>
                </div>

                <div class="space-y-1.5">
                  <label class="text-xs font-medium text-kb-body">Phone / WhatsApp Number *</label>
                  <input
                    type="tel"
                    v-model="form.phone"
                    required
                    placeholder="+91 98765 43210"
                    class="w-full bg-kb-surface-3 border border-kb-line rounded-xl px-4 py-3 text-sm text-kb-text outline-none focus:border-kb-accent focus:bg-kb-surface-2 transition"
                  />
                  <div v-if="form.errors.phone" class="text-xs text-rose-400">{{ form.errors.phone }}</div>
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                  <label class="text-xs font-medium text-kb-body">Email Address (Optional)</label>
                  <input
                    type="email"
                    v-model="form.email"
                    placeholder="name@example.com"
                    class="w-full bg-kb-surface-3 border border-kb-line rounded-xl px-4 py-3 text-sm text-kb-text outline-none focus:border-kb-accent focus:bg-kb-surface-2 transition"
                  />
                </div>

                <div class="space-y-1.5">
                  <label class="text-xs font-medium text-kb-body">City / Location *</label>
                  <input
                    type="text"
                    v-model="form.city"
                    required
                    placeholder="e.g. Ludhiana, Jalandhar, Khanna"
                    class="w-full bg-kb-surface-3 border border-kb-line rounded-xl px-4 py-3 text-sm text-kb-text outline-none focus:border-kb-accent focus:bg-kb-surface-2 transition"
                  />
                </div>
              </div>

              <!-- Primary Area with quick selection pills -->
              <div class="space-y-2">
                <label class="text-xs font-medium text-kb-body block">Primary Area of Help *</label>
                <div class="flex items-center gap-1.5 flex-wrap pb-1">
                  <button
                    v-for="qp in [
                      { k: 'mutual_funds', l: 'Mutual Funds & SIP' },
                      { k: 'health_insurance', l: 'Health Insurance' },
                      { k: 'cgtmse_loan', l: 'CGTMSE Business Loan' },
                      { k: 'loan_against_property', l: 'Loan Against Property' },
                      { k: 'pms', l: 'PMS (₹50L+)' },
                    ]"
                    :key="qp.k"
                    type="button"
                    @click="form.service_type = qp.k"
                    :class="[
                      'px-3 py-1.5 rounded-lg text-xs transition border min-h-[36px] flex items-center cursor-pointer',
                      form.service_type === qp.k
                        ? 'bg-kb-accent text-black font-bold border-kb-accent'
                        : 'bg-kb-surface-3/60 text-kb-muted border-kb-hair hover:border-kb-border hover:text-kb-text',
                    ]"
                  >
                    {{ qp.l }}
                  </button>
                </div>
                <select
                  v-model="form.service_type"
                  class="w-full min-h-[44px] bg-kb-surface-3 border border-kb-line rounded-xl px-4 py-3 text-sm text-kb-text outline-none focus:border-kb-accent focus:bg-kb-surface-2 transition"
                >
                  <option value="mutual_funds">Mutual Funds (SIP / Lump sum)</option>
                  <option value="pms">PMS Introductions (₹50 lakh+)</option>
                  <option value="health_insurance">Health Insurance Review / Portability</option>
                  <option value="life_insurance">Life & Term Cover</option>
                  <option value="motor_insurance">Motor Insurance Renewal</option>
                  <option value="cgtmse_loan">CGTMSE Collateral-Free Business Loan</option>
                  <option value="loan_against_property">Loan Against Property (LAP)</option>
                  <option value="home_loan">Home Loan</option>
                  <option value="working_capital">Working Capital (CC/OD Limit)</option>
                  <option value="stressed_loan">Stressed Account / NPA Assistance</option>
                  <option value="other">Other Financial Inquiries</option>
                </select>
              </div>

              <div class="space-y-1.5">
                <label class="text-xs font-medium text-kb-body">What would you like assistance with?</label>
                <textarea
                  v-model="form.details"
                  rows="4"
                  placeholder="Share a few details so we can prepare before calling you back..."
                  class="w-full bg-kb-surface-3 border border-kb-line rounded-xl p-4 text-sm text-kb-text outline-none focus:border-kb-accent focus:bg-kb-surface-2 transition resize-none"
                ></textarea>
              </div>

              <!-- DPDP Consent Checkbox -->
              <div class="flex items-start gap-3 p-3 rounded-xl bg-kb-surface-3/50 border border-kb-line text-xs text-kb-muted">
                <input
                  type="checkbox"
                  id="consent"
                  v-model="form.consent_given"
                  required
                  class="mt-0.5 accent-amber-400 shrink-0 cursor-pointer"
                />
                <label for="consent" class="cursor-pointer leading-relaxed">
                  I consent to KB Finvest contacting me regarding this specific enquiry. I understand my data is protected under the DPDP framework and will never be shared for third-party marketing.
                </label>
              </div>

              <button
                type="submit"
                :disabled="form.processing"
                class="w-full min-h-[48px] py-4 rounded-xl font-bold text-sm uppercase tracking-wider bg-gold-gradient text-black hover:opacity-95 transition shadow-lg shadow-amber-500/15 disabled:opacity-50 flex items-center justify-center gap-2 active:scale-95 btn-shimmer cursor-pointer"
              >
                <span>{{ form.processing ? 'Submitting...' : 'Send Confidential Enquiry' }}</span>
                <SvgIcon name="i-arr" className="w-4 h-4" />
              </button>
            </form>
          </div>

          <!-- Right: Location, Map & Grievance Desk -->
          <div class="lg:col-span-5 space-y-6">
            <!-- Office Card -->
            <div class="card-luxury p-6 sm:p-8 space-y-4">
              <h3 class="text-base font-bold text-kb-text font-display flex items-center gap-2">
                <SvgIcon name="i-pin" className="w-5 h-5 text-kb-accent" />
                <span>Visit Our Office</span>
              </h3>
              <p class="text-xs text-kb-body leading-relaxed">
                179-A, St. No. 1, Central Town, Near Hotel Keys, Ludhiana, Punjab.
              </p>
              <div class="text-xs text-kb-muted space-y-1 pt-1">
                <div><strong>Hours:</strong> Mon–Fri 9:30 am – 6:30 pm</div>
                <div>Sat 9:30 am – 5:30 pm · Sunday Closed</div>
              </div>
              <div class="pt-2">
                <a
                  href="https://www.google.com/maps/search/?api=1&query=KB+Finvest+Central+Town+Ludhiana"
                  target="_blank"
                  rel="noopener"
                  class="text-xs font-bold text-kb-accent hover:underline flex items-center gap-1.5"
                >
                  <span>Open location in Google Maps</span>
                  <SvgIcon name="i-arr" className="w-3.5 h-3.5" />
                </a>
              </div>
            </div>

            <!-- Direct Contact -->
            <div class="card-frame p-6 sm:p-8 space-y-4">
              <h3 class="text-base font-bold text-kb-text font-display">Direct Reach</h3>
              <div class="space-y-3">
                <a
                  href="tel:+917973461669"
                  class="p-3 min-h-[44px] rounded-xl bg-kb-surface-2 hover:bg-kb-surface-3 transition flex items-center gap-3 text-xs text-kb-text"
                >
                  <SvgIcon name="i-phone" className="w-4 h-4 text-kb-accent" />
                  <span>+91 79734 61669</span>
                </a>
                <a
                  href="https://wa.me/917973461669"
                  target="_blank"
                  class="p-3 min-h-[44px] rounded-xl bg-emerald-950/20 hover:bg-emerald-950/40 border border-emerald-500/30 transition flex items-center gap-3 text-xs text-emerald-300 font-semibold"
                >
                  <SvgIcon name="i-wa" className="w-4 h-4 text-emerald-400" />
                  <span>WhatsApp: 917973461669</span>
                </a>
                <a
                  href="mailto:ks_sandhu07@yahoo.co.in"
                  class="p-3 rounded-xl bg-kb-surface-2 hover:bg-kb-surface-3 transition flex items-center gap-3 text-xs text-kb-muted hover:text-kb-text"
                >
                  <SvgIcon name="i-mail" className="w-4 h-4 text-kb-accent" />
                  <span>ks_sandhu07@yahoo.co.in</span>
                </a>
                <a
                  href="https://www.instagram.com/kb_finvest_"
                  target="_blank"
                  rel="noopener"
                  class="p-3 min-h-[44px] rounded-xl bg-pink-950/20 hover:bg-pink-950/40 border border-pink-500/30 transition flex items-center gap-3 text-xs text-pink-300 font-semibold"
                >
                  <SvgIcon name="instagram" className="w-4 h-4 text-pink-400" />
                  <span>Instagram: @kb_finvest_</span>
                </a>
              </div>
            </div>

            <!-- Grievance Desk -->
            <div class="card-luxury p-6 sm:p-8 space-y-3">
              <h3 class="text-base font-bold text-kb-text font-display">Grievances & Escalations</h3>
              <p class="text-xs text-kb-muted leading-relaxed">
                If you have an unresolved issue with any mutual fund folio, insurance claim, or loan file, contact Kulwinder Singh directly or consult our regulatory grievance channels.
              </p>
              <div class="pt-1">
                <a
                  href="/legal/grievance"
                  class="text-xs font-bold text-kb-accent hover:underline flex items-center gap-1"
                >
                  <span>Grievance redressal procedure →</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
