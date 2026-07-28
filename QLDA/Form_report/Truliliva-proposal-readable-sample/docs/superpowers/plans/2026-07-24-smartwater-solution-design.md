# SmartWater Solution Design LaTeX Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Xây dựng dự án LaTeX có thể biên dịch thành tài liệu `SMARTWATER SOLUTION DESIGN DOCUMENT`, phản ánh trung thực source code SmartWater hiện tại.

**Architecture:** `main.tex` quản lý định dạng và thứ tự nội dung; mỗi chương, phụ lục và sơ đồ là một file độc lập trong `include/`. Nội dung được dựng sau inventory và gap analysis, sử dụng migration làm nguồn chuẩn cho schema và dẫn chứng đường dẫn source cho mọi nhận định quan trọng.

**Tech Stack:** LaTeX, XeLaTeX hoặc pdfLaTeX có hỗ trợ tiếng Việt, TikZ/PGF, longtable, tabularx, graphicx, hyperref, SmartWater source code, MySQL/Laravel migrations.

## Global Constraints

- Chỉ đọc source ứng dụng; không refactor, sửa bug, đổi schema hoặc thay đổi cấu hình runtime.
- Không ghi password, token, API key, certificate hoặc giá trị secret vào tài liệu.
- Phân loại bằng đúng sáu trạng thái: Implemented, Partially Implemented, Configured, Prototype/Mock, Planned, Not Found.
- Migration là nguồn chuẩn khi model và schema không đồng nhất.
- `mcu_id` là định danh chuẩn của telemetry theo schema hiện tại.
- Văn phong tiếng Việt chuyên nghiệp, trung lập, không dùng ngôi thứ nhất.
- Tài liệu mang phiên bản `0.1`, trạng thái `Draft`, tác giả `Hoàng Anh Duy`.
- Bảng lô hàng được phân tích trong database catalogue nhưng ghi rõ nút điều hướng đã bị ẩn khỏi Admin Dashboard.

---

## File Structure

- Create: `QLDA/Form_report/Truliliva-proposal/main.tex` — entry point và định dạng chung.
- Create: `QLDA/Form_report/Truliliva-proposal/include/frontmatter/*.tex` — bìa, kiểm soát tài liệu, lịch sử phiên bản.
- Create: `QLDA/Form_report/Truliliva-proposal/include/chapters/01-introduction.tex` đến `17-conclusion.tex` — nội dung 17 chương.
- Create: `QLDA/Form_report/Truliliva-proposal/include/appendices/*.tex` — inventory, routes, database, MQTT, cấu hình, inconsistencies và evidence.
- Create: `QLDA/Form_report/Truliliva-proposal/include/diagrams/*.tex` — sáu sơ đồ TikZ.
- Create: `QLDA/Form_report/Truliliva-proposal/include/images/*` — ảnh được chọn và sao chép từ hai thư mục ảnh đã cung cấp.
- Create: `QLDA/Form_report/Truliliva-proposal/README.md` — lệnh biên dịch và cấu trúc tài liệu.

---

### Task 1: Source inventory và evidence register

**Files:**
- Create: `QLDA/Form_report/Truliliva-proposal/include/appendices/a-repository-structure.tex`
- Create: `QLDA/Form_report/Truliliva-proposal/include/appendices/b-component-inventory.tex`
- Create: `QLDA/Form_report/Truliliva-proposal/include/appendices/j-source-evidence.tex`

**Interfaces:**
- Consumes: `Project/firmware`, `Project/Device-monitor`, `Project/smartwater-admin`, `Project/smartwater-database`, các service cấp cao nhất trong repository.
- Produces: danh mục component và evidence path được các chương sau tham chiếu.

- [ ] **Step 1: Liệt kê entry point và manifest**

Run:

```powershell
rg --files Project -g "platformio.ini" -g "composer.json" -g "package.json" -g "*.csproj" -g "Dockerfile*" -g "docker-compose*.yml" -g ".env.example"
```

Expected: danh sách manifest của firmware, Device Monitor, Laravel Admin và các service thực sự tồn tại.

- [ ] **Step 2: Lập inventory theo bằng chứng**

Ghi mỗi component với các cột: Thành phần, đường dẫn, công nghệ, entry point, vai trò, trạng thái. Không đưa component không tìm thấy vào nhóm Implemented.

