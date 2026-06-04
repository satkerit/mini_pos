# BDD_CoffeePOS.md

# BUSINESS DESIGN DOCUMENT (BDD)

## Project Information

| Item          | Value                    |
| ------------- | ------------------------ |
| Project Name  | CoffeePOS                |
| Version       | 1.0                      |
| Document Type | Business Design Document |
| Platform      | Web Based                |
| Industry      | Food & Beverage          |
| Prepared By   | Satker IT                |
| Date          | June 2026                |

---

# 1. Background

Coffee shop membutuhkan sistem Point of Sale yang mampu mengelola transaksi penjualan, inventori bahan baku, perhitungan HPP, pembayaran digital, serta monitoring multi cabang secara real-time.

Sistem dirancang berbasis web agar dapat diakses melalui smartphone, tablet, maupun desktop tanpa instalasi aplikasi tambahan.

---

# 2. Business Objectives

## Primary Objectives

- Mempercepat transaksi penjualan
- Mengurangi kesalahan pencatatan stok
- Mengetahui profit setiap produk
- Monitoring seluruh cabang secara real-time
- Mendukung pembayaran cashless

---

# 3. Business Scope

## Included

### Inventory Management

- Stock In
- Stock Out
- Stock Adjustment
- Stock Opname

### Product Management

- Menu Coffee
- Menu Non Coffee
- Snack
- Dessert

### Sales Management

- POS Cashier
- Barcode Sales
- Receipt Printing

### Financial

- HPP Calculation
- Sales Reporting
- Profit Analysis

### Payment

- Cash
- QRIS Dynamic
- E-Wallet
- Virtual Account

### Administration

- Multi Branch
- User Management
- Role Management

---

# 4. Stakeholders

## Owner

Mengawasi seluruh operasional bisnis.

## Branch Manager

Mengelola operasional cabang.

## Cashier

Melakukan transaksi penjualan.

## Barista

Memproses pesanan pelanggan.

## Administrator

Mengelola konfigurasi sistem.

---

# 5. Business Process

Customer Order

↓

Cashier Input Order

↓

System Calculate Total

↓

Payment Processing

↓

Order Sent To Kitchen/Barista

↓

Inventory Deduction

↓

Receipt Generated

↓

Sales Recorded

---

# 6. Success Metrics

- Waktu transaksi < 10 detik
- Akurasi stok > 95%
- Downtime < 1%
- Monitoring multi cabang real-time
- QRIS berhasil > 99%
