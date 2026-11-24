import os
import sys
from reportlab.lib.pagesizes import A4
from reportlab.lib import colors
from reportlab.lib.units import inch, cm
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak, KeepTogether, HRFlowable
)
from reportlab.pdfgen import canvas

class NumberedCanvas(canvas.Canvas):
    def __init__(self, *args, **kwargs):
        super(NumberedCanvas, self).__init__(*args, **kwargs)
        self._saved_page_states = []

    def showPage(self):
        self._saved_page_states.append(dict(self.__dict__))
        self._startPage()

    def save(self):
        num_pages = len(self._saved_page_states)
        for state in self._saved_page_states:
            self.__dict__.update(state)
            self.draw_page_decorations(num_pages)
            super(NumberedCanvas, self).showPage()
        super(NumberedCanvas, self).save()

    def draw_page_decorations(self, page_count):
        if self._pageNumber == 1:
            return  # Cover page without header/footer

        self.saveState()
        self.setFont("Helvetica", 8)
        self.setFillColor(colors.HexColor("#64748b"))

        # Header
        self.drawString(40, 810, "SportBooking Indonesia — Application Discovery & QA Documentation")
        self.drawRightString(555, 810, "QA & Reverse Engineering Report")
        self.setStrokeColor(colors.HexColor("#cbd5e1"))
        self.setLineWidth(0.5)
        self.line(40, 804, 555, 804)

        # Footer
        self.line(40, 40, 555, 40)
        self.drawString(40, 28, "Confidential — For Internal Quality Assurance & Audit")
        self.drawRightString(555, 28, f"Halaman {self._pageNumber} dari {page_count}")
        self.restoreState()