- [ ] **Step 3: Tạo source evidence index**

Ghi các evidence theo định dạng `E-<nhóm>-<số>: đường/dẫn/file — class/method/route/config key`, không sao chép secret value.

- [ ] **Step 4: Kiểm tra inventory**

Run:

```powershell
Select-String -Path include\appendices\a-repository-structure.tex,include\appendices\b-component-inventory.tex,include\appendices\j-source-evidence.tex -Pattern "TBD|TODO|password\s*=|api_key\s*="
```

Expected: không có kết quả.

---

### Task 2: Khảo sát kiến trúc và luồng telemetry

**Files:**
- Create: `include/chapters/02-solution-overview.tex`
- Create: `include/chapters/03-system-architecture.tex`
- Create: `include/chapters/05-data-flow-design.tex`
- Create: `include/diagrams/system-architecture.tex`
- Create: `include/diagrams/deployment.tex`
- Create: `include/diagrams/component.tex`
- Create: `include/diagrams/telemetry-sequence.tex`
- Create: `include/diagrams/error-sequence.tex`

**Interfaces:**
- Consumes: evidence register từ Task 1 và source firmware, MQTT, Device Monitor, Laravel API/database.
- Produces: kiến trúc thực tế, dependency graph và telemetry flow để chương integration/database sử dụng.

- [ ] **Step 1: Truy vết publisher đến persistence**

Run:

```powershell
rg -n "mcu_id|tds|alert|topic|publish|subscribe|telemetry|api/telemetry" Project/firmware Project/Device-monitor Project/smartwater-admin Project/smartwater-database
```

Expected: xác định được file phát MQTT, callback/parser, API hoặc repository lưu dữ liệu, migration và query dashboard.

- [ ] **Step 2: Viết kiến trúc theo bằng chứng**

Mô tả Sensor/Simulator → ESP32 → HiveMQ → Device Monitor → MySQL → SmartWater Admin; nếu source cho thấy nhánh khác, ghi đúng nhánh thực tế và trạng thái của nó.

- [ ] **Step 3: Dựng năm sơ đồ TikZ**

Mỗi node phải tương ứng một component đã có evidence. Error sequence chỉ gồm invalid JSON, unknown MCU, database error hoặc MQTT disconnect nếu tìm thấy xử lý tương ứng.

- [ ] **Step 4: Kiểm tra thuật ngữ telemetry**

Run:

```powershell
Select-String -Path include\chapters\*.tex,include\diagrams\*.tex -Pattern "device_id.*telemetry|telemetry.*device_id"
```

Expected: không mô tả `device_id` là khóa chuẩn của bảng telemetry; mọi khác biệt lịch sử phải nằm trong phần inconsistency.

---

### Task 3: Phân tích database và mapping dữ liệu

**Files:**
- Create: `include/chapters/06-data-design.tex`
- Create: `include/appendices/d-database-catalogue.tex`
- Create: `include/appendices/f-telemetry-json.tex`
- Create: `include/appendices/i-known-inconsistencies.tex`
- Create: `include/diagrams/entity-relationship.tex`

**Interfaces:**
- Consumes: `Project/smartwater-database/database/migrations`, models/queries của Admin và Device Monitor.
- Produces: table catalogue, ERD, field mapping và danh sách model/migration/query mismatch.

- [ ] **Step 1: Trích xuất schema thực tế**

Run:

```powershell
rg -n "Schema::create|Schema::table|foreignId|unique\(|index\(|enum\(|timestamps\(" Project/smartwater-database/database/migrations
```

Expected: catalogue đầy đủ các table, foreign key, unique/index, enum và timestamp hiện có.

- [ ] **Step 2: Đối chiếu model và query**

Run:

```powershell
rg -n "protected \\$table|fillable|casts|belongsTo|hasOne|hasMany|DB::table|->join\(" Project/smartwater-admin/app Project/Device-monitor
```

Expected: xác định model/table/relationship/query đang được sử dụng thực tế.

- [ ] **Step 3: Viết catalogue và field mapping**

Mỗi table có: mục đích, PK, FK, quan hệ, module sử dụng, trạng thái dùng thực tế. Mapping telemetry phải đối chiếu firmware payload, Device Monitor parser, migration, model/repository và dashboard field.

