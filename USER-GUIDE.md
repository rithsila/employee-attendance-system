# Employee Attendance System - User Guide

## Table of Contents
1. [Introduction](#introduction)
2. [System Overview](#system-overview)
3. [User Roles](#user-roles)
4. [Features](#features)
5. [Admin/Operator Guide](#adminoperator-guide)
6. [Employee Guide](#employee-guide)
7. [API Documentation](#api-documentation)

---

## Introduction

The Employee Attendance System is a web-based application built with Laravel 9 for tracking employee attendance. It supports multiple attendance methods including QR code scanning, manual check-in/out, and permission/leave management.

**Default Login Credentials:**
- Email: `admin@gmail.com`
- Password: `password`

---

## System Overview

### Tech Stack
- **Backend**: Laravel 9 (PHP 8.0.2+)
- **Frontend**: Blade templates with Livewire components
- **Database**: MySQL
- **Features**: QR Code generation, PDF export, Excel/CSV export
- **Timezone**: Asia/Jakarta (WIB)

### Core Entities
1. **Users** - Employees, Operators, and Administrators
2. **Positions** - Job positions/roles within the organization
3. **Attendances** - Attendance schedules with time windows
4. **Presences** - Actual check-in/out records
5. **Permissions** - Leave/permission requests
6. **Holidays** - Company holidays

---

## User Roles

The system has three user roles with different access levels:

### 1. Admin (role_id: 1)
**Access:** Full system access
- Dashboard
- Manage Positions (CRUD)
- Manage Employees (CRUD)
- Manage Holidays (CRUD)
- Manage Attendance Schedules (CRUD)
- View Presence Records
- Generate QR Codes
- Export data to Excel/CSV

### 2. Operator (role_id: 2)
**Access:** Administrative functions (same as Admin)
- Dashboard
- Manage Positions
- Manage Employees
- Manage Holidays
- Manage Attendance Schedules
- View Presence Records
- Generate QR Codes
- Export data

### 3. User/Employee (role_id: 3)
**Access:** Self-service functions only
- Home page with available attendances
- Check-in/out (via button or QR code)
- Submit permission/leave requests
- View personal attendance history

---

## Features

### 1. Authentication System
**How it works:**
- Session-based authentication using Laravel's built-in auth system
- Login page accessible at `/login`
- Logout via POST request to `/logout`
- Remember me functionality available

**How to use:**
1. Navigate to the login page
2. Enter email and password
3. Check "Remember me" for persistent login (optional)
4. System redirects based on role:
   - Admin/Operator → Dashboard
   - Employee → Home page

---

### 2. Dashboard
**Access:** Admin, Operator

**Features:**
- Display total positions count
- Display total users count
- Quick navigation to management modules

**How to access:**
- Navigate to `/dashboard` after login

---

### 3. Position Management (Jabatan)
**Access:** Admin, Operator

**Features:**
- Create new positions
- Edit existing positions (single or bulk)
- Delete positions (single or bulk)
- Search and filter positions
- Export to Excel/CSV

**How to use:**

*Create Position:*
1. Go to `/positions`
2. Click "Tambah Data" button
3. Enter position name
4. Click Save

*Edit Position:*
1. Select checkbox next to position(s) in the table
2. Click "Edit" button in table header
3. Or click individual "Edit" link on the row
4. Modify the name
5. Click Update

*Delete Position:*
1. Select checkbox next to position(s)
2. Click "Hapus" (Delete) button
3. Confirm deletion

*Export:*
- Click export button to download Excel or CSV

---

### 4. Employee Management (Karyawan)
**Access:** Admin, Operator

**Features:**
- Create employees with role assignment
- Edit employee details (single or bulk)
- Delete employees (with self-protection - cannot delete logged-in user)
- Search by name, email, phone, position, role
- Filter by position and role
- Export to Excel/CSV

**Employee Fields:**
- Name
- Email
- Password
- Phone number
- Position (dropdown)
- Role (Admin/Operator/User)

**How to use:**

*Create Employee:*
1. Go to `/employees`
2. Click "Tambah Data"
3. Fill in all required fields
4. Select position from dropdown
5. Select role (User for regular employees)
6. Click Save

*Edit Employee:*
1. Select checkbox(es) in the table
2. Click "Edit" button
3. Modify fields as needed
4. Click Update

*Bulk Edit:*
- Select multiple employees
- Click "Edit"
- All selected employees will be editable in the same form

---

### 5. Holiday Management (Hari Libur)
**Access:** Admin, Operator

**Features:**
- Create holidays with date and description
- Edit holidays (single or bulk)
- Delete holidays
- Search and filter
- Export to Excel/CSV

**Holiday Fields:**
- Title (e.g., "Idul Fitri", "Independence Day")
- Description
- Holiday Date

**How it works:**
- Holidays are checked during attendance to prevent check-in on holiday dates
- When an employee attempts to check-in on a holiday, they see a holiday notification

**How to use:**
1. Go to `/holidays`
2. Add new holiday with date and description
3. System automatically blocks attendance on these dates

---

### 6. Attendance Management (Absensi)
**Access:** Admin, Operator

**Features:**
- Create attendance schedules with time windows
- Assign to specific positions
- Generate QR codes for attendance
- Edit schedules
- Delete schedules

**Attendance Schedule Fields:**
- **Title** - Name of the attendance (e.g., "Morning Shift")
- **Description** - Additional details
- **Start Time** - Check-in start time (e.g., 08:00)
- **Batas Start Time** - Check-in deadline (e.g., 09:00)
- **End Time** - Check-out start time (e.g., 17:00)
- **Batas End Time** - Check-out deadline (e.g., 18:00)
- **Code** - Unique code for QR code generation (optional)
- **Positions** - Which job positions can use this attendance

**How it works:**
- Attendance schedules define when employees can check in/out
- Time windows prevent early/late check-ins
- QR codes can be generated for touchless attendance
- Schedules can be assigned to specific positions only

**How to use:**

*Create Attendance Schedule:*
1. Go to `/attendances`
2. Click "Tambah Data"
3. Fill in title and description
4. Set time windows:
   - Start Time: When check-in opens
   - Batas Start Time: When check-in closes
   - End Time: When check-out opens
   - Batas End Time: When check-out closes
5. Enter a unique code (for QR code) - optional
6. Select applicable positions
7. Click Save

*Edit Attendance:*
1. Click "Edit" on the attendance row
2. Modify fields
3. Click Update

---

### 7. QR Code Attendance
**Access:** Admin, Operator (Generate), Employee (Scan)

**Features:**
- Generate QR codes for each attendance schedule
- Download QR code as PDF
- Employees scan QR to check in/out

**How it works:**
1. Admin creates attendance schedule with a unique code
2. System generates QR code containing the code
3. QR code can be printed and placed at entry/exit points
4. Employee scans QR code with their phone
5. System validates:
   - Current time is within check-in/check-out window
   - Employee's position is allowed for this attendance
   - Not a holiday
6. Records presence automatically

**How to use (Admin/Operator):**
1. Go to `/presences`
2. Click "QR Code" button on an attendance
3. View QR code on screen
4. Click "Download PDF" to print

**How to use (Employee):**
1. Log in to the system
2. Go to available attendance
3. Click "Scan QR Code" button
4. Scan the QR code displayed at the entry point
5. System automatically records check-in/out

---

### 8. Presence Management (Kehadiran)
**Access:** Admin, Operator

**Features:**
- View all presence records per attendance
- Search by employee name, date, time
- Filter by date range
- View not-present employees
- View permission requests
- Mark absent employees as present (manual override)
- Accept permission requests
- Export to Excel/CSV

**Presence Record Fields:**
- Employee Name
- Date
- Check-in Time
- Check-out Time
- Status (Hadir/Present or Izin/Permission)

**How to use:**

*View Presence Records:*
1. Go to `/presences`
2. Click on an attendance name
3. See all presence records in the table

*View Not Present Employees:*
1. On the presence page, click "Data Tidak Hadir"
2. Select date to view
3. See list of employees who haven't checked in

*Mark Employee as Present (Manual):*
1. Go to "Data Tidak Hadir"
2. Find the employee
3. Click "Tandai Hadir" button
4. Employee is marked as present for that date

*View Permissions:*
1. Click "Data Izin" on the presence page
2. See all permission requests for that attendance

*Accept Permission:*
1. Go to "Data Izin"
2. Find pending permission request
3. Click "Terima" button
4. Employee is marked as present with "Izin" status

---

### 9. Permission/Leave Requests (Izin)
**Access:** Employee (Request), Admin/Operator (Approve)

**Features:**
- Employees submit permission requests
- Title and description for the request
- Admin/Operator can view and accept requests
- Accepted permissions count as attendance with "Izin" status

**How it works:**
1. Employee submits permission request with reason
2. Request appears in Admin/Operator's permission list
3. Admin reviews and accepts/rejects
4. If accepted, employee is marked present for that day with "Izin" status

**How to use (Employee):**
1. Log in and go to an attendance
2. Click "Ajukan Izin" button
3. Enter title (e.g., "Sakit", "Cuti")
4. Enter description/reason
5. Click Submit
6. Wait for approval

**How to use (Admin/Operator):**
1. Go to `/presences/{attendance}/permissions`
2. View pending requests
3. Click "Terima" to accept
4. Employee is automatically marked present

---

### 10. Employee Self-Service (Home)
**Access:** Employee

**Features:**
- View available attendances (filtered by position)
- Check-in/out with button click
- Check-in/out with QR code
- Submit permission requests
- View attendance history (last 30 days)
- View holidays

**How to use:**

*Check-in:*
1. Log in as employee
2. Home page shows available attendances
3. Click on an attendance
4. If within check-in time window, "Absen Masuk" button is active
5. Click the button to check in

*Check-out:*
1. After checking in, "Absen Pulang" button becomes active
2. Click to check out
3. Check-out time is recorded

*View History:*
- On attendance detail page, scroll to "Riwayat Absensi"
- See last 30 days of attendance records
- Green = Present, Red = Not Present

---

## Admin/Operator Guide

### Daily Workflow
1. **Login** to the system
2. **Check Dashboard** for overview
3. **Manage Attendances** - Create/edit schedules as needed
4. **Generate QR Codes** - For QR-based attendance
5. **Monitor Presences** - Check who is present/absent
6. **Handle Permissions** - Approve/reject leave requests

### Managing Attendance Records
- Presence records are created automatically when employees check in
- Use "Data Tidak Hadir" to find employees who missed attendance
- Use "Tandai Hadir" to manually mark employees as present if needed

### Exporting Data
All tables support export to:
- Excel (.xlsx)
- CSV (.csv)

Click the export button on any table to download.

### Bulk Operations
Most tables support bulk operations:
1. Select multiple rows using checkboxes
2. Use header buttons to:
   - Delete selected records
   - Edit selected records (opens bulk edit form)

---

## Employee Guide

### Daily Attendance Workflow
1. **Login** to the system
2. **View Home Page** - See available attendances
3. **Select Attendance** - Click on the attendance card
4. **Check-in** - Click "Absen Masuk" or scan QR code
5. **Work**
6. **Check-out** - Click "Absen Pulang" or scan QR code

### When You Can't Check In
If the check-in button is disabled, possible reasons:
- **Not within time window** - Check the displayed time range
- **Holiday** - Check if today is a company holiday
- **Already checked in** - You may have already recorded attendance
- **Pending permission** - You have a pending permission request

### Requesting Permission/Leave
1. Go to the attendance page
2. Click "Ajukan Izin"
3. Fill in the form:
   - Title: Brief reason (e.g., "Sakit", "Cuti Tahunan")
   - Description: Detailed explanation
4. Submit and wait for approval

### Viewing Your History
- On any attendance page, scroll to "Riwayat Absensi"
- See the last 30 days
- Dates marked green = Present
- Dates marked red = Absent

---

## API Documentation

### Authentication
The API uses Laravel Sanctum for authentication.

### Endpoints

#### Get Permission Detail
```
GET /api/permissions/detail
```

**Query Parameters:**
- (Add parameters as needed based on implementation)

**Response:**
```json
{
  "data": {
    // Permission details
  }
}
```

### QR Code Endpoints (Internal)

#### Check-in via QR Code
```
POST /absensi/qrcode
```

**Body:**
```json
{
  "code": "unique-attendance-code"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Kehadiran atas nama 'John Doe' berhasil dikirim."
}
```

#### Check-out via QR Code
```
POST /absensi/qrcode/out
```

**Body:**
```json
{
  "code": "unique-attendance-code"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Atas nama 'John Doe' berhasil melakukan absensi pulang."
}
```

---

## Troubleshooting

### Common Issues

**Cannot Login**
- Check email and password
- Ensure account exists in the system
- Contact admin if account is locked

**Attendance Button Disabled**
- Check if current time is within the attendance window
- Check if today is a holiday
- Check if you already checked in

**QR Code Not Working**
- Ensure QR code is for the correct attendance
- Check if attendance time window is active
- Ensure your position is assigned to the attendance

**Cannot Delete Employee**
- You cannot delete your own account while logged in
- Check if employee has associated presence records

**Export Not Working**
- Check browser pop-up blocker
- Ensure you have permission to export

### Error Messages

| Message | Cause | Solution |
|---------|-------|----------|
| "Kamu tidak memiliki izin" | Unauthorized access | Login with appropriate role |
| "Data gagal dihapus" | Foreign key constraint | Delete related records first |
| "Login gagal" | Invalid credentials | Check email/password |
| "Terjadi masalah pada saat melakukan absensi" | Time window issue or already checked in | Check time and existing records |

---

## Best Practices

### For Admins/Operators
1. **Set Clear Time Windows** - Define realistic check-in/out times
2. **Regular Backups** - Export data regularly
3. **Monitor Absences** - Check "Data Tidak Hadir" daily
4. **Approve Permissions Promptly** - Don't leave employees waiting
5. **Test QR Codes** - Verify QR codes work before deployment

### For Employees
1. **Check in Early** - Don't wait until the last minute
2. **Request Permission in Advance** - When possible, request leave beforehand
3. **Keep Login Secure** - Don't share your credentials
4. **Verify Check-out** - Ensure you properly check out each day

---

## Support

For technical issues or feature requests, contact the system administrator.

---

*Document Version: 1.0*
*Last Updated: March 2026*
