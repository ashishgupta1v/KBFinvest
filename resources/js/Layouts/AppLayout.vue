<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import SvgIcon from '@/Components/SvgIcon.vue';
import SearchModal from '@/Components/SearchModal.vue';
import KbLogo from '@/Components/KbLogo.vue';

const page = usePage();
const site = computed(() => page.props.site || {});
const navigation = computed(() => page.props.navigation || []);
const flash = computed(() => page.props.flash || {});

const showSearch = ref(false);
const mobileMenuOpen = ref(false);

// Dynamic office open/closed status for Ludhiana (Asia/Kolkata timezone)
const officeStatus = ref({ open: false, text: 'Checking hours...' });

function calculateOfficeState() {
  const now = new Date();
  const ist = new Date(now.toLocaleString('en-US', { timeZone: 'Asia/Kolkata' }));
  const day = ist.getDay();
  const mins = ist.getHours() * 60 + ist.getMinutes();
  const hours = site.value.hours || { 1: [570, 1110], 2: [570, 1110], 3: [570, 1110], 4: [570, 1110], 5: [570, 1110], 6: [570, 1050], 0: null };

  const todayHours = hours[day];
  if (todayHours && mins >= todayHours[0] && mins < todayHours[1]) {
    const left = todayHours[1] - mins;
    officeStatus.value = {
      open: true,
      text: left <= 45 ? `Open — closes in ${left} min` : `Open now · until ${Math.floor(todayHours[1] / 60)}:${String(todayHours[1] % 60).padStart(2, '0')}`,
    };
    return;
  }

  for (let i = 0; i <= 7; i++) {
    const d = (day + i) % 7;
    const nextH = hours[d];
    if (!nextH) continue;
    if (i === 0 && mins >= nextH[1]) continue;
    const when = i === 0 ? 'today' : (i === 1 ? 'tomorrow' : ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][d]);
    officeStatus.value = {
      open: false,
      text: `Closed · opens ${when} at ${Math.floor(nextH[0] / 60)}:${String(nextH[0] % 60).padStart(2, '0')}`,
    };
    return;
  }

  officeStatus.value = { open: false, text: 'Closed' };
}

function handleGlobalKey(e) {
  if (e.key === '/' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
    e.preventDefault();
    showSearch.value = true;
  }
}

const isScrolled = ref(false);

function handleScroll() {
  isScrolled.value = window.scrollY > 20;
}

onMounted(() => {
  try {
    localStorage.removeItem('kb_theme');
    document.documentElement.setAttribute('data-theme', 'dark');
  } catch (e) {}

  calculateOfficeState();
  const timer = setInterval(calculateOfficeState, 60000);
  window.addEventListener('keydown', handleGlobalKey);
  window.addEventListener('scroll', handleScroll, { passive: true });

  unmountHandler = () => {
    clearInterval(timer);
    window.removeEventListener('keydown', handleGlobalKey);
    window.removeEventListener('scroll', handleScroll);
  };
});

let unmountHandler = null;
onUnmounted(() => {
  if (unmountHandler) unmountHandler();
});

const activeDropdown = ref(null);
let dropdownTimeout = null;

function openDropdown(name) {
  if (dropdownTimeout) clearTimeout(dropdownTimeout);
  activeDropdown.value = name;
}

function scheduleCloseDropdown() {
  dropdownTimeout = setTimeout(() => {
    activeDropdown.value = null;
  }, 180);
}

function closeDropdownNow() {
  if (dropdownTimeout) clearTimeout(dropdownTimeout);
  activeDropdown.value = null;
}

const currentPath = computed(() => {
  if (page.url) return page.url;
  return typeof window !== 'undefined' ? window.location.pathname : '/';
});

function isPathActive(paths) {
  const p = currentPath.value;
  return paths.some((path) => (path === '/' ? p === '/' : p.startsWith(path)));
}

function isCurrent(url) {
  const p = currentPath.value;
  if (url === '/') return p === '/';
  return p.startsWith(url);
}