- [ ] **Step 4: Dựng ERD từ migration**

Thể hiện tối thiểu Customer → Contract → Product → Device + MCU và MCU → Telemetry; đánh dấu nullable/unique bằng chú thích.

- [ ] **Step 5: Kiểm tra tên table**

Run:

```powershell
Select-String -Path include\appendices\d-database-catalogue.tex -Pattern "device_replacement_histories"
```

Expected: không có kết quả vì schema hiện tại dùng self-reference `devices.replaced_by_device_id`.

---

### Task 4: Phân tích SmartWater Admin và feature matrix

**Files:**
- Create: `include/chapters/08-smartwater-admin.tex`
- Create: `include/appendices/c-route-catalogue.tex`
- Create: `include/appendices/k-feature-matrix.tex`

**Interfaces:**
- Consumes: Laravel routes, controllers, models, views, JavaScript và database catalogue.
- Produces: module design, route catalogue và implementation status matrix.

- [ ] **Step 1: Đọc route và module entry points**

Run:

```powershell
rg -n "Route::|class .*Controller|return view\(|fetch\(|new ApexCharts|data-datatable" Project/smartwater-admin/routes Project/smartwater-admin/app Project/smartwater-admin/resources
```

Expected: mapping Route → Controller → View/API cho từng module có thật.

- [ ] **Step 2: Phân loại module**

Đánh giá Authentication, Dashboard, Customer, Contract, Product, Inventory, Device, MCU, Maintenance, Employee, Activity và Telemetry. Chỉ tạo mục chi tiết cho module có source.

- [ ] **Step 3: Ghi trạng thái Lô hàng**

Route/controller/view của Batch được ghi theo evidence; navigation được phân loại là ẩn vì dòng `batches.index` trong sidebar đã được comment.

- [ ] **Step 4: Tạo feature matrix**

Mỗi hàng có Module, chức năng, evidence, trạng thái sáu mức, giới hạn. Dữ liệu hard-code hoặc mock phải được ghi `Prototype/Mock`.

- [ ] **Step 5: Kiểm tra mọi route có evidence**

Run:

```powershell
Select-String -Path include\appendices\c-route-catalogue.tex -Pattern "route\{|Evidence"
```

Expected: mỗi nhóm route có ít nhất một evidence path.

---

### Task 5: Phân tích component, integration, security và vận hành

**Files:**
- Create: `include/chapters/04-component-design.tex`
- Create: `include/chapters/07-integration-design.tex`
- Create: `include/chapters/09-security.tex`
- Create: `include/chapters/10-operations-error-handling.tex`
- Create: `include/chapters/11-technologies.tex`
- Create: `include/chapters/12-deployment.tex`
- Create: `include/appendices/e-mqtt-specification.tex`
- Create: `include/appendices/g-configuration-variables.tex`

**Interfaces:**
- Consumes: manifest/config/source evidence và kiến trúc từ Tasks 1–4.
- Produces: component contracts, integration catalogue, security status, deployment/runbook evidence.

- [ ] **Step 1: Xác định version và dependency**

Đọc `platformio.ini`, `composer.json`, `package.json`, project files và lock files liên quan; chỉ ghi version xác định được.

- [ ] **Step 2: Lập MQTT specification**

Ghi broker variable name, transport, port variable, TLS evidence, topic, QoS, client identity variable, payload và reconnect behavior. Thay mọi secret value bằng tên biến cấu hình.

- [ ] **Step 3: Phân tích security và error handling**

Mỗi kiểm soát được gắn một trạng thái; nội dung không tìm thấy phải ghi `Not Found`, không chuyển thành khẳng định triển khai.

- [ ] **Step 4: Phân tích deployment**

Ghi đúng lệnh build/start tìm thấy trong source hoặc manifest; phân biệt cấu hình hiện có với deployment đã được kiểm chứng.

- [ ] **Step 5: Quét secret trong tài liệu**

Run:

```powershell
rg -n -i "password\s*[=:]\s*[^}\\]|api[_-]?key\s*[=:]\s*[^}\\]|BEGIN .*PRIVATE KEY|hivemq.*cloud" include main.tex
```

Expected: không có secret value; hostname công khai chỉ được giữ khi cần mô tả cấu hình và không chứa credential.

