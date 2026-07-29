# Internship Report Weeks 5–7 Revision Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Revise Weeks 5–7 into a concise, academic internship-report narrative that presents the completed SmartWater deliverables without disclosing internal implementation detail.

**Architecture:** Preserve the current LaTex inclusion hierarchy and image assets. Rewrite the three weekly chapters around work objective, implementation summary, achieved results, and skills gained; keep Week 6's SmartWater Admin screens in its existing companion file.

**Tech Stack:** LaTex, Vietnamese academic writing, existing SmartWater screenshots.

## Global Constraints

- Modify only `QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week5.tex`, `week6.tex`, `week6-smartadmin.tex`, and `week7.tex`.
- Keep all existing image paths, labels, captions and the `main.tex` include hierarchy unchanged.
- State deliverables as completed while omitting configuration, source-code, API, schema, test-name and sensitive-infrastructure detail.
- Use consistent Vietnamese academic language with the subjects “sinh viên” and “hệ thống”.
- Do not modify application code or project data.

---

### Task 1: Rewrite Week 5 — device data transmission foundation

**Files:**
- Modify: `QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week5.tex`
- Test: `QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week5.tex`

**Interfaces:**
- Consumes: the Week 4 hardware-selection narrative and the Week 6 data-reception narrative.
- Produces: a completed Week 5 narrative that introduces the device-to-monitoring data flow without implementation exposure.

- [ ] **Step 1: Replace the work description with an academic objective and deliverables list**

Rewrite Section 5.1 to state that the student completed the data transmission channel from the water-purifier device to the SmartWater monitoring platform, including device connectivity, periodic telemetry transmission and message-content verification.

- [ ] **Step 2: Replace low-level implementation subsections with three concise implementation themes**

Use subsections covering device-data transmission preparation, stable communication, and telemetry-information standardization. Describe observable functions and project value; remove class names, broker configuration, topic literals, payload samples, field limits and timing values.

- [ ] **Step 3: Add achieved results and skills gained**

Close Week 5 with a result list confirming the completed transmission channel and readiness for monitoring integration, followed by a short paragraph on IoT communication, data-flow design and technical verification skills.

- [ ] **Step 4: Perform content checks**

Run: `rg -n "SimulatorApp|WifiManager|MqttManager|SimulatorConfig|devices/telemetry|verbatim|ESP32_001|millis\\(\\)" QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week5.tex`

Expected: no output.

- [ ] **Step 5: Commit the Week 5 revision**

Run: `git add -- QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week5.tex; git commit -m "docs: refine internship report week 5"`

### Task 2: Rewrite Week 6 — data monitoring and administrative application

**Files:**
- Modify: `QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week6.tex`
- Modify: `QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week6-smartadmin.tex`
- Test: both Week 6 sources

**Interfaces:**
- Consumes: the completed communication channel from Week 5 and the existing Week 6 image assets.
- Produces: a monitoring-and-administration narrative that Week 7 can summarize as completed work.

- [ ] **Step 1: Rewrite Week 6 telemetry integration narrative**

Condense Sections 6.1–6.2 into data reception, storage and monitoring activities. Retain the architecture diagram and screenshots, but describe the system at a functional level: receiving device information, recording monitoring data, selecting each device and observing TDS trends.

- [ ] **Step 2: Remove internal implementation detail from Week 6**

Delete or rewrite configuration examples, URL forms, JSON examples, table-field lists, index lists, API and repository terminology. Retain user-facing outcomes and high-level data-flow context.

- [ ] **Step 3: Standardize SmartWater Admin documentation**

Keep each screenshot and caption but group explanations into concise business modules: overview, catalog and inventory, customer and contract, device monitoring, and operational support. Remove controller, service, route, model and implementation-specific descriptions.

- [ ] **Step 4: Add Week 6 results and skills gained**

State that monitoring data are integrated into the management system, available by device and presented as trends; state that the administrative modules support operational information management. Add a short skill paragraph on system integration, database-aware application development and interface evaluation.

- [ ] **Step 5: Perform content checks**

Run: `rg -n "wss://|verbatim|Repository|DashboardController|DashboardService|prepared statement|index|/api/|smartwater_database" QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week6.tex QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week6-smartadmin.tex`

Expected: no output.

- [ ] **Step 6: Commit the Week 6 revision**

Run: `git add -- QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week6.tex QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week6-smartadmin.tex; git commit -m "docs: refine internship report week 6"`

### Task 3: Rewrite Week 7 — completion and internship outcomes

**Files:**
- Modify: `QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week7.tex`
- Test: `QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week7.tex`

**Interfaces:**
- Consumes: the completed Week 5 communication and Week 6 monitoring/administration narratives.
- Produces: an academic conclusion for the final internship week.

- [ ] **Step 1: Rewrite the test scope as final validation activities**

Describe final validation across the device-data flow, monitoring screen, business data and administrative modules. Do not name test scripts, endpoints, status codes, database objects or exact test fixtures.

- [ ] **Step 2: Rewrite optimization and completion content at the outcome level**

Describe improvements as consistent device identification, reliable data access, efficient monitoring views and a coherent management workflow. Do not disclose query, schema, timestamp or component-level implementation details.

- [ ] **Step 3: Add internship skills and orientation**

Summarize skills gained: requirement analysis, coordinated implementation, data-flow integration, validation, documentation and professional teamwork. End with a forward-looking statement about extending the system to support operational scale.

- [ ] **Step 4: Perform content checks**

Run: `rg -n "Test\\.php|Test\\.ps1|HTTP|/api/|Seeder|migrate\\.bat|prepared statement|index|timestamp|smartwater_database" QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week7.tex`

Expected: no output.

- [ ] **Step 5: Commit the Week 7 revision**

Run: `git add -- QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week7.tex; git commit -m "docs: refine internship report week 7"`

### Task 4: Validate the revised report sources

**Files:**
- Test: `QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/main.tex` and the four revised sources

**Interfaces:**
- Consumes: all rewritten weekly chapters.
- Produces: static evidence that the report structure remains consistent.

- [ ] **Step 1: Verify includes and image paths**

Run a PowerShell static check that confirms `main.tex` includes Weeks 5–7 and every `\\includegraphics` path used by the revised files resolves from the report root.

- [ ] **Step 2: Verify labels and brace balance**

Run a PowerShell static check that flags duplicate figure labels and unbalanced curly braces in the four changed files.

- [ ] **Step 3: Run LaTex compilation if available**

Run: `latexmk -pdf -interaction=nonstopmode -halt-on-error main.tex`

Expected: a regenerated `main.pdf` with no LaTex error. If `latexmk` is unavailable, record static validation and do not modify generated artifacts.

- [ ] **Step 4: Check the final diff**

Run: `git diff --check -- QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253`

Expected: no output.

## Self-review

- Spec coverage: Tasks 1–3 cover the four target files, the requested academic narrative, completed-deliverable framing and removal of internal detail. Task 4 covers structural validation.
- Placeholder scan: no placeholder markers or deferred work items are present.
- Consistency: each weekly chapter uses the same narrative pattern; images and inclusion boundaries remain unchanged.