def create_qa_pdf(filename):
    doc = SimpleDocTemplate(
        filename,
        pagesize=A4,
        leftMargin=40,
        rightMargin=40,
        topMargin=50,
        bottomMargin=50
    )

    styles = getSampleStyleSheet()

    # Custom typography
    primary_color = colors.HexColor("#0f172a") # Slate 900
    brand_accent = colors.HexColor("#0284c7")  # Sky 600
    success_color = colors.HexColor("#16a34a") # Green 600
    text_dark = colors.HexColor("#1e293b")     # Slate 800
    text_muted = colors.HexColor("#475569")    # Slate 600

    title_style = ParagraphStyle(
        'CoverTitle',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=24,
        leading=30,
        textColor=primary_color,
        alignment=0
    )

    subtitle_style = ParagraphStyle(
        'CoverSubtitle',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=12,
        leading=16,
        textColor=brand_accent,
        alignment=0
    )

    h1_style = ParagraphStyle(
        'Heading1Custom',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=15,
        leading=19,
        textColor=primary_color,
        spaceBefore=14,
        spaceAfter=6,
        keepWithNext=True
    )

    h2_style = ParagraphStyle(
        'Heading2Custom',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=12,
        leading=15,
        textColor=brand_accent,
        spaceBefore=10,
        spaceAfter=4,
        keepWithNext=True
    )

    body_style = ParagraphStyle(
        'BodyCustom',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=9.5,
        leading=14,
        textColor=text_dark,
        spaceAfter=6
    )

    badge_pass = ParagraphStyle(
        'BadgePass',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=8,
        leading=10,
        textColor=colors.HexColor("#166534"),
        alignment=1
    )

    table_header = ParagraphStyle(
        'TableHeader',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=8.5,
        leading=11,
        textColor=colors.white,
        alignment=0
    )

    table_cell = ParagraphStyle(
        'TableCell',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8,
        leading=11,
        textColor=text_dark
    )

    table_cell_bold = ParagraphStyle(
        'TableCellBold',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=8,
        leading=11,
        textColor=text_dark
    )

    story = []

    # ------------------ COVER PAGE ------------------
    story.append(Spacer(1, 40))
    story.append(Paragraph("SPORTBOOKING INDONESIA", subtitle_style))
    story.append(Spacer(1, 10))
    story.append(Paragraph("APPLICATION DISCOVERY &amp;<br/>QA DOCUMENTATION REPORT", title_style))
    story.append(Spacer(1, 15))
    story.append(HRFlowable(width="100%", thickness=3, color=brand_accent, spaceBefore=5, spaceAfter=20))

    meta_text = """
    <b>Repository GitHub:</b> https://github.com/ilhamihsan201-source/sport-booking<br/>
    <b>Production URL:</b> https://sport-booking-production-0df4977f.up.railway.app<br/>
    <b>Framework &amp; Stack:</b> Laravel 11.x, PHP 8.2+, MySQL 8, Tailwind CSS, Vite<br/>
    <b>Status Audit QA:</b> <font color="#16a34a"><b>PASSED (67/67 Tests, 200 Assertions - 100% Success)</b></font><br/>
    <b>Disusun Oleh:</b> Principal QA &amp; Software Documentation Architect<br/>
    <b>Tanggal Audit:</b> 24 November 2026
    """
    story.append(Paragraph(meta_text, body_style))
    story.append(Spacer(1, 30))

    exec_summary_box = [
        [Paragraph("<b>RINGKASAN EKSEKUTIF (EXECUTIVE SUMMARY)</b>", table_header)],
        [Paragraph(
            "Dokumen ini disusun berdasarkan standar <i>skill-application-discovery-qa-documentation</i> "
            "untuk memetakan seluruh karakteristik aplikasi <b>SportBooking Indonesia</b>, mencakup penemuan halaman (Page Discovery), "
            "inventarisasi fitur (Feature Inventory), model peran pengguna (Role Permission Matrix), diagram alur proses bisnis (Application Flow), "
            "spesifikasi aturan bisnis (Business Rules), hingga matriks pengujian kualitas (QA Requirement Specification &amp; Automated Test Results). "
            "Semua pengujian fungsional dan proteksi keamanan (IDOR, Double Booking Prevention, RBAC, dan Validasi Status Finansial) "
            "telah teruji secara empiris dengan hasil <b>100% PASS</b>.", table_cell)]
    ]
    t_summary = Table(exec_summary_box, colWidths=[515])
    t_summary.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), primary_color),
        ('BACKGROUND', (0,1), (-1,1), colors.HexColor("#f8fafc")),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#cbd5e1")),
        ('TOPPADDING', (0,0), (-1,-1), 8),
        ('BOTTOMPADDING', (0,0), (-1,-1), 8),
        ('LEFTPADDING', (0,0), (-1,-1), 10),
        ('RIGHTPADDING', (0,0), (-1,-1), 10),
    ]))
    story.append(t_summary)

    story.append(PageBreak())

    # ------------------ SECTION 1: PROFIL & KLASIFIKASI PROYEK ------------------
    story.append(Paragraph("1. PROFIL &amp; KLASIFIKASI SISTEM (PROJECT UNDERSTANDING)", h1_style))
    story.append(HRFlowable(width="100%", thickness=1, color=colors.HexColor("#e2e8f0"), spaceBefore=2, spaceAfter=10))

    project_info = [
        [Paragraph("<b>Atribut Proyek</b>", table_header), Paragraph("<b>Deskripsi Aktual Sistem</b>", table_header)],
        [Paragraph("<b>Nama Proyek</b>", table_cell_bold), Paragraph("SportBooking — Sistem Reservasi &amp; Manajemen Lapangan Olahraga", table_cell)],
        [Paragraph("<b>Kategori Aplikasi</b>", table_cell_bold), Paragraph("Booking &amp; Reservation System / Sports Facility Management", table_cell)],
        [Paragraph("<b>Tujuan Utama</b>", table_cell_bold), Paragraph("Memfasilitasi pelanggan menyewa lapangan olahraga secara digital dan real-time serta memberi admin alat verifikasi pembayaran dan manajemen venue.", table_cell)],
        [Paragraph("<b>Arsitektur Sistem</b>", table_cell_bold), Paragraph("Monolith MVC berbasis Laravel 11, Blade Templates, RESTful routing, Tailwind CSS, SQLite (Testing) &amp; MySQL (Production).", table_cell)],
        [Paragraph("<b>Deployment</b>", table_cell_bold), Paragraph("Railway Cloud PaaS (Dockerized container) + MySQL Database Service", table_cell)],
        [Paragraph("<b>Model Autentikasi</b>", table_cell_bold), Paragraph("Session-based Authentication via Laravel Breeze dengan Role-Based Access Control (Admin vs Customer).", table_cell)],
    ]
    t_proj = Table(project_info, colWidths=[140, 375])
    t_proj.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), primary_color),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#cbd5e1")),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [colors.white, colors.HexColor("#f8fafc")]),
        ('TOPPADDING', (0,0), (-1,-1), 5),
        ('BOTTOMPADDING', (0,0), (-1,-1), 5),
        ('LEFTPADDING', (0,0), (-1,-1), 6),
        ('RIGHTPADDING', (0,0), (-1,-1), 6),
    ]))
    story.append(t_proj)
    story.append(Spacer(1, 15))

    # ------------------ SECTION 2: APPLICATION & PAGE DISCOVERY ------------------
    story.append(Paragraph("2. INVENTARISASI HALAMAN AKTUAL (PAGE DISCOVERY)", h1_style))
    story.append(HRFlowable(width="100%", thickness=1, color=colors.HexColor("#e2e8f0"), spaceBefore=2, spaceAfter=10))

    pages_data = [
        [Paragraph("<b>Page ID</b>", table_header), Paragraph("<b>Nama Halaman</b>", table_header), Paragraph("<b>Route / URL</b>", table_header), Paragraph("<b>Role Akses</b>", table_header), Paragraph("<b>Fungsi Utama</b>", table_header)],
        [Paragraph("PG-01", table_cell_bold), Paragraph("Beranda / Landing", table_cell), Paragraph("<code>/</code>", table_cell), Paragraph("Public", table_cell), Paragraph("Showcase lapangan, hero banner, call-to-action reservasi.", table_cell)],
        [Paragraph("PG-02", table_cell_bold), Paragraph("Katalog Arena", table_cell), Paragraph("<code>/arena</code>, <code>/arena/{sport}</code>", table_cell), Paragraph("Public", table_cell), Paragraph("Katalog cabang olahraga (Padel, Mini Soccer, dll) &amp; lapangan.", table_cell)],
        [Paragraph("PG-03", table_cell_bold), Paragraph("Detail Lapangan", table_cell), Paragraph("<code>/lapangan/{court}</code>", table_cell), Paragraph("Public", table_cell), Paragraph("Galeri foto lapangan, spesifikasi teknis, harga/jam, fasilitas.", table_cell)],
        [Paragraph("PG-04", table_cell_bold), Paragraph("Jadwal &amp; Slot", table_cell), Paragraph("<code>/jadwal</code>, <code>/jadwal/availability</code>", table_cell), Paragraph("Public", table_cell), Paragraph("Pemeriksaan ketersediaan slot jam main secara real-time.", table_cell)],
        [Paragraph("PG-05", table_cell_bold), Paragraph("Form Reservasi", table_cell), Paragraph("<code>POST /booking</code>", table_cell), Paragraph("Customer", table_cell), Paragraph("Pembuatan booking dengan pencegahan tabrakan jadwal (double-booking).", table_cell)],
        [Paragraph("PG-06", table_cell_bold), Paragraph("Detail Booking", table_cell), Paragraph("<code>/booking/{code}</code>", table_cell), Paragraph("Customer", table_cell), Paragraph("Ringkasan pesanan, rincian biaya, tombol pembatalan mandiri.", table_cell)],
        [Paragraph("PG-07", table_cell_bold), Paragraph("Pembayaran", table_cell), Paragraph("<code>/booking/{code}/pembayaran</code>", table_cell), Paragraph("Customer", table_cell), Paragraph("Instruksi transfer bank &amp; upload bukti transfer (JPG/PNG/PDF).", table_cell)],
        [Paragraph("PG-08", table_cell_bold), Paragraph("Customer Dashboard", table_cell), Paragraph("<code>/customer/dashboard</code>", table_cell), Paragraph("Customer", table_cell), Paragraph("Histori booking aktif, booking selesai, dan status verifikasi bayar.", table_cell)],
        [Paragraph("PG-09", table_cell_bold), Paragraph("Admin Dashboard", table_cell), Paragraph("<code>/admin/dashboard</code>", table_cell), Paragraph("Admin", table_cell), Paragraph("Statistik booking harian, total omset, status pembayaran.", table_cell)],
        [Paragraph("PG-10", table_cell_bold), Paragraph("Admin Kelola Booking", table_cell), Paragraph("<code>/admin/bookings</code>", table_cell), Paragraph("Admin", table_cell), Paragraph("Filter status booking, pembatalan admin, dan penyelesaian sesi.", table_cell)],
        [Paragraph("PG-11", table_cell_bold), Paragraph("Admin Verifikasi Bayar", table_cell), Paragraph("<code>/admin/payments</code>", table_cell), Paragraph("Admin", table_cell), Paragraph("Verifikasi approval atau penolakan bukti bayar dengan alasan.", table_cell)],
        [Paragraph("PG-12", table_cell_bold), Paragraph("Admin Lapangan", table_cell), Paragraph("<code>/admin/courts</code>", table_cell), Paragraph("Admin", table_cell), Paragraph("CRUD lapangan, upload foto multiple, toggle aktif/nonaktif.", table_cell)],
        [Paragraph("PG-13", table_cell_bold), Paragraph("Admin Venue &amp; Fasilitas", table_cell), Paragraph("<code>/admin/venues</code>, <code>facilities</code>", table_cell), Paragraph("Admin", table_cell), Paragraph("Pengelolaan lokasi arena, jam operasional, dan master fasilitas.", table_cell)],
    ]
    t_pages = Table(pages_data, colWidths=[45, 95, 120, 55, 200])
    t_pages.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), primary_color),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#cbd5e1")),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [colors.white, colors.HexColor("#f8fafc")]),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
        ('LEFTPADDING', (0,0), (-1,-1), 4),
        ('RIGHTPADDING', (0,0), (-1,-1), 4),
    ]))
    story.append(t_pages)

    story.append(PageBreak())

    # ------------------ SECTION 3: ROLE PERMISSION MATRIX ------------------
    story.append(Paragraph("3. ROLE PERMISSION MATRIX (ACCESS CONTROL)", h1_style))
    story.append(HRFlowable(width="100%", thickness=1, color=colors.HexColor("#e2e8f0"), spaceBefore=2, spaceAfter=10))

    perm_data = [
        [Paragraph("<b>Fitur &amp; Kemampuan Sistem</b>", table_header), Paragraph("<b>Public (Guest)</b>", table_header), Paragraph("<b>Customer</b>", table_header), Paragraph("<b>Administrator</b>", table_header)],
        [Paragraph("Melihat Katalog Lapangan &amp; Galeri", table_cell), Paragraph("<font color='#16a34a'><b>ALLOWED</b></font>", table_cell), Paragraph("<font color='#16a34a'><b>ALLOWED</b></font>", table_cell), Paragraph("<font color='#16a34a'><b>ALLOWED</b></font>", table_cell)],
        [Paragraph("Cek Ketersediaan Jam / Slot Real-time", table_cell), Paragraph("<font color='#16a34a'><b>ALLOWED</b></font>", table_cell), Paragraph("<font color='#16a34a'><b>ALLOWED</b></font>", table_cell), Paragraph("<font color='#16a34a'><b>ALLOWED</b></font>", table_cell)],
        [Paragraph("Membuat Booking Baru", table_cell), Paragraph("<font color='#dc2626'><b>DENIED (Harus Login)</b></font>", table_cell), Paragraph("<font color='#16a34a'><b>ALLOWED</b></font>", table_cell), Paragraph("<font color='#dc2626'><b>N/A (via Customer)</b></font>", table_cell)],
        [Paragraph("Upload Bukti Pembayaran", table_cell), Paragraph("<font color='#dc2626'><b>DENIED</b></font>", table_cell), Paragraph("<font color='#16a34a'><b>ALLOWED (Booking Milik Sendiri)</b></font>", table_cell), Paragraph("<font color='#dc2626'><b>DENIED</b></font>", table_cell)],
        [Paragraph("Melihat Booking Pengguna Lain (IDOR Protection)", table_cell), Paragraph("<font color='#dc2626'><b>BLOCKED (403/404)</b></font>", table_cell), Paragraph("<font color='#dc2626'><b>BLOCKED (403 Forbidden)</b></font>", table_cell), Paragraph("<font color='#16a34a'><b>ALLOWED (Supervisi)</b></font>", table_cell)],
        [Paragraph("Membatalkan Booking Mandiri (Status Pending)", table_cell), Paragraph("<font color='#dc2626'><b>DENIED</b></font>", table_cell), Paragraph("<font color='#16a34a'><b>ALLOWED</b></font>", table_cell), Paragraph("<font color='#16a34a'><b>ALLOWED</b></font>", table_cell)],
        [Paragraph("Verifikasi / Tolak Pembayaran", table_cell), Paragraph("<font color='#dc2626'><b>DENIED (403)</b></font>", table_cell), Paragraph("<font color='#dc2626'><b>DENIED (403)</b></font>", table_cell), Paragraph("<font color='#16a34a'><b>ALLOWED</b></font>", table_cell)],
        [Paragraph("Akses Dashboard Admin (/admin/*)", table_cell), Paragraph("<font color='#dc2626'><b>DENIED (Redirect Login)</b></font>", table_cell), Paragraph("<font color='#dc2626'><b>DENIED (403 Forbidden)</b></font>", table_cell), Paragraph("<font color='#16a34a'><b>ALLOWED</b></font>", table_cell)],
        [Paragraph("Manajemen Lapangan, Venue, Fasilitas", table_cell), Paragraph("<font color='#dc2626'><b>DENIED</b></font>", table_cell), Paragraph("<font color='#dc2626'><b>DENIED</b></font>", table_cell), Paragraph("<font color='#16a34a'><b>ALLOWED</b></font>", table_cell)],
        [Paragraph("Hapus Lapangan yang Memiliki Riwayat Booking", table_cell), Paragraph("<font color='#dc2626'><b>DENIED</b></font>", table_cell), Paragraph("<font color='#dc2626'><b>DENIED</b></font>", table_cell), Paragraph("<font color='#dc2626'><b>PROTECTED (Ditolak Sistem)</b></font>", table_cell)],
    ]
    t_perm = Table(perm_data, colWidths=[180, 105, 120, 110])
    t_perm.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), primary_color),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#cbd5e1")),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [colors.white, colors.HexColor("#f8fafc")]),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(t_perm)
    story.append(Spacer(1, 15))

    # ------------------ SECTION 4: BUSINESS RULES & LIFECYCLE ------------------
    story.append(Paragraph("4. ATURAN BISNIS &amp; SIKLUS HIDUP (BUSINESS RULES)", h1_style))
    story.append(HRFlowable(width="100%", thickness=1, color=colors.HexColor("#e2e8f0"), spaceBefore=2, spaceAfter=8))

    rules_p = """
    Sistem SportBooking mengimplementasikan aturan bisnis ketat (Strict Business Rules) yang telah teruji:
    <br/>
    <b>1. Pencegahan Double-Booking (Concurrency Protection):</b> Kombinasi <code>court_id + booking_date + start_time + end_time</code> diproteksi pada tingkat transaksi database. Apabila ada 2 pelanggan memesan slot yang sama dalam detik bersamaan, pesanan kedua akan otomatis ditolak dengan pesan peringatan ketersediaan.
    <br/>
    <b>2. Masa Berlaku Pembayaran (Payment Expiration):</b> Pesanan berstatus <code>pending</code> memiliki batas waktu bayar (default 2 jam). Command <code>php artisan bookings:expire-pending</code> membatalkan otomatis pesanan yang kedaluwarsa sehingga slot kembali tersedia untuk umum.
    <br/>
    <b>3. Proteksi Akses Data (IDOR Prevention):</b> Customer A tidak dapat mengakses, melihat faktur, maupun membatalkan pesanan milik Customer B. Sistem memvalidasi kepemilikan <code>user_id === booking.user_id</code> sebelum merender halaman.
    <br/>
    <b>4. Integritas Data Lapangan (Referential Integrity):</b> Lapangan yang memiliki riwayat booking (pending, confirmed, atau completed) tidak dapat dihapus permanen oleh Admin untuk melindungi rekonsiliasi audit keuangan. Admin hanya diizinkan melakukan non-aktifkan (deactivate).
    """
    story.append(Paragraph(rules_p, body_style))
    story.append(Spacer(1, 10))

    # ------------------ SECTION 5: AUTOMATED QA RESULTS ------------------
    story.append(Paragraph("5. HASIL PENGUJIAN OTOMATIS (QA TEST RESULTS)", h1_style))
    story.append(HRFlowable(width="100%", thickness=1, color=colors.HexColor("#e2e8f0"), spaceBefore=2, spaceAfter=10))

    qa_summary_table = [
        [Paragraph("<b>Kategori Pengujian</b>", table_header), Paragraph("<b>Jumlah Test</b>", table_header), Paragraph("<b>Assertions</b>", table_header), Paragraph("<b>Hasil</b>", table_header), Paragraph("<b>Durasi</b>", table_header)],
        [Paragraph("Unit Tests (System Integrity)", table_cell_bold), Paragraph("1", table_cell), Paragraph("1", table_cell), Paragraph("<font color='#16a34a'><b>100% PASS</b></font>", table_cell), Paragraph("0.59s", table_cell)],
        [Paragraph("Authentication &amp; RBAC Access Tests", table_cell_bold), Paragraph("9", table_cell), Paragraph("28", table_cell), Paragraph("<font color='#16a34a'><b>100% PASS</b></font>", table_cell), Paragraph("9.49s", table_cell)],
        [Paragraph("Security: Email &amp; Password Management", table_cell_bold), Paragraph("12", table_cell), Paragraph("34", table_cell), Paragraph("<font color='#16a34a'><b>100% PASS</b></font>", table_cell), Paragraph("6.58s", table_cell)],
        [Paragraph("Registration &amp; Profile Integrity", table_cell_bold), Paragraph("7", table_cell), Paragraph("21", table_cell), Paragraph("<font color='#16a34a'><b>100% PASS</b></font>", table_cell), Paragraph("5.95s", table_cell)],
        [Paragraph("Core SportBooking Business &amp; Lifecycle Tests", table_cell_bold), Paragraph("38", table_cell), Paragraph("116", table_cell), Paragraph("<font color='#16a34a'><b>100% PASS</b></font>", table_cell), Paragraph("9.97s", table_cell)],
        [Paragraph("<b>TOTAL KESELURUHAN</b>", table_cell_bold), Paragraph("<b>67 Tests</b>", table_cell_bold), Paragraph("<b>200 Assertions</b>", table_cell_bold), Paragraph("<font color='#16a34a'><b>ALL PASSED</b></font>", table_cell_bold), Paragraph("<b>32.58s</b>", table_cell_bold)],
    ]
    t_qa = Table(qa_summary_table, colWidths=[185, 75, 80, 100, 75])
    t_qa.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), primary_color),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#cbd5e1")),
        ('ROWBACKGROUNDS', (0,1), (-1,-2), [colors.white, colors.HexColor("#f8fafc")]),
        ('BACKGROUND', (0,-1), (-1,-1), colors.HexColor("#e2e8f0")),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(t_qa)

    story.append(PageBreak())

    # ------------------ SECTION 6: DETAILED TEST CASE EVIDENCE ------------------
    story.append(Paragraph("6. MATRIKS SPESIFIKASI TEST CASE &amp; EVIDENCE", h1_style))
    story.append(HRFlowable(width="100%", thickness=1, color=colors.HexColor("#e2e8f0"), spaceBefore=2, spaceAfter=8))

    tc_data = [
        [Paragraph("<b>Test Case ID</b>", table_header), Paragraph("<b>Fitur yang Diuji</b>", table_header), Paragraph("<b>Skenario / Kondisi Uji</b>", table_header), Paragraph("<b>Ekspektasi Sistem</b>", table_header), Paragraph("<b>Status</b>", table_header)],
        [Paragraph("TC-AUTH-01", table_cell_bold), Paragraph("RBAC Admin", table_cell), Paragraph("Customer mencoba akses <code>/admin/dashboard</code>", table_cell), Paragraph("Akses ditolak dengan status HTTP 403 Forbidden.", table_cell), Paragraph("<font color='#16a34a'><b>PASS</b></font>", table_cell)],
        [Paragraph("TC-AUTH-02", table_cell_bold), Paragraph("Smart Redirect", table_cell), Paragraph("Login sebagai Admin lalu buka <code>/dashboard</code>", table_cell), Paragraph("Otomatis diarahkan ke <code>/admin/dashboard</code>.", table_cell), Paragraph("<font color='#16a34a'><b>PASS</b></font>", table_cell)],
        [Paragraph("TC-BOOK-01", table_cell_bold), Paragraph("Booking Flow", table_cell), Paragraph("Customer membuat pesanan lapangan valid", table_cell), Paragraph("Booking tersimpan dengan status <code>pending</code>.", table_cell), Paragraph("<font color='#16a34a'><b>PASS</b></font>", table_cell)],
        [Paragraph("TC-BOOK-02", table_cell_bold), Paragraph("Double Booking", table_cell), Paragraph("Dua pemesanan pada slot &amp; jam yang sama", table_cell), Paragraph("Pemesanan kedua ditolak, integritas jadwal aman.", table_cell), Paragraph("<font color='#16a34a'><b>PASS</b></font>", table_cell)],
        [Paragraph("TC-PAY-01", table_cell_bold), Paragraph("Upload Bukti", table_cell), Paragraph("Customer upload file transfer valid", table_cell), Paragraph("File tersimpan dan status berubah menjadi <code>paid</code>.", table_cell), Paragraph("<font color='#16a34a'><b>PASS</b></font>", table_cell)],
        [Paragraph("TC-PAY-02", table_cell_bold), Paragraph("Verifikasi Admin", table_cell), Paragraph("Admin klik verify bukti bayar", table_cell), Paragraph("Status booking menjadi <code>confirmed</code>, email notifikasi.", table_cell), Paragraph("<font color='#16a34a'><b>PASS</b></font>", table_cell)],
        [Paragraph("TC-PAY-03", table_cell_bold), Paragraph("Penolakan Bayar", table_cell), Paragraph("Admin tolak bukti bayar buram / palsu", table_cell), Paragraph("Status ditolak disertai alasan feedback ke user.", table_cell), Paragraph("<font color='#16a34a'><b>PASS</b></font>", table_cell)],
        [Paragraph("TC-SEC-01", table_cell_bold), Paragraph("IDOR Protection", table_cell), Paragraph("Customer A akses URL booking Customer B", table_cell), Paragraph("Sistem memblokir dengan status HTTP 403.", table_cell), Paragraph("<font color='#16a34a'><b>PASS</b></font>", table_cell)],
        [Paragraph("TC-CRON-01", table_cell_bold), Paragraph("Auto Expiration", table_cell), Paragraph("Menjalankan command <code>bookings:expire-pending</code>", table_cell), Paragraph("Booking lampau dibatalkan, booking masa depan aman.", table_cell), Paragraph("<font color='#16a34a'><b>PASS</b></font>", table_cell)],
        [Paragraph("TC-SAFE-01", table_cell_bold), Paragraph("Delete Guard", table_cell), Paragraph("Admin mencoba delete lapangan dengan transaksi", table_cell), Paragraph("Ditolak sistem, integritas pembukuan terlindungi.", table_cell), Paragraph("<font color='#16a34a'><b>PASS</b></font>", table_cell)],
    ]
    t_tc = Table(tc_data, colWidths=[65, 80, 150, 165, 55])
    t_tc.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), primary_color),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#cbd5e1")),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [colors.white, colors.HexColor("#f8fafc")]),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
        ('LEFTPADDING', (0,0), (-1,-1), 4),
        ('RIGHTPADDING', (0,0), (-1,-1), 4),
    ]))
    story.append(t_tc)
    story.append(Spacer(1, 15))

    # ------------------ SECTION 7: KESIMPULAN & REKOMENDASI ------------------
    story.append(Paragraph("7. KESIMPULAN &amp; SERAH TERIMA QA (HANDOVER)", h1_style))
    story.append(HRFlowable(width="100%", thickness=1, color=colors.HexColor("#e2e8f0"), spaceBefore=2, spaceAfter=8))

    conclusion_p = """
    Berdasarkan seluruh hasil audit aplikasi, inspeksi kode, dan pengeksekusian 67 skenario pengujian otomatis,
    aplikasi <b>SportBooking Indonesia</b> dinyatakan <b>STABIL, AMAN, DAN PRODUCTION-READY</b>.
    <br/><br/>
    <b>Rekomendasi Pemeliharaan Rutin:</b>
    <br/>
    1. Pastikan scheduler cron job di server production (Railway) menjalankan <code>php artisan bookings:expire-pending</code> setiap menit.
    <br/>
    2. Pertahankan backup reguler database MySQL pada dashboard Railway.
    <br/>
    3. Seluruh dokumentasi teknis ini siap digunakan sebagai acuan pengujian otomatis berkelanjutan (CI/CD Pipeline).
    """
    story.append(Paragraph(conclusion_p, body_style))

    # Build the document
    doc.build(story, canvasmaker=NumberedCanvas)
    print(f"Successfully generated PDF: {filename}")

if __name__ == "__main__":
    out_pdf = "QA/SportBooking_Application_Discovery_and_QA_Documentation.pdf"
    create_qa_pdf(out_pdf)
