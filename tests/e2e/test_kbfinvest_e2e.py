"""
KB Finvest - Comprehensive End-to-End Test Suite (Playwright)
Covers all critical user journeys, calculators, booking, contact, responsiveness, theme toggle, and error sentinels.
"""

import sys
import time
from playwright.sync_api import sync_playwright, expect

if sys.stdout.encoding != 'utf-8':
    try:
        sys.stdout.reconfigure(encoding='utf-8')
    except Exception:
        pass

BASE_URL = "http://127.0.0.1:8003"

def run_e2e_tests():
    print(f"\n=======================================================")
    print(f"[START] Starting KB Finvest Comprehensive E2E Testing Suite")
    print(f"[TARGET] Target URL: {BASE_URL}")
    print(f"=======================================================\n")

    passed_count = 0
    failed_count = 0
    errors = []

    def log_pass(test_name):
        nonlocal passed_count
        passed_count += 1
        print(f"  [PASS] {test_name}")

    import traceback

    def log_fail(test_name, err):
        nonlocal failed_count
        failed_count += 1
        print(f"  [FAIL] {test_name}")
        print(f"     Details: {err}")
        traceback.print_exc()
        errors.append((test_name, str(err)))

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(viewport={"width": 1440, "height": 900})
        page = context.new_page()

        # Capture console errors and failed responses
        console_errors = []
        page.on("console", lambda msg: console_errors.append(msg.text) if msg.type == "error" else None)
        page.on("pageerror", lambda exc: console_errors.append(str(exc)))

        # -----------------------------------------------------------------
        # TEST 1: Homepage, Brand Header & Theme Toggle
        # -----------------------------------------------------------------
        print("[RUN] [Test 1] Homepage, Brand Identity & Theme Switching...")
        try:
            page.goto(f"{BASE_URL}/", wait_until="networkidle")
            page.wait_for_selector("header")
            
            # 1.1 Brand Title & Logo
            assert "KB Finvest" in page.title(), f"Unexpected title: {page.title()}"
            logo = page.locator("header a[href='/'] img").first
            assert logo.is_visible(), "Header logo is not visible"

            # 1.2 Theme Toggle
            html_elem = page.locator("html")
            initial_theme = html_elem.get_attribute("data-theme")
            assert initial_theme in ["dark", "light"], f"Unexpected initial theme: {initial_theme}"

            # Click theme toggle button
            toggle_btn = page.locator("#theme-toggle-btn")
            toggle_btn.click()
            page.wait_for_timeout(300)
            toggled_theme = html_elem.get_attribute("data-theme")
            assert toggled_theme != initial_theme, f"Theme did not toggle: still {toggled_theme}"

            # Toggle again to ensure idempotency
            toggle_btn.click()
            page.wait_for_timeout(300)
            reverted_theme = html_elem.get_attribute("data-theme")
            assert reverted_theme == initial_theme, "Theme toggle did not revert back properly"

            log_pass("Homepage Brand Identity & Theme Toggle")
        except Exception as e:
            log_fail("Homepage Brand Identity & Theme Toggle", e)

        # -----------------------------------------------------------------
        # TEST 2: Header Search Modal
        # -----------------------------------------------------------------
        print("[RUN] [Test 2] Header Search Modal & Live Filtering...")
        try:
            search_btn = page.locator("#header-search-btn")
            search_btn.click()
            page.wait_for_selector("input[placeholder*='Search']", timeout=3000)

            search_input = page.locator("input[placeholder*='Search']").first
            search_input.fill("SIP")
            page.wait_for_timeout(400)

            results = page.locator("button:has-text('SIP')")
            assert results.count() > 0, "No search results found for 'SIP'"

            # Close search modal with Escape
            page.keyboard.press("Escape")
            page.wait_for_timeout(300)

            log_pass("Header Search Modal & Search Query Filtering")
        except Exception as e:
            log_fail("Header Search Modal & Search Query Filtering", e)

        # -----------------------------------------------------------------
        # TEST 3: Homepage Interactive Calculators & Solution Finder
        # -----------------------------------------------------------------
        print("[RUN] [Test 3] Homepage Hero SIP & Compounding Simulators...")
        try:
            # 3.1 Hero Quick SIP Widget
            hero_slider = page.locator("input[type='range']").first
            initial_val = hero_slider.input_value()
            hero_slider.fill(str(int(initial_val) + 5000))
            page.wait_for_timeout(200)

            # 3.2 Solution Finder Wizard on Homepage
            finder = page.locator("text=Solution Finder").first
            if finder.is_visible():
                page.evaluate("window.scrollTo(0, 1500)")
                page.wait_for_timeout(400)
                
                # Step 1: Select Wealth Creation
                wealth_goal = page.locator("button:has-text('Wealth Creation')").first
                if wealth_goal.is_visible():
                    wealth_goal.click()
                    page.wait_for_timeout(300)
                    
                    # Step 2: Click Generate Roadmap
                    gen_btn = page.locator("button:has-text('Generate Roadmap')").first
                    if gen_btn.is_visible():
                        gen_btn.click()
                        page.wait_for_timeout(300)
                        
                        # Step 3: Check recommendation
                        rec_card = page.locator("text=Recommended Strategy").first
                        assert rec_card.is_visible(), "Solution Finder roadmap card did not render"

            log_pass("Homepage Live SIP Slider & Solution Finder Flow")
        except Exception as e:
            log_fail("Homepage Live SIP Slider & Solution Finder Flow", e)

        # -----------------------------------------------------------------
        # TEST 4: 14 Financial Calculators Suite (`/calculators`)
        # -----------------------------------------------------------------
        print("[RUN] [Test 4] 14 Financial Calculators Suite (`/calculators`)...")
        try:
            page.goto(f"{BASE_URL}/calculators", wait_until="networkidle")
            assert "Calculators" in page.title()

            # 4.1 Verify Category Selector Tabs
            cat_btn = page.locator("button:has-text('Loans & Credit')").first
            cat_btn.click()
            page.wait_for_timeout(300)
            
            # Check active calculator updated to Loan EMI
            active_calc_header = page.locator("h2").first
            assert "EMI" in active_calc_header.inner_text() or "Loan" in active_calc_header.inner_text(), "Failed to switch to Loan category"

            # 4.2 Switch back to All 14 Tools and click SIP Calculator
            all_cat_btn = page.locator("button:has-text('All 14 Tools')").first
            all_cat_btn.click()
            page.wait_for_timeout(300)
            
            sip_pill = page.locator("button:has-text('SIP')").first
            sip_pill.click()
            page.wait_for_timeout(300)

            # 4.3 Change SIP Input Slider and check calculation
            amount_input = page.locator("input[type='number']").first
            amount_input.fill("25000")
            page.wait_for_timeout(300)

            # Check Highlight Cards
            cards = page.locator("text=Total Invested").first
            assert cards.is_visible(), "Calculator Highlight Cards not visible"

            # 4.4 Test Year-by-Year Schedule Accordion
            breakdown_btn = page.locator("button:has-text('View Full Breakdown')").first
            if breakdown_btn.is_visible():
                breakdown_btn.click()
                page.wait_for_timeout(300)
                table = page.locator("table").first
                assert table.is_visible(), "Schedule table did not expand"

            # 4.5 Test Copy Calculation Summary
            copy_btn = page.locator("button:has-text('Copy Summary')").first
            if copy_btn.is_visible():
                copy_btn.click()
                page.wait_for_timeout(200)
                assert page.locator("text=Copied!").is_visible(), "Copy Summary confirmation failed"

            # 4.6 Test Print / Save PDF Modal
            print_btn = page.locator("button:has-text('Print / Save PDF')").first
            print_btn.click()
            page.wait_for_timeout(300)
            
            report_preview = page.locator("#kb-print-report")
            assert report_preview.is_visible(), "Print Report modal preview is not visible"
            assert page.locator("text=KB Finvest Advisory").first.is_visible(), "Letterhead missing in PDF preview"
            
            cancel_btn = page.locator("button:has-text('Cancel')").first
            cancel_btn.click()
            report_preview.wait_for(state="hidden", timeout=5000)

            # 4.7 Test SWP Calculator
            swp_pill = page.locator("button:has-text('SWP')").first
            swp_pill.click()
            page.wait_for_timeout(300)
            assert "SWP" in page.locator("h2").first.inner_text(), "Failed to switch to SWP Calculator"

            log_pass("14 Calculators Suite (Filters, Computations, Accordion, Copy, PDF Modal)")
        except Exception as e:
            log_fail("14 Calculators Suite (Filters, Computations, Accordion, Copy, PDF Modal)", e)

        # -----------------------------------------------------------------
        # TEST 5: Consultation Booking Journey (`/book`)
        # -----------------------------------------------------------------
        print("[RUN] [Test 5] Consultation Booking Journey (`/book`)...")
        try:
            page.goto(f"{BASE_URL}/book", wait_until="networkidle")
            assert "Book" in page.title() or "Consultation" in page.title()

            # Step 1: Select Format & Topic -> Click Continue
            step1_next = page.locator("button:has-text('Choose Date & Time Slot')").first
            assert step1_next.is_visible(), "Step 1 Continue button not visible"
            step1_next.click()
            page.wait_for_timeout(400)

            # Step 2: Pick Available Slot -> Click Continue
            slot_pills = page.locator("button:has-text(':')")
            if slot_pills.count() > 0:
                slot_pills.first.click()
                page.wait_for_timeout(200)

            step2_next = page.locator("button:has-text('Enter Your Details')").first
            assert step2_next.is_visible(), "Step 2 Continue button not visible"
            step2_next.click()
            page.wait_for_timeout(400)

            # Step 3: Contact Details
            name_input = page.locator("#book-name")
            phone_input = page.locator("#book-phone")
            name_input.fill("E2E Automated Verifier")
            
            # Generate unique phone number based on timestamp to avoid duplicate slot conflict
            test_phone = f"98140{int(time.time()) % 100000:05d}"
            phone_input.fill(test_phone)

            email_input = page.locator("#book-email")
            if email_input.is_visible():
                email_input.fill("verifier@kbfinvest.com")

            submit_btn = page.locator("button:has-text('Confirm 30-Min Consultation')")
            assert submit_btn.is_visible(), "Booking submit button not visible"
            submit_btn.click()

            # Wait for Success Screen (up to 15s to allow synchronous webhook handling)
            success_heading = page.locator("text=Consultation Requested!").first
            success_heading.wait_for(state="visible", timeout=15000)
            assert success_heading.is_visible()
            assert page.locator("text=Download .ics").first.is_visible(), "Calendar .ics download button missing"
            assert page.locator("text=Add to Google Calendar").first.is_visible(), "Google Calendar button missing"

            log_pass("Consultation Booking Journey (3-Step Wizard & Confirmed State)")
        except Exception as e:
            log_fail("Consultation Booking Journey (3-Step Wizard & Confirmed State)", e)

        # -----------------------------------------------------------------
        # TEST 6: Contact Form Inquiry Submission (`/contact`)
        # -----------------------------------------------------------------
        print("[RUN] [Test 6] Contact Desk Form (`/contact`)...")
        try:
            page.goto(f"{BASE_URL}/contact", wait_until="networkidle")
            assert "Contact" in page.title()

            test_contact_phone = f"98765{int(time.time()) % 100000:05d}"
            page.locator("#contact-name").fill("Automated E2E Lead")
            page.locator("#contact-phone").fill(test_contact_phone)
            page.locator("#contact-city").fill("Ludhiana Central")

            consent_chk = page.locator("#consent")
            if not consent_chk.is_checked():
                consent_chk.check()

            submit_btn = page.locator("form button[type='submit']")
            submit_btn.click()

            # Wait for Success Message (up to 15s to allow synchronous webhook handling)
            success_msg = page.locator("text=Enquiry Submitted!").first
            success_msg.wait_for(state="visible", timeout=15000)
            assert success_msg.is_visible()

            log_pass("Contact Desk Form & Lead Ingestion")
        except Exception as e:
            log_fail("Contact Desk Form & Lead Ingestion", e)

        # -----------------------------------------------------------------
        # TEST 7: Service Pages, About & Legal Disclosures Navigation
        # -----------------------------------------------------------------
        print("[RUN] [Test 7] Service Pages, About & Legal Disclosures...")
        try:
            # 7.1 Investments Page
            page.goto(f"{BASE_URL}/investments", wait_until="networkidle")
            main = page.locator("#main-content")
            main.locator("text=Investments & Wealth Creation").first.wait_for(state="visible", timeout=5000)
            pms_tab = main.locator("button:has-text('PMS')").first
            pms_tab.click()
            page.wait_for_timeout(200)
            main.locator("text=Portfolio Management Services").first.wait_for(state="visible", timeout=5000)

            # 7.2 Loans Page
            page.goto(f"{BASE_URL}/loans", wait_until="networkidle")
            main = page.locator("#main-content")
            main.locator("text=CGTMSE").first.wait_for(state="visible", timeout=5000)
            lap_tab = main.locator("button:has-text('Loan Against Property')").first
            lap_tab.click()
            page.wait_for_timeout(200)
            main.locator("text=Property").first.wait_for(state="visible", timeout=5000)

            # 7.3 Insurance Page
            page.goto(f"{BASE_URL}/insurance", wait_until="networkidle")
            main = page.locator("#main-content")
            main.locator("text=Health Insurance").first.wait_for(state="visible", timeout=5000)
            life_tab = main.locator("button:has-text('Life')").first
            life_tab.click()
            page.wait_for_timeout(200)

            # 7.4 Advisory Permanent Redirect (/advisory -> /investments)
            page.goto(f"{BASE_URL}/advisory", wait_until="networkidle")
            assert "investments" in page.url, f"Expected redirect to /investments, got: {page.url}"

            # 7.5 About Page
            page.goto(f"{BASE_URL}/about", wait_until="networkidle")
            main = page.locator("#main-content")
            main.locator("text=Kulwinder Singh").first.wait_for(state="visible", timeout=5000)
            main.locator("text=ARN-178400").first.wait_for(state="visible", timeout=5000)
            main.locator("text=The Conventional Product-Led Pitch").first.wait_for(state="attached", timeout=5000)
            main.locator("text=How KB Finvest Runs Instead").first.wait_for(state="attached", timeout=5000)

            # 7.6 Resources Page
            page.goto(f"{BASE_URL}/resources", wait_until="networkidle")
            main = page.locator("#main-content")
            faq_cat = main.locator("button:has-text('Lending')").first
            if faq_cat.is_visible():
                faq_cat.click()
                page.wait_for_timeout(200)

            # Checklist modal test
            checklist_btn = main.locator("button:has-text('View & track checklist')").first
            if checklist_btn.is_visible():
                checklist_btn.click()
                page.wait_for_timeout(300)
                modal = page.locator("text=Tick off what you have already prepared").first
                modal.wait_for(state="visible", timeout=5000)
                page.keyboard.press("Escape")
                page.wait_for_timeout(200)

            # 7.7 Legal Documents
            page.goto(f"{BASE_URL}/legal/disclosures", wait_until="networkidle")
            main = page.locator("#main-content")
            main.locator("text=Compliance & Legal Framework").first.wait_for(state="visible", timeout=5000)
            privacy_link = main.locator("a:has-text('Client Privacy & DPDP')").first
            if privacy_link.is_visible():
                privacy_link.click()
                page.wait_for_timeout(300)
                page.locator("#main-content text=Privacy").first.wait_for(state="visible", timeout=5000)

            log_pass("All Service Pages, About, Resources & Legal Disclosures Routing")
        except Exception as e:
            log_fail("All Service Pages, About, Resources & Legal Disclosures Routing", e)

        # -----------------------------------------------------------------
        # TEST 8: Mobile Responsive Viewport (390x844)
        # -----------------------------------------------------------------
        print("[RUN] [Test 8] Mobile Responsive Viewport (390x844)...")
        try:
            mobile_context = browser.new_context(viewport={"width": 390, "height": 844}, is_mobile=True)
            mob_page = mobile_context.new_page()
            mob_page.goto(f"{BASE_URL}/", wait_until="networkidle")

            # Check sticky mobile bottom action bar
            concierge_bar = mob_page.locator("aside").first
            assert concierge_bar.is_visible(), "Mobile sticky concierge bar is missing"
            assert mob_page.locator("aside a:has-text('WhatsApp')").is_visible()
            assert mob_page.locator("aside a:has-text('Call')").is_visible()
            assert mob_page.locator("aside a:has-text('Book')").is_visible()

            # Open Mobile Drawer
            burger_btn = mob_page.locator("button[aria-label='Open main navigation menu'], button:has-text('Menu')").first
            if burger_btn.is_visible():
                burger_btn.click()
                mob_page.wait_for_timeout(300)
                assert mob_page.locator("text=Services").first.is_visible(), "Mobile drawer did not open"
                
                # Mobile theme toggle test
                mob_toggle = mob_page.locator("#theme-toggle-mobile-btn")
                if mob_toggle.is_visible():
                    mob_toggle.click()
                    mob_page.wait_for_timeout(200)

            mobile_context.close()
            log_pass("Mobile Viewport Layout, Sticky Concierge Bar & Drawer")
        except Exception as e:
            log_fail("Mobile Viewport Layout, Sticky Concierge Bar & Drawer", e)

        # -----------------------------------------------------------------
        # TEST 9: Console Error & Network Health Audit
        # -----------------------------------------------------------------
        print("[RUN] [Test 9] Browser Console Errors & Runtime Health...")
        try:
            # Filter out non-critical harmless messages if any
            critical_console_errors = [
                err for err in console_errors 
                if "favicon" not in err.lower() and "status of 404" not in err.lower()
            ]
            if critical_console_errors:
                print(f"     [!] Notice: {len(critical_console_errors)} console errors recorded:")
                for ce in critical_console_errors[:5]:
                    print(f"       - {ce}")
            assert len(critical_console_errors) == 0, f"Found {len(critical_console_errors)} critical console errors"
            log_pass("Console Health Check (Zero unhandled exceptions)")
        except Exception as e:
            log_fail("Console Health Check (Zero unhandled exceptions)", e)

        browser.close()

    # -----------------------------------------------------------------
    # Final Report
    # -----------------------------------------------------------------
    print(f"\n=======================================================")
    print(f"[SUMMARY] E2E TESTING REPORT SUMMARY")
    print(f"=======================================================")
    print(f"  Total Suites: {passed_count + failed_count}")
    print(f"  Passed:       {passed_count}")
    print(f"  Failed:       {failed_count}")
    print(f"=======================================================\n")

    if failed_count > 0:
        print("[RESULT] Test failures encountered:")
        for tname, err in errors:
            print(f"  - {tname}: {err}")
        sys.exit(1)
    else:
        print("[SUCCESS] ALL E2E TEST FLOWS PASSED SUCCESSFULLY WITH 100% HEALTH!")
        sys.exit(0)

if __name__ == "__main__":
    run_e2e_tests()
