# Internship Report Table of Contents Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Generate a complete, navigable table of contents for the internship report, including the opening section and all Week 1--7 headings.

**Architecture:** Keep the report body and its existing manually numbered headings intact. Add three small heading wrappers that render the existing unnumbered headings and explicitly write their titles to the `.toc` file; use them consistently in the seven week content files. Add the opening section to the table of contents and remove its invalid trailing line break so the report can compile through the included week files.

**Tech Stack:** LaTeX (`article`, `hyperref`, generated `.toc` files), MiKTeX XeLaTeX or pdfLaTeX.

## Global Constraints

- Preserve the user's existing content, images, and unrelated worktree changes.
- Keep existing heading text and manually authored hierarchical numbering unchanged.
- Include entries through `subsubsubsection` level only; do not add unrelated lists of figures or tables.
- Run the compiler twice so page numbers and table-of-contents entries settle.

---

### Task 1: Make starred weekly headings write to the table of contents

**Files:**
- Modify: `QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/main.tex:145-160`
- Modify: `QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week1.tex`
- Modify: `QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week2.tex`
- Modify: `QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week3.tex`
- Modify: `QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week4.tex`
- Modify: `QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week5.tex`
- Modify: `QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week6.tex`
- Modify: `QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week6-smartadmin.tex`
- Modify: `QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week7.tex`
- Test: generated `QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/main.toc`

**Interfaces:**
- Consumes: Existing titles currently passed to `\subsection*`, `\subsubsection*`, and `\subsubsubsection*`.
- Produces: `\tocsubsection`, `\tocsubsubsection`, and `\tocsubsubsubsection`, each accepting one title argument and writing a corresponding `.toc` entry.

- [ ] **Step 1: Add table-of-contents heading wrappers after the existing section-level definitions.**

```tex
\newcommand{\tocsubsection}[1]{%
  \subsection*{#1}%
  \addcontentsline{toc}{subsection}{#1}%
}
\newcommand{\tocsubsubsection}[1]{%
  \subsubsection*{#1}%
  \addcontentsline{toc}{subsubsection}{#1}%
}
\newcommand{\tocsubsubsubsection}[1]{%
  \subsubsubsection*{#1}%
  \addcontentsline{toc}{subsubsubsection}{#1}%
}
```

- [ ] **Step 2: Replace every weekly starred heading command with its matching wrapper without changing its title.**

```tex
\subsection*{Tuần 1}
```

becomes:

```tex
\tocsubsection{Tuần 1}
```

- [ ] **Step 3: Confirm every starred week heading was converted.**

Run: `rg -n "^\\(subsection|subsubsection|subsubsubsection)\*\{" QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/include/content/week*.tex`

Expected: no output.

### Task 2: Include the opening section and build a settled table of contents

**Files:**
- Modify: `QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/main.tex:209-212`
- Test: `QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253/main.toc`

**Interfaces:**
- Consumes: The existing `\section*{LỜI MỞ ĐẦU}` title.
- Produces: A `section`-level table-of-contents entry for `LỜI MỞ ĐẦU` and valid paragraph flow before the opening text.

- [ ] **Step 1: Add the opening title to the `.toc` file and remove the orphaned line break after the centered heading.**

```tex
\begin{center}
    \section*{LỜI MỞ ĐẦU}
    \addcontentsline{toc}{section}{LỜI MỞ ĐẦU}
\end{center}
```

- [ ] **Step 2: Compile the report twice from its report directory.**

Run: `xelatex -interaction=nonstopmode -halt-on-error main.tex` twice.

Expected: both passes complete successfully and regenerate `main.toc`.

- [ ] **Step 3: Inspect the generated entries and source integrity.**

Run: `rg -n "LỜI MỞ ĐẦU|Tuần [1-7]|7\.2\.4" main.toc; git diff --check -- QLDA/Form_report/DA_TTNT_HoangAnhDuy_2310458_HK253`

Expected: the opening section, all seven weeks, and the final Week 7 detail appear; no whitespace errors are reported.
