# SMARTWATER SOLUTION DESIGN DOCUMENT

Dự án LaTeX dùng XeLaTeX, tiếng Việt, phiên bản tài liệu `0.1` và trạng thái `Draft`.

## Biên dịch

Chạy từ thư mục `QLDA/Form_report/Truliliva-proposal`:

```powershell
xelatex -interaction=nonstopmode -halt-on-error main.tex
xelatex -interaction=nonstopmode -halt-on-error main.tex
```

Hoặc dùng `latexmk`:

```powershell
latexmk -xelatex -interaction=nonstopmode main.tex
```

PDF đầu ra: `main.pdf`.

## Cấu trúc

- `main.tex`: preamble, front matter, 17 chương và phụ lục A--K.
- `include/frontmatter`: trang bìa và Document Control.
- `include/chapters`: nội dung thiết kế giải pháp.
- `include/diagrams`: sáu sơ đồ TikZ.
- `include/appendices`: catalogue, đặc tả, bằng chứng và ma trận chức năng.
- `include/images`: ảnh kiến trúc, dữ liệu và giao diện được chọn lọc.
