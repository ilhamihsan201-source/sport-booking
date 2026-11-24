import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn

def set_cell_background(cell, fill_hex):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tcPr.append(shd)

def create_docx(filename):
    doc = docx.Document()

    # Page Margins
    sections = doc.sections
    for section in sections:
        section.top_margin = Inches(0.8)
        section.bottom_margin = Inches(0.8)
        section.left_margin = Inches(0.8)
        section.right_margin = Inches(0.8)

    # Styles
    title_p = doc.add_paragraph()
    title_run = title_p.add_run("SPORTBOOKING INDONESIA")
    title_run.font.name = "Calibri"
    title_run.font.size = Pt(13)
    title_run.font.bold = True
    title_run.font.color.rgb = RGBColor(2, 132, 199)

    h_p = doc.add_paragraph()
    h_run = h_p.add_run("Application Discovery & QA Documentation")
    h_run.font.name = "Calibri"
    h_run.font.size = Pt(22)
    h_run.font.bold = True
    h_run.font.color.rgb = RGBColor(15, 23, 42)

    doc.add_paragraph(
        "Dokumentasi resmi arsitektur sistem, penemuan fitur, kontrol akses, dan laporan audit kualitas (QA) aplikasi SportBooking.\n"
        "Status Audit: PASSED (67 Tests, 200 Assertions - 100% Success)"
    )

    doc.add_heading("1. Profil & Klasifikasi Sistem", level=1)
    p = doc.add_paragraph()
    p.add_run("• Nama Proyek: ").bold = True
    p.add_run("SportBooking Indonesia\n")
    p.add_run("• Repository GitHub: ").bold = True
    p.add_run("https://github.com/ilhamihsan201-source/sport-booking\n")
    p.add_run("• Production URL: ").bold = True
    p.add_run("https://sport-booking-production-0df4977f.up.railway.app\n")
    p.add_run("• Teknologi: ").bold = True
    p.add_run("Laravel 11, PHP 8.2, MySQL 8, Tailwind CSS, Vite\n")

    doc.add_heading("2. Inventarisasi Halaman (Page Discovery)", level=1)
    pages = [
        ("PG-01", "Beranda / Landing", "/", "Public", "Showcase lapangan, hero banner, call-to-action reservasi."),
        ("PG-02", "Katalog Arena", "/arena", "Public", "Katalog cabang olahraga (Padel, Mini Soccer) & lapangan."),
        ("PG-03", "Detail Lapangan", "/lapangan/{court}", "Public", "Galeri foto, spesifikasi, harga/jam, fasilitas."),
        ("PG-04", "Jadwal & Slot", "/jadwal", "Public", "Pemeriksaan ketersediaan slot jam main secara real-time."),
        ("PG-05", "Pemesanan", "POST /booking", "Customer", "Pembuatan booking dengan pencegahan tabrakan jadwal."),
        ("PG-06", "Pembayaran", "/booking/{code}/pembayaran", "Customer", "Instruksi transfer bank & upload bukti bayar."),
        ("PG-07", "Dashboard Customer", "/customer/dashboard", "Customer", "Riwayat booking aktif, selesai, dan status bayar."),
        ("PG-08", "Dashboard Admin", "/admin/dashboard", "Admin", "Statistik booking, total omset, status pembayaran."),
        ("PG-09", "Verifikasi Pembayaran", "/admin/payments", "Admin", "Approval atau penolakan bukti transfer."),
        ("PG-10", "Kelola Lapangan", "/admin/courts", "Admin", "CRUD lapangan, upload foto multiple, toggle aktif."),
    ]
    t1 = doc.add_table(rows=1, cols=5)
    t1.alignment = WD_TABLE_ALIGNMENT.CENTER
    hdr = t1.rows[0].cells
    hdr[0].text = "ID"
    hdr[1].text = "Halaman"
    hdr[2].text = "Route"
    hdr[3].text = "Role"
    hdr[4].text = "Deskripsi"
    for c in hdr:
        set_cell_background(c, "0F172A")
        for p in c.paragraphs:
            for r in p.runs:
                r.font.color.rgb = RGBColor(255, 255, 255)
                r.font.bold = True

    for item in pages:
        row = t1.add_row().cells
        for idx, text in enumerate(item):
            row[idx].text = text

    doc.add_heading("3. Hasil Pengujian Otomatis (QA Results)", level=1)
    doc.add_paragraph(
        "Pengujian QA otomatis dijalankan menggunakan PHPUnit & Laravel Testing Framework dengan SQLite in-memory database "
        "mencakup unit test, otentikasi Breeze, proteksi otorisasi, pencegahan double booking, dan alur pembayaran."
    )

    qa_summary = [
        ("Unit Tests (System Integrity)", "1", "1", "100% PASS", "0.59s"),
        ("Authentication & RBAC Access Tests", "9", "28", "100% PASS", "9.49s"),
        ("Security: Email & Password Management", "12", "34", "100% PASS", "6.58s"),
        ("Registration & Profile Integrity", "7", "21", "100% PASS", "5.95s"),
        ("Core SportBooking Business & Lifecycle Tests", "38", "116", "100% PASS", "9.97s"),
        ("TOTAL KESELURUHAN", "67 Tests", "200 Assertions", "ALL PASSED", "32.58s"),
    ]
    t2 = doc.add_table(rows=1, cols=5)
    t2.alignment = WD_TABLE_ALIGNMENT.CENTER
    hdr2 = t2.rows[0].cells
    hdr2[0].text = "Kategori Pengujian"
    hdr2[1].text = "Jumlah Test"
    hdr2[2].text = "Assertions"
    hdr2[3].text = "Status"
    hdr2[4].text = "Durasi"
    for c in hdr2:
        set_cell_background(c, "0F172A")
        for p in c.paragraphs:
            for r in p.runs:
                r.font.color.rgb = RGBColor(255, 255, 255)
                r.font.bold = True

    for item in qa_summary:
        row = t2.add_row().cells
        for idx, text in enumerate(item):
            row[idx].text = text

    doc.add_heading("4. Kesimpulan & Serah Terima (Handover)", level=1)
    doc.add_paragraph(
        "Aplikasi SportBooking telah lolos seluruh kriteria pengujian fungsional dan keamanan. "
        "Sistem siap digunakan di production environment Railway."
    )

    doc.save(filename)
    print(f"Successfully generated DOCX: {filename}")

if __name__ == "__main__":
    create_docx("QA/SportBooking_Application_Discovery_and_QA_Documentation.docx")