function scrollToTop() {
  if (typeof window !== 'undefined') {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
}
</script>

<template>
  <div class="min-h-screen flex flex-col bg-kb-bg text-kb-text font-sans selection:bg-kb-accent selection:text-black">
    <!-- Accessible Skip to Main Content Link -->
    <a
      href="#main-content"
      class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:px-4 focus:py-2.5 focus:bg-kb-accent focus:text-black focus:font-bold focus:rounded-xl focus:shadow-2xl focus:ring-2 focus:ring-white transition"
    >
      Skip to main content
    </a>

    <!-- Top Announcement & Office Status Bar -->
    <div class="bg-kb-surface-2/80 border-b border-kb-line text-xs py-1.5 px-3 sm:px-4 backdrop-blur-md">
      <div class="max-w-7xl mx-auto flex items-center justify-between gap-2 sm:gap-4">
        <div class="flex items-center gap-1.5 sm:gap-2 truncate">
          <span
            :class="[
              'w-2 h-2 rounded-full inline-block animate-pulse shrink-0',
              officeStatus.open ? 'bg-emerald-400 shadow-[0_0_8px_#34D399]' : 'bg-amber-400 shadow-[0_0_8px_#FBBF24]',
            ]"
          ></span>
          <span class="text-kb-body font-medium truncate text-[11px] sm:text-xs">{{ officeStatus.text }}</span>
          <span class="hidden md:inline text-kb-muted">· Central Town, Ludhiana</span>
        </div>
        <div class="flex items-center gap-2.5 sm:gap-4 text-kb-muted shrink-0 text-xs">
          <a :href="`tel:${site.phone}`" class="hover:text-kb-accent transition flex items-center gap-1" title="Call advisor">
            <SvgIcon name="i-phone" className="w-3.5 h-3.5 text-kb-accent shrink-0" />
            <span class="hidden sm:inline font-mono">{{ site.phoneDisplay }}</span>
          </a>
          <a
            :href="`https://wa.me/${site.wa}?text=Hello%20KB%20Finvest,%20I%20would%20like%20to%20consult%20you.`"
            target="_blank"
            rel="noopener"
            class="hover:text-emerald-400 transition flex items-center gap-1 text-emerald-500 font-medium"
            title="Chat on WhatsApp"
          >
            <SvgIcon name="i-wa" className="w-3.5 h-3.5 shrink-0" />
            <span class="text-[11px] sm:text-xs">WhatsApp</span>
          </a>
          <a
            href="https://www.instagram.com/kb_finvest_"
            target="_blank"
            rel="noopener"
            class="hover:text-pink-400 transition flex items-center gap-1 text-pink-400/90 font-medium"
            title="Follow @kb_finvest_ on Instagram"
          >
            <SvgIcon name="instagram" className="w-3.5 h-3.5 text-pink-400 shrink-0" />
            <span class="hidden sm:inline text-[11px] sm:text-xs">Instagram</span>
          </a>
        </div>
      </div>
    </div>

    <header
      :class="[
        'sticky top-0 z-40 transition-all duration-300 border-b border-kb-line backdrop-blur-xl',
        isScrolled ? 'h-16 bg-kb-bg/95 shadow-xl shadow-black/25' : 'h-18 sm:h-20 bg-kb-bg/90'
      ]"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center justify-between gap-4">
        <!-- Full Authentic KB Finvest Brand Logo -->
        <Link href="/" class="flex items-center shrink-0 group py-1" @click="closeDropdownNow">
          <KbLogo
            :is-scrolled="isScrolled"
            :class="[
              'w-auto transition-all duration-300',
              isScrolled ? 'h-9 sm:h-9.5' : 'h-11 sm:h-12'
            ]"
          />
        </Link>

        <!-- Desktop Navigation Items (Curated Mega-Menu Hierarchy) -->
        <nav class="hidden lg:flex items-center gap-1 text-sm font-medium">
          <!-- Home -->
          <Link
            href="/"
            :class="[
              'px-3 py-2 rounded-lg transition-all',
              isCurrent('/') && currentPath === '/'
                ? 'text-kb-accent font-semibold bg-kb-surface-2/60 shadow-sm'
                : 'text-kb-body hover:text-kb-text hover:bg-kb-surface/40',
            ]"
            @click="closeDropdownNow"
          >
            Home
          </Link>

          <!-- 1. Services Mega Dropdown -->
          <div
            class="relative"
            @mouseenter="openDropdown('services')"
            @mouseleave="scheduleCloseDropdown"
          >
            <button
              type="button"
              :class="[
                'px-3.5 py-2 rounded-lg transition-all flex items-center gap-1.5',
                isPathActive(['/investments', '/insurance', '/loans', '/advisory']) || activeDropdown === 'services'
                  ? 'text-kb-accent font-semibold bg-kb-surface-2/60'
                  : 'text-kb-body hover:text-kb-text hover:bg-kb-surface/40',
              ]"
              @click="activeDropdown = activeDropdown === 'services' ? null : 'services'"
            >
              <span>Services</span>
              <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180 text-kb-accent': activeDropdown === 'services' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Dropdown Menu -->
            <div
              v-show="activeDropdown === 'services'"
              class="absolute left-1/2 -translate-x-1/2 top-full pt-2 w-[620px] animate-in fade-in slide-in-from-top-2 duration-150 z-50"
              @mouseenter="openDropdown('services')"
              @mouseleave="scheduleCloseDropdown"
            >
              <div class="bg-kb-surface/98 backdrop-blur-2xl border border-kb-border/50 rounded-2xl p-5 shadow-2xl ring-1 ring-black/40 grid grid-cols-2 gap-4">
                <!-- Wealth & Investments -->
                <div class="space-y-2">
                  <div class="text-[11px] font-bold uppercase tracking-wider text-kb-accent px-2.5 pb-1 border-b border-kb-line/60 flex items-center gap-1.5">
                    <SvgIcon name="i-growth" className="w-3.5 h-3.5" />
                    <span>Investments & Wealth</span>
                  </div>
                  <Link
                    href="/investments?tab=mf"
                    class="group block p-2.5 rounded-xl hover:bg-kb-surface-2 transition"
                    @click="closeDropdownNow"
                  >
                    <div class="font-semibold text-xs text-kb-text group-hover:text-kb-accent flex items-center justify-between">
                      <span>Mutual Funds & SIPs</span>
                      <SvgIcon name="i-arr" className="w-3 h-3 opacity-0 group-hover:opacity-100 transition text-kb-accent" />
                    </div>
                    <p class="text-[11px] text-kb-muted mt-0.5 leading-snug">Goal-linked portfolios, ELSS tax savings & regular reviews</p>
                  </Link>

                  <Link
                    href="/investments?tab=pms"
                    class="group block p-2.5 rounded-xl hover:bg-kb-surface-2 transition"
                    @click="closeDropdownNow"
                  >
                    <div class="font-semibold text-xs text-kb-text group-hover:text-kb-accent flex items-center justify-between">
                      <span>PMS Introductions</span>
                      <span class="text-[9px] px-1.5 py-0.2 rounded bg-amber-500/10 text-kb-accent border border-amber-500/20">₹50L+</span>
                    </div>
                    <p class="text-[11px] text-kb-muted mt-0.5 leading-snug">SEBI-registered portfolio managers for high net-worth individuals</p>
                  </Link>

                  <Link
                    href="/investments?tab=mkt"
                    class="group block p-2.5 rounded-xl hover:bg-kb-surface-2 transition"
                    @click="closeDropdownNow"
                  >
                    <div class="font-semibold text-xs text-kb-text group-hover:text-kb-accent flex items-center justify-between">
                      <span>Shares, ETFs & Bonds</span>
                      <SvgIcon name="i-arr" className="w-3 h-3 opacity-0 group-hover:opacity-100 transition text-kb-accent" />
                    </div>
                    <p class="text-[11px] text-kb-muted mt-0.5 leading-snug">Equities, index funds, sovereign gold & debt securities</p>
                  </Link>
                </div>

                <!-- Protection & Financing -->
                <div class="space-y-2">
                  <div class="text-[11px] font-bold uppercase tracking-wider text-kb-accent px-2.5 pb-1 border-b border-kb-line/60 flex items-center gap-1.5">
                    <SvgIcon name="i-shield" className="w-3.5 h-3.5" />
                    <span>Protection & Credit</span>
                  </div>
                  <Link
                    href="/insurance"
                    class="group block p-2.5 rounded-xl hover:bg-kb-surface-2 transition"
                    @click="closeDropdownNow"
                  >
                    <div class="font-semibold text-xs text-kb-text group-hover:text-kb-accent flex items-center justify-between">
                      <span>Insurance (Health & Life)</span>
                      <SvgIcon name="i-arr" className="w-3 h-3 opacity-0 group-hover:opacity-100 transition text-kb-accent" />
                    </div>
                    <p class="text-[11px] text-kb-muted mt-0.5 leading-snug">Mediclaim with restoration, pure term cover & claim guidance</p>
                  </Link>

                  <Link
                    href="/loans"
                    class="group block p-2.5 rounded-xl hover:bg-kb-surface-2 transition"
                    @click="closeDropdownNow"
                  >
                    <div class="font-semibold text-xs text-kb-text group-hover:text-kb-accent flex items-center justify-between">
                      <span>Loans & CGTMSE Finance</span>
                      <span class="text-[9px] px-1.5 py-0.2 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Collateral-Free</span>
                    </div>
                    <p class="text-[11px] text-kb-muted mt-0.5 leading-snug">Home loans, LAP, and collateral-free MSME credit up to ₹10 Cr</p>
                  </Link>

                  <Link
                    href="/advisory"
                    class="group block p-2.5 rounded-xl hover:bg-kb-surface-2 transition"
                    @click="closeDropdownNow"
                  >
                    <div class="font-semibold text-xs text-kb-text group-hover:text-kb-accent flex items-center justify-between">
                      <span>Specialist Advisory</span>
                      <SvgIcon name="i-arr" className="w-3 h-3 opacity-0 group-hover:opacity-100 transition text-kb-accent" />
                    </div>
                    <p class="text-[11px] text-kb-muted mt-0.5 leading-snug">Unlisted equities & confidential NPA / debt restructuring</p>
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <!-- 2. Tools & Calculators Mega Dropdown -->
          <div
            class="relative"
            @mouseenter="openDropdown('tools')"
            @mouseleave="scheduleCloseDropdown"
          >
            <button
              type="button"
              :class="[
                'px-3.5 py-2 rounded-lg transition-all flex items-center gap-1.5',
                isPathActive(['/calculators', '/resources']) || activeDropdown === 'tools'
                  ? 'text-kb-accent font-semibold bg-kb-surface-2/60'
                  : 'text-kb-body hover:text-kb-text hover:bg-kb-surface/40',
              ]"
              @click="activeDropdown = activeDropdown === 'tools' ? null : 'tools'"
            >
              <span>Calculators & Tools</span>
              <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180 text-kb-accent': activeDropdown === 'tools' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Dropdown Menu -->
            <div
              v-show="activeDropdown === 'tools'"
              class="absolute left-1/2 -translate-x-1/2 top-full pt-2 w-[520px] animate-in fade-in slide-in-from-top-2 duration-150 z-50"
              @mouseenter="openDropdown('tools')"
              @mouseleave="scheduleCloseDropdown"
            >
              <div class="bg-kb-surface/98 backdrop-blur-2xl border border-kb-border/50 rounded-2xl p-5 shadow-2xl ring-1 ring-black/40 grid grid-cols-2 gap-4">
                <div class="space-y-2">
                  <div class="text-[11px] font-bold uppercase tracking-wider text-kb-accent px-2.5 pb-1 border-b border-kb-line/60 flex items-center gap-1.5">
                    <SvgIcon name="i-calc" className="w-3.5 h-3.5" />
                    <span>14 Financial Tools</span>
                  </div>
                  <Link
                    href="/calculators?calc=sip"
                    class="group block p-2 rounded-xl hover:bg-kb-surface-2 transition"
                    @click="closeDropdownNow"
                  >
                    <div class="font-semibold text-xs text-kb-text group-hover:text-kb-accent">SIP & Step-Up Calculator</div>
                    <p class="text-[11px] text-kb-muted">Model monthly wealth compounding</p>
                  </Link>
                  <Link
                    href="/calculators?calc=emi"
                    class="group block p-2 rounded-xl hover:bg-kb-surface-2 transition"
                    @click="closeDropdownNow"
                  >
                    <div class="font-semibold text-xs text-kb-text group-hover:text-kb-accent">Loan EMI & Prepayment</div>
                    <p class="text-[11px] text-kb-muted">Monthly payout & interest savings</p>
                  </Link>
                  <Link
                    href="/calculators?calc=hlv"
                    class="group block p-2 rounded-xl hover:bg-kb-surface-2 transition"
                    @click="closeDropdownNow"
                  >
                    <div class="font-semibold text-xs text-kb-text group-hover:text-kb-accent">Human Life Value (HLV)</div>
                    <p class="text-[11px] text-kb-muted">Exact term insurance cover needed</p>
                  </Link>
                </div>

                <div class="space-y-2">
                  <div class="text-[11px] font-bold uppercase tracking-wider text-kb-accent px-2.5 pb-1 border-b border-kb-line/60 flex items-center gap-1.5">
                    <SvgIcon name="i-doc" className="w-3.5 h-3.5" />
                    <span>Knowledge & Insights</span>
                  </div>
                  <Link
                    href="/calculators"
                    class="group block p-2 rounded-xl hover:bg-kb-surface-2 transition"
                    @click="closeDropdownNow"
                  >
                    <div class="font-semibold text-xs text-kb-text group-hover:text-kb-accent flex items-center justify-between">
                      <span>All 14 Calculators</span>
                      <span class="text-[10px] text-kb-accent">Suite →</span>
                    </div>
                    <p class="text-[11px] text-kb-muted">SWP, Goal, Retirement & CGTMSE</p>
                  </Link>
                  <Link
                    href="/resources"
                    class="group block p-2 rounded-xl hover:bg-kb-surface-2 transition"
                    @click="closeDropdownNow"
                  >
                    <div class="font-semibold text-xs text-kb-text group-hover:text-kb-accent">Investor Resources</div>
                    <p class="text-[11px] text-kb-muted">SEBI regulations, tax norms & FAQs</p>
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <!-- 3. Company Dropdown -->
          <div
            class="relative"
            @mouseenter="openDropdown('company')"
            @mouseleave="scheduleCloseDropdown"
          >
            <button
              type="button"
              :class="[
                'px-3.5 py-2 rounded-lg transition-all flex items-center gap-1.5',
                isPathActive(['/about', '/legal']) || activeDropdown === 'company'
                  ? 'text-kb-accent font-semibold bg-kb-surface-2/60'
                  : 'text-kb-body hover:text-kb-text hover:bg-kb-surface/40',
              ]"
              @click="activeDropdown = activeDropdown === 'company' ? null : 'company'"
            >
              <span>Company</span>
              <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180 text-kb-accent': activeDropdown === 'company' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Dropdown Menu -->
            <div
              v-show="activeDropdown === 'company'"
              class="absolute left-1/2 -translate-x-1/2 top-full pt-2 w-72 animate-in fade-in slide-in-from-top-2 duration-150 z-50"
              @mouseenter="openDropdown('company')"
              @mouseleave="scheduleCloseDropdown"
            >
              <div class="bg-kb-surface/98 backdrop-blur-2xl border border-kb-border/50 rounded-2xl p-4 shadow-2xl ring-1 ring-black/40 space-y-1.5">
                <Link
                  href="/about"
                  class="group block p-2.5 rounded-xl hover:bg-kb-surface-2 transition"
                  @click="closeDropdownNow"
                >
                  <div class="font-semibold text-xs text-kb-text group-hover:text-kb-accent">About Kulwinder Singh</div>
                  <p class="text-[11px] text-kb-muted mt-0.5">AMFI registration & Ludhiana heritage</p>
                </Link>
                <Link
                  href="/legal/disclosures"
                  class="group block p-2.5 rounded-xl hover:bg-kb-surface-2 transition"
                  @click="closeDropdownNow"
                >
                  <div class="font-semibold text-xs text-kb-text group-hover:text-kb-accent">Disclosures & Conduct</div>
                  <p class="text-[11px] text-kb-muted mt-0.5">Regulatory filings, commissions & charter</p>
                </Link>
              </div>
            </div>
          </div>

          <!-- Contact (Direct Link) -->
          <Link
            href="/contact"
            :class="[
              'px-3.5 py-2 rounded-lg transition-all',
              isCurrent('/contact')
                ? 'text-kb-accent font-semibold bg-kb-surface-2/60 shadow-sm'
                : 'text-kb-body hover:text-kb-text hover:bg-kb-surface/40',
            ]"
            @click="closeDropdownNow"
          >
            Contact
          </Link>
        </nav>

        <!-- Header Controls -->
        <div class="flex items-center gap-2 sm:gap-3">
          <!-- Quick Search Button -->
          <button
            @click="showSearch = true"
            class="p-2.5 rounded-lg border border-kb-line hover:border-kb-border hover:bg-kb-surface transition text-kb-muted hover:text-kb-text flex items-center gap-1.5 cursor-pointer"
            title="Search (Press /)"
            aria-label="Search website (Press /)"
          >
            <SvgIcon name="i-search" className="w-4 h-4" />
            <span class="hidden md:inline-flex text-[10px] font-mono px-1.5 py-0.5 rounded bg-kb-surface-2 border border-kb-line text-kb-muted">/</span>
          </button>


          <!-- Book Consultation CTA (With nowrap and shrink-0 to prevent awkward wrapping) -->
          <Link
            href="/book"
            class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider bg-gold-gradient text-black hover:opacity-95 transition shadow-lg shadow-amber-500/10 active:scale-95 btn-shimmer whitespace-nowrap shrink-0"
          >
            <SvgIcon name="i-cal" className="w-4 h-4 shrink-0" />
            <span>Book a call</span>
          </Link>

          <!-- Mobile Menu Trigger -->
          <button
            @click="mobileMenuOpen = true"
            class="lg:hidden p-2.5 rounded-lg border border-kb-line text-kb-text hover:bg-kb-surface transition"
            aria-label="Open Navigation Menu"
          >
            <SvgIcon name="i-menu" className="w-5 h-5" />
          </button>
        </div>
      </div>
      <!-- Ambient gold sheen border bottom -->
      <div class="h-[1px] w-full bg-gradient-to-r from-transparent via-kb-accent/30 to-transparent"></div>
    </header>

    <!-- Mobile Slide-over Drawer (Enhanced Categorization) -->
    <div
      v-if="mobileMenuOpen"
      class="fixed inset-0 z-50 lg:hidden flex bg-black/80 backdrop-blur-sm animate-in fade-in"
    >
      <div
        class="w-[85vw] max-w-sm ml-auto bg-kb-surface border-l border-kb-line h-full flex flex-col justify-between p-5 sm:p-6 shadow-2xl animate-in slide-in-from-right overflow-y-auto"
      >
        <div>
          <div class="flex items-center justify-between pb-6 border-b border-kb-line">
            <Link href="/" class="flex items-center" @click="mobileMenuOpen = false">
              <KbLogo class="h-10 w-auto" />
            </Link>
            <button
              @click="mobileMenuOpen = false"
              class="p-2 rounded-lg text-kb-muted hover:text-kb-text border border-kb-line cursor-pointer"
              aria-label="Close navigation menu"
            >
              <SvgIcon name="i-x" className="w-5 h-5" />
            </button>
          </div>

          <nav class="flex flex-col gap-1 py-4">
            <Link
              href="/"
              @click="mobileMenuOpen = false"
              class="px-3.5 py-2.5 rounded-xl text-sm font-semibold transition hover:bg-kb-surface-2 flex items-center justify-between text-kb-text"
            >
              <span>Home</span>
              <SvgIcon name="i-arr" className="w-4 h-4 opacity-40" />
            </Link>

            <div class="pt-3 pb-1 text-[10px] uppercase font-bold tracking-wider text-kb-accent px-3.5">
              Services
            </div>
            <Link
              href="/investments"
              @click="mobileMenuOpen = false"
              class="px-3.5 py-2 rounded-xl text-sm font-medium transition hover:bg-kb-surface-2 text-kb-body flex items-center justify-between"
            >
              <span>Investments & Mutual Funds</span>
              <SvgIcon name="i-arr" className="w-3.5 h-3.5 opacity-40" />
            </Link>
            <Link
              href="/insurance"
              @click="mobileMenuOpen = false"
              class="px-3.5 py-2 rounded-xl text-sm font-medium transition hover:bg-kb-surface-2 text-kb-body flex items-center justify-between"
            >
              <span>Health & Life Insurance</span>
              <SvgIcon name="i-arr" className="w-3.5 h-3.5 opacity-40" />
            </Link>
            <Link
              href="/loans"
              @click="mobileMenuOpen = false"
              class="px-3.5 py-2 rounded-xl text-sm font-medium transition hover:bg-kb-surface-2 text-kb-body flex items-center justify-between"
            >
              <span>Loans & CGTMSE Credit</span>
              <SvgIcon name="i-arr" className="w-3.5 h-3.5 opacity-40" />
            </Link>
            <Link
              href="/advisory"
              @click="mobileMenuOpen = false"
              class="px-3.5 py-2 rounded-xl text-sm font-medium transition hover:bg-kb-surface-2 text-kb-body flex items-center justify-between"
            >
              <span>Specialist Advisory</span>
              <SvgIcon name="i-arr" className="w-3.5 h-3.5 opacity-40" />
            </Link>

            <div class="pt-3 pb-1 text-[10px] uppercase font-bold tracking-wider text-kb-accent px-3.5">
              Tools & Insights
            </div>
            <Link
              href="/calculators"
              @click="mobileMenuOpen = false"
              class="px-3.5 py-2 rounded-xl text-sm font-medium transition hover:bg-kb-surface-2 text-kb-body flex items-center justify-between"
            >
              <span>14 Financial Calculators</span>
              <span class="text-[10px] px-1.5 py-0.2 rounded bg-amber-500/10 text-kb-accent">Suite</span>
            </Link>
            <Link
              href="/resources"
              @click="mobileMenuOpen = false"
              class="px-3.5 py-2 rounded-xl text-sm font-medium transition hover:bg-kb-surface-2 text-kb-body flex items-center justify-between"
            >
              <span>Investor Resources & Guides</span>
              <SvgIcon name="i-arr" className="w-3.5 h-3.5 opacity-40" />
            </Link>

            <div class="pt-3 pb-1 text-[10px] uppercase font-bold tracking-wider text-kb-accent px-3.5">
              Company
            </div>
            <Link
              href="/about"
              @click="mobileMenuOpen = false"
              class="px-3.5 py-2 rounded-xl text-sm font-medium transition hover:bg-kb-surface-2 text-kb-body flex items-center justify-between"
            >
              <span>About Kulwinder Singh</span>
              <SvgIcon name="i-arr" className="w-3.5 h-3.5 opacity-40" />
            </Link>
            <Link
              href="/contact"
              @click="mobileMenuOpen = false"
              class="px-3.5 py-2 rounded-xl text-sm font-medium transition hover:bg-kb-surface-2 text-kb-body flex items-center justify-between"
            >
              <span>Contact & Ludhiana Office</span>
              <SvgIcon name="i-arr" className="w-3.5 h-3.5 opacity-40" />
            </Link>
          </nav>
        </div>

        <div class="pt-4 border-t border-kb-line flex flex-col gap-2.5">
          <Link
            href="/book"
            @click="mobileMenuOpen = false"
            class="w-full text-center py-3 rounded-xl font-bold text-xs uppercase tracking-wider bg-gold-gradient text-black shadow-lg shadow-amber-500/10 btn-shimmer"
          >
            Book a 30-min Consultation
          </Link>
          <a
            :href="`https://wa.me/${site.wa}`"
            target="_blank"
            class="w-full text-center py-2.5 rounded-xl border border-emerald-500/40 text-emerald-400 font-medium text-xs flex items-center justify-center gap-2 bg-emerald-950/20"
          >
            <SvgIcon name="i-wa" className="w-4 h-4" /> Message on WhatsApp
          </a>
          <a
            href="https://www.instagram.com/kb_finvest_"
            target="_blank"
            rel="noopener"
            class="w-full text-center py-2.5 rounded-xl border border-pink-500/40 text-pink-400 font-medium text-xs flex items-center justify-center gap-2 bg-pink-950/20"
          >
            <SvgIcon name="instagram" className="w-4 h-4" /> Follow @kb_finvest_ on Instagram
          </a>
        </div>
      </div>
    </div>

    <!-- Flash message toast -->
    <div
      v-if="flash.success"
      class="fixed top-20 sm:top-24 left-4 right-4 sm:left-auto sm:right-4 z-50 max-w-md bg-emerald-950/95 text-emerald-200 border border-emerald-500/40 rounded-xl p-4 shadow-xl backdrop-blur-md flex items-start gap-3 animate-in slide-in-from-top-4"
    >
      <SvgIcon name="i-check" className="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" />
      <div class="text-sm font-medium">{{ flash.success }}</div>
    </div>

    <!-- Main Page Content with bottom padding for mobile sticky action bar -->
    <main id="main-content" class="flex-1 pb-24 lg:pb-0" role="main">
      <slot />
    </main>

    <!-- Desktop Floating Speed Dial / Back-To-Top (Antigravity Spatial Elevation) -->
    <div
      v-show="isScrolled"
      class="hidden lg:flex fixed bottom-6 right-6 z-40 items-center gap-2.5 transition-all duration-300 animate-in fade-in slide-in-from-bottom-3"
    >
      <a
        :href="`https://wa.me/${site.wa}?text=Hello%20KB%20Finvest,%20I%20would%20like%20to%20consult%20you.`"
        target="_blank"
        rel="noopener"
        class="h-11 px-4 rounded-full bg-emerald-950/90 border border-emerald-500/50 text-emerald-300 font-bold text-xs flex items-center gap-2 shadow-xl shadow-emerald-950/60 hover:bg-emerald-900/90 hover:scale-105 active:scale-95 transition backdrop-blur-md"
        title="Chat on WhatsApp"
        aria-label="Direct WhatsApp consultation"
      >
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        <SvgIcon name="i-wa" className="w-4 h-4 text-emerald-400 shrink-0" />
        <span>WhatsApp</span>
      </a>

      <button
        type="button"
        @click="scrollToTop"
        class="w-11 h-11 rounded-full bg-kb-surface-2/90 border border-kb-border/60 text-kb-muted hover:text-kb-text hover:border-kb-accent flex items-center justify-center shadow-xl hover:scale-105 active:scale-95 transition backdrop-blur-md cursor-pointer"
        title="Back to top"
        aria-label="Scroll back to top of page"
      >
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
        </svg>
      </button>
    </div>

    <!-- Mobile Sticky Quick Concierge Bar with Safe Area Support -->
    <aside class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-kb-surface/95 backdrop-blur-xl border-t border-kb-border/40 px-3 sm:px-4 pt-2.5 pb-[calc(0.625rem+env(safe-area-inset-bottom,0px))] shadow-2xl flex items-center justify-between gap-2.5 sm:gap-3">
      <a
        :href="`https://wa.me/${site.wa}?text=Hello%20KB%20Finvest,%20I%20would%20like%20to%20consult%20you.`"
        target="_blank"
        rel="noopener"
        class="flex-1 flex items-center justify-center gap-1.5 py-2.5 px-2 rounded-xl bg-emerald-950/50 border border-emerald-500/40 text-emerald-400 font-semibold text-xs active:scale-95 transition min-h-[44px]"
      >
        <SvgIcon name="i-wa" className="w-4 h-4 shrink-0" />
        <span class="truncate">WhatsApp</span>
      </a>
      <a
        :href="`tel:${site.phone}`"
        class="flex-1 flex items-center justify-center gap-1.5 py-2.5 px-2 rounded-xl bg-kb-surface-2 border border-kb-line text-kb-body font-semibold text-xs active:scale-95 transition min-h-[44px]"
      >
        <SvgIcon name="i-phone" className="w-4 h-4 text-kb-accent shrink-0" />
        <span class="truncate">Call</span>
      </a>
      <Link
        href="/book"
        class="flex-1 flex items-center justify-center gap-1.5 py-2.5 px-2 rounded-xl bg-gold-gradient text-black font-bold text-xs shadow-md shadow-amber-500/20 active:scale-95 transition btn-shimmer min-h-[44px]"
      >
        <SvgIcon name="i-cal" className="w-4 h-4 shrink-0" />
        <span class="truncate">Book 30m</span>
      </Link>
    </aside>

    <!-- Site Footer -->
    <footer class="bg-kb-surface-2/60 border-t border-kb-line mt-12 sm:mt-20 pt-12 sm:pt-16 pb-12 text-sm text-kb-muted">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-kb-line/60">
          <!-- Col 1: About & Founder -->
          <div class="space-y-4 md:col-span-1">
            <Link href="/" class="inline-block">
              <KbLogo class="h-11 sm:h-12 w-auto" />
            </Link>
            <p class="text-xs text-kb-body leading-relaxed">
              Relationship-led financial guidance from <strong>Kulwinder Singh</strong>. Mutual funds, insurance, and
              business loan facilitation from an office you can walk into on Central Town, Ludhiana.
            </p>
            <div class="text-xs text-kb-muted space-y-1">
              <div>📍 179-A, St. No. 1, Central Town, Near Hotel Keys, Ludhiana, 142021</div>
              <div>📞 +91 79734 61669</div>
              <div>✉️ ks_sandhu07@yahoo.co.in</div>
              <div class="pt-1.5">
                <a
                  href="https://www.instagram.com/kb_finvest_"
                  target="_blank"
                  rel="noopener"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-pink-500/10 hover:bg-pink-500/20 border border-pink-500/30 text-pink-400 text-xs font-semibold transition"
                >
                  <SvgIcon name="instagram" className="w-3.5 h-3.5" />
                  <span>@kb_finvest_</span>
                </a>
              </div>
            </div>
          </div>

          <!-- Col 2: Services -->
          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-kb-text mb-4">Core Services</h4>
            <ul class="space-y-2.5 text-xs text-kb-body">
              <li><Link href="/investments" class="hover:text-kb-accent transition">Mutual Funds (SIP & Lump)</Link></li>
              <li><Link href="/investments?tab=pms" class="hover:text-kb-accent transition">PMS Introductions (₹50L+)</Link></li>
              <li><Link href="/insurance" class="hover:text-kb-accent transition">Health, Life & Motor Insurance</Link></li>
              <li><Link href="/loans?tab=cgtmse" class="hover:text-kb-accent transition">CGTMSE Collateral-Free Loans</Link></li>
              <li><Link href="/loans?tab=lap" class="hover:text-kb-accent transition">Loan Against Property & Home Loans</Link></li>
              <li><Link href="/advisory" class="hover:text-kb-accent transition">Specialist Advisory & NPA Support</Link></li>
            </ul>
          </div>

          <!-- Col 3: Planning Tools & Resources -->
          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-kb-text mb-4">Tools & Guides</h4>
            <ul class="space-y-2.5 text-xs text-kb-body">
              <li><Link href="/calculators?calc=sip" class="hover:text-kb-accent transition">SIP Calculator</Link></li>
              <li><Link href="/calculators?calc=emi" class="hover:text-kb-accent transition">Loan EMI Calculator</Link></li>
              <li><Link href="/calculators?calc=cgtmse" class="hover:text-kb-accent transition">CGTMSE Fee Checker</Link></li>
              <li><Link href="/resources" class="hover:text-kb-accent transition">Document Checklists</Link></li>
              <li><Link href="/resources" class="hover:text-kb-accent transition">Frequently Asked Questions</Link></li>
              <li><Link href="/resources" class="hover:text-kb-accent transition">Financial Glossary</Link></li>
            </ul>
          </div>

          <!-- Col 4: Regulatory Frameworks -->
          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-kb-text mb-4">Regulatory Bodies</h4>
            <p class="text-xs text-kb-muted mb-3">
              We operate under regulated frameworks:
            </p>
            <div class="flex flex-wrap gap-2 text-[11px]">
              <span class="px-2.5 py-1 rounded bg-kb-surface-3 border border-kb-line text-kb-body">AMFI ARN</span>
              <span class="px-2.5 py-1 rounded bg-kb-surface-3 border border-kb-line text-kb-body">SEBI Guidelines</span>
              <span class="px-2.5 py-1 rounded bg-kb-surface-3 border border-kb-line text-kb-body">IRDAI Norms</span>
              <span class="px-2.5 py-1 rounded bg-kb-surface-3 border border-kb-line text-kb-body">RBI Directives</span>
              <span class="px-2.5 py-1 rounded bg-kb-surface-3 border border-kb-line text-kb-body">CGTMSE Trust</span>
            </div>
            <div class="mt-4">
              <Link
                href="/legal/disclosures"
                class="text-xs font-medium text-kb-accent hover:underline flex items-center gap-1"
              >
                <span>Read regulatory disclosures</span>
                <SvgIcon name="i-arr" className="w-3.5 h-3.5" />
              </Link>
            </div>
          </div>
        </div>

        <!-- Statutory Disclaimers -->
        <div class="py-6 border-b border-kb-line/40 text-[11px] leading-relaxed text-kb-muted space-y-2">
          <p>
            <strong>Mutual Funds:</strong> Mutual Fund investments are subject to market risks, read all scheme related
            documents carefully. Past performance does not indicate future results.
          </p>
          <p>
            <strong>Insurance:</strong> Insurance is the subject matter of solicitation. Benefits, exclusions and
            waiting periods are governed entirely by the policy wording issued by the insurer.
          </p>
          <p>
            <strong>Loans:</strong> KB Finvest facilitates introductions to regulated lenders. Sanction, interest rate,
            charges and final terms are decided solely by the lender. We do not collect loan repayments or hold loan funds.
          </p>
        </div>

        <!-- Copyright & Legal Links -->
        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
          <div>
            © {{ new Date().getFullYear() }} KB Finvest. All rights reserved. Ludhiana, Punjab.
          </div>
          <div class="flex items-center gap-6">
            <Link href="/legal/privacy" class="hover:text-kb-text transition">Privacy Notice</Link>
            <Link href="/legal/terms" class="hover:text-kb-text transition">Terms of Use</Link>
            <Link href="/legal/disclosures" class="hover:text-kb-text transition">Disclosures</Link>
            <Link href="/legal/grievance" class="hover:text-kb-text transition">Grievance Desk</Link>
          </div>
        </div>
      </div>
    </footer>

    <!-- Search Modal Component -->
    <SearchModal :show="showSearch" @close="showSearch = false" />
  </div>
</template>
