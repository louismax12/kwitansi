# Prompt Design UI untuk Stitch AI / v0 / Generative UI AI

Gunakan prompt di bawah ini untuk Anda berikan kepada Stitch AI (atau AI UI Generator lainnya) agar dapat men-generate ulang desain UI sistem RKZ Billing dengan tampilan yang jauh lebih modern, responsif, dan profesional.

---

**Copy teks di bawah ini ke dalam Stitch AI:**

```text
Act as an expert Frontend Developer and UI/UX Designer. I want you to design a modern, clean, and highly usable responsive web application UI for a hospital/clinical billing system named "RKZ Billing" (or "PRM RKZ Clinical Management").

### Tech Stack & Styling Preferences
- Use React (Next.js preferred) or plain HTML/TailwindCSS depending on what you output.
- Use Tailwind CSS for all styling.
- Use Lucide Icons or FontAwesome for icons.
- Design aesthetic: Clean, modern, medical/corporate vibe. Primary color should be a trustworthy medical blue (e.g., #2271e7), with soft grays for backgrounds and subtle shadows for cards. Clean typography (Inter or Roboto).
- Layout: A persistent sidebar navigation on the left and a top navbar for user profile and notifications. The main content area should be on the right.

### Core Layout Structure
1. **Sidebar Navigation**:
   - Logo area at the top: "PRM RKZ" with a subtitle "Clinical Management".
   - Links: Dashboard, Buat Invoice, Client Management, Master Kategori, Invoices (History), Settings, Logout.
   - Active states should be clearly highlighted with the primary blue color.

2. **Top Navbar**:
   - Search bar (optional).
   - Bell icon for Notifications (with a red dot indicator for unread). When clicked, it shows a dropdown of recent invoices.
   - User Profile dropdown (Avatar, Admin Name) with "Edit Profile" and "Logout" options.

### Pages to Generate (Please create the layout with these main views in mind):

**1. Dashboard Page**
- **Stats Cards**: 3-4 cards showing "Total Pendapatan (Bulan Ini)", "Total Invoices", "Pasien Aktif". Use large typography for numbers and small trend indicators (e.g., "+5% this month").
- **Quick Actions**: Prominent buttons for "Buat Tagihan Baru" and "Tambah Klien".
- **Recent Invoices Table**: A sleek table showing the last 5 transactions (ID, Date, Patient Name, Total Amount, Status with badge styling).

**2. Buat Invoice (Create Kwitansi) Page**
- A two-column or card-based layout for data entry.
- **Header Card**: Hospital info at the top (Rumah Sakit Katolik St. Vincentius a Paulo).
- **Patient Info Section**: Input fields for "Telah terima dari" (Patient Name), "Uang Sejumlah" (Amount in words/terbilang), "Untuk Pembayaran" (Purpose), and "Keterangan" (Notes).
- **Detail Pembayaran (Items) Section**: A dynamic table where users can add multiple rows. Each row has:
  - A dropdown for "Kategori" (fetching from Master Kategori).
  - A text input for "Nama detail (wajib)".
  - A number input for "Jumlah (Rp)".
  - A delete/remove row button.
- **Footer**: Total Amount calculated automatically, and a primary "Simpan & Cetak Invoice" button.

**3. Master Kategori Page**
- A simple data management page.
- A card at the top with an input field "Nama Kategori Baru" and a "Tambah" button.
- A table listing existing categories with columns: "ID Kategori" and "Nama Kategori / Tindakan". 
- Styling should be minimal and readable, using badge styles for the ID.

### Interaction & UX Details
- Use subtle hover effects on buttons and table rows.
- Form inputs should have clear focus states (blue ring).
- Use proper padding/margins (spacious design).
- Data tables should look clean without heavy borders, using alternating background colors or simple bottom borders.

Please generate the complete HTML/Tailwind code or React components for this layout. Ensure the sidebar is collapsible on mobile devices.
```