---

### Task 6: Gap analysis, scalability và trạng thái triển khai

**Files:**
- Create: `include/chapters/13-scalability-performance.tex`
- Create: `include/chapters/14-implementation-status.tex`
- Create: `include/chapters/15-limitations-risks.tex`
- Create: `include/chapters/16-future-enhancements.tex`

**Interfaces:**
- Consumes: toàn bộ inventory, feature matrix, database/integration mismatches.
- Produces: gap/risk register và roadmap không bị trình bày nhầm là tính năng hiện tại.

- [ ] **Step 1: Tổng hợp gap có evidence**

Đưa vào các route/view mismatch, mock data, model/migration mismatch, thiếu validation/index/error handling và cấu hình hard-code được chứng minh từ source.

- [ ] **Step 2: Viết scalability không dùng số liệu suy đoán**

Chỉ phân tích topology, telemetry frequency cấu hình, query/index hiện có và điểm nghẽn kiến trúc; không ghi throughput hoặc SLA khi không có benchmark.

- [ ] **Step 3: Gắn nhãn Future Enhancement**

Alert notification, OTA, predictive maintenance, Edge AI, time-series database, horizontal scaling và observability phải có nhãn `Future Enhancement`.

- [ ] **Step 4: Kiểm tra phát ngôn quá mức**

Run:

```powershell
rg -n -i "production-ready|hoàn hảo|tối ưu tuyệt đối|đảm bảo SLA|chịu tải [0-9]" include\chapters
```

Expected: không có kết quả.

---

### Task 7: Dựng dự án LaTeX và tích hợp nội dung

**Files:**
- Create: `main.tex`
- Create: `include/frontmatter/title-page.tex`
- Create: `include/frontmatter/document-control.tex`
- Create: `include/chapters/01-introduction.tex`
- Create: `include/chapters/17-conclusion.tex`
- Create: `README.md`
- Copy: ảnh được chọn vào `include/images/`

**Interfaces:**
- Consumes: tất cả file `.tex` từ Tasks 1–6.
- Produces: entry point LaTeX và PDF hoàn chỉnh.

- [ ] **Step 1: Tạo preamble có hỗ trợ tiếng Việt**

Dùng `article`, A4, `fontspec` khi XeLaTeX có sẵn; dùng `geometry`, `fancyhdr`, `hyperref`, `graphicx`, `booktabs`, `longtable`, `tabularx`, `xcolor`, `tikz`, `listings` và `caption`.

- [ ] **Step 2: Tích hợp 17 chương và phụ lục**

Mỗi `\input{}` trỏ đến đúng file tồn tại; tạo mục lục, danh mục hình, danh mục bảng và đánh số phụ lục.

- [ ] **Step 3: Chọn ảnh**

Ưu tiên `smartwater-architect.png`, `Device-monitor.png`, `DB_Design.png` và các ảnh Admin thể hiện module thực tế. Không dùng ảnh Lô hàng như module điều hướng đang hoạt động.

- [ ] **Step 4: Viết README build**

Ghi lệnh:

```powershell
xelatex -interaction=nonstopmode -halt-on-error main.tex
xelatex -interaction=nonstopmode -halt-on-error main.tex
```

Nếu chỉ có `latexmk`, ghi thêm `latexmk -xelatex -interaction=nonstopmode main.tex`.

- [ ] **Step 5: Biên dịch hai lượt**

Run:

```powershell
xelatex -interaction=nonstopmode -halt-on-error main.tex
xelatex -interaction=nonstopmode -halt-on-error main.tex
```

Expected: exit code 0 và tạo `main.pdf`.

- [ ] **Step 6: Kiểm tra log và tham chiếu**

Run:

```powershell
Select-String -LiteralPath 'main.log' -Pattern "LaTeX Error|Undefined control sequence|Reference .* undefined|Citation .* undefined|File .* not found"
```

Expected: không có kết quả.

- [ ] **Step 7: Kiểm tra phạm vi thay đổi**

Run:

```powershell
git status --short -- QLDA/Form_report/Truliliva-proposal
git diff --check -- QLDA/Form_report/Truliliva-proposal
```

Expected: chỉ có file tài liệu trong `Truliliva-proposal`; `git diff --check` exit code 0.

