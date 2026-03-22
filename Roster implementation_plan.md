# Roster & Attendance Features Implementation Plan

## Goal Description
Enhance the existing Employee Attendance System to support a 24/7 shop environment. This includes implementing a personalized roster system, handling individual weekly days off, enabling staff-to-staff day-off/shift swaps, and calculating total working hours.

> [!IMPORTANT]
> ## User Review Required
> The proposed plan introduces fundamental changes to how attendance is validated. Currently, attendance is tied to "Positions". The new approach ties attendance to personalized "Rosters/Shifts" for each employee on specific dates.
> Please review the core features below to ensure they align directly with your shop's operational needs.

## Proposed Features & Changes

### 1. Database & Models
#### [NEW] `Shift` Model & Migration
Defines standard shift templates (e.g., Morning Shift: 08:00 - 16:00, Night Shift: 16:00 - 00:00).
- Columns: `name`, `start_time`, `end_time`

#### [NEW] `Roster` Model & Migration
Assigns an employee to a shift or marks a day as an "Off Day" for specific dates.
- Columns: `user_id`, `date`, `shift_id` (nullable if Off Day), `is_off_day` (boolean)

#### [NEW] `ShiftSwap` Model & Migration
Manages the lifecycle of a shift or day-off swap between two employees.
- Columns: `requester_id`, `requested_roster_id`, `target_user_id`, `target_roster_id`, `status` (pending_employee_approval, pending_admin_approval, approved, rejected)

#### [MODIFY] `Presence` Model & Migration
- Add `total_hours` (decimal or time) to store the calculated working hours for the day.

---

### 2. Backend Logic (Controllers & Services)
#### [NEW] `RosterController` / Livewire Component
- For Admins/Operators to generate and manage weekly rosters for all staff.
- Ensure validation that every staff member gets at least 1 "Off Day" per week.

#### [NEW] `ShiftSwapController` / Livewire Component
- **Employee Action:** Request a swap by selecting their roster day and a colleague's roster day.
- **Colleague Action:** Accept or Decline the swap.
- **Admin Action:** Final approval. Automatically updates the `Roster` records.

#### [MODIFY] `PresenceController` (Check-in/Check-out Logic)
- **Validation Update:** Check the employee's `Roster` for today to determine their allowed check-in/out window, rather than checking the global `Attendance` schedule. Block check-in if it is their "Off Day".
- **Hours Calculation:** Upon successful check-out, calculate the time difference between check-in and check-out and save it to `total_hours`.

---

### 3. Frontend Views (UI/UX)
#### [NEW] Admin/Operator Roster Dashboard
- Calendar or weekly table view to assign shifts and off-days.
- Interface to review and approve `ShiftSwap` requests.
- Staff Working Hours Report.

#### [NEW] Employee Roster & Swap Interface
- View personalized weekly schedule.
- Request shift/day-off swaps with other staff.
- View pending swap requests from others.

#### [MODIFY] Employee Home Page
- Display "Total Working Hours" (Weekly/Monthly summary).
- Show the shift assigned for today (or "Off Day" status).

## Verification Plan

### Automated Tests
- Write PHPUnit feature tests for the Shift Swap lifecycle (Request -> Accept -> Approve).
- Write PHPUnit tests for the Check-out calculation to ensure `total_hours` is computed correctly.

### Manual Verification
1. **Roster Creation:** Log in as Admin, create shifts, and assign a weekly roster to an employee (including 1 off day).
2. **Attendance Validation:** Attempt to check-in as the employee on an "Off Day" (should fail). Attempt to check-in during the assigned shift (should succeed).
3. **Hours Tracking:** Complete a check-in and check-out cycle, and verify the `total_hours` field is populated and displayed correctly on the dashboard.
4. **Swap Process:** 
   - Employee A requests to swap their Tuesday Off Day with Employee B's Wednesday Shift.
   - Employee B logs in and accepts.
   - Admin logs in and approves.
   - Verify the Roster is updated correctly for both employees.
