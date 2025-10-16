# 🎨 UI Mockup - Vendor Collaboration Integration

## Overview
This document provides visual mockups and descriptions for integrating vendor collaboration features into the Landscape Grooming Web application.

---

## 📱 Navigation Structure

### Current Navigation (Staff Panel)
```
┌─────────────────────────────────────────────────────────────┐
│  🌿 Landscape Grooming                    [Staff Dashboard] │
│                                           [My Bookings]      │
│                                           [Schedule]         │
│                                           [Logout]           │
└─────────────────────────────────────────────────────────────┘
```

### Enhanced Navigation (With Vendor Features)
```
┌─────────────────────────────────────────────────────────────┐
│  🌿 Landscape Grooming                    [Staff Dashboard] │
│                                           [My Bookings]      │
│                                           [Schedule]         │
│                                           [Vendors] ⭐ NEW   │
│                                           [Performance]      │
│                                           [Logout]           │
└─────────────────────────────────────────────────────────────┘
```

---

## 🏠 Staff Dashboard - Enhanced Layout

```
┌─────────────────────────────────────────────────────────────────────────┐
│  👨‍🔧 Staff Dashboard                      [Dashboard] [Bookings] [Vendors]│
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐  │
│  │   Today's    │ │   This Week  │ │   Completed  │ │   Revenue    │  │
│  │   Jobs       │ │   Jobs       │ │   This Month │ │   This Month │  │
│  │      3       │ │      8       │ │      24      │ │   $2,450     │  │
│  └──────────────┘ └──────────────┘ └──────────────┘ └──────────────┘  │
│                                                                          │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │  🤝 Vendor Collaborations (Active)                       [View All]│  │
│  ├──────────────────────────────────────────────────────────────────┤  │
│  │  📦 Green Supply Co.          Role: Participant   🟢 Active      │  │
│  │  └─ Last activity: 2 hours ago                                   │  │
│  │                                                                   │  │
│  │  🏗️ Equipment Rentals Ltd.    Role: Manager      🟢 Active      │  │
│  │  └─ Last activity: 15 minutes ago    [Join Session] →           │  │
│  │                                                                   │  │
│  │  🌱 Organic Fertilizers Inc.  Role: Participant   ⚪ Idle        │  │
│  │  └─ Last activity: 3 days ago                                    │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│                                                                          │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │  📅 Today's Schedule                                              │  │
│  ├──────────────────────────────────────────────────────────────────┤  │
│  │  09:00 AM - Lawn Mowing (John Smith)                  [Details] │  │
│  │  11:30 AM - Garden Design (Sarah Johnson)             [Details] │  │
│  │  02:00 PM - Tree Trimming (Mike Davis)                [Details] │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 🤝 Vendors List Page (`/staff/vendors`)

```
┌─────────────────────────────────────────────────────────────────────────┐
│  🤝 My Vendor Collaborations            [Dashboard] [Bookings] [Vendors]│
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  [+ Invite New Vendor]                          [Search vendors...]  🔍 │
│                                                                          │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │  Filters: [All] [Active Sessions] [My Owned] [Collaborating]     │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│                                                                          │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │  📦 Green Supply Co.                              🟢 Active       │  │
│  │  ├─ Owner: John Doe (john@example.com)                           │  │
│  │  ├─ Your Role: Participant                                       │  │
│  │  ├─ Collaborators: 5 members                                     │  │
│  │  ├─ Active Session: Yes (Started 2h ago by Jane Smith)           │  │
│  │  └─ Actions: [Join Session] [View Details] [Leave]               │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│                                                                          │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │  🏗️ Equipment Rentals Ltd.                       🟢 Active       │  │
│  │  ├─ Owner: You                                                    │  │
│  │  ├─ Your Role: Owner                                             │  │
│  │  ├─ Collaborators: 3 members                                     │  │
│  │  ├─ Active Session: Yes (Started 15m ago by You)                 │  │
│  │  └─ Actions: [Join Session] [Manage] [Invite Members]            │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│                                                                          │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │  🌱 Organic Fertilizers Inc.                      ⚪ Idle         │  │
│  │  ├─ Owner: Sarah Connor (sarah@example.com)                      │  │
│  │  ├─ Your Role: Manager                                           │  │
│  │  ├─ Collaborators: 8 members                                     │  │
│  │  ├─ Active Session: No                                           │  │
│  │  └─ Actions: [Start Session] [View Details] [Manage Members]     │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│                                                                          │
│                        [1] [2] [3] ... [10] Next →                      │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 📋 Vendor Details Page (`/staff/vendors/{id}`)

```
┌─────────────────────────────────────────────────────────────────────────┐
│  ← Back to Vendors                      [Dashboard] [Bookings] [Vendors]│
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  🏗️ Equipment Rentals Ltd.                                              │
│  ═══════════════════════════════════════════════════════════════════   │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │  Vendor Information                                      [Edit] ✏️  │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │  Name:         Equipment Rentals Ltd.                              │ │
│  │  Email:        contact@equipmentrentals.com                        │ │
│  │  Phone:        +1 (555) 123-4567                                   │ │
│  │  Website:      https://equipmentrentals.com                        │ │
│  │  Address:      123 Industrial Ave, City, State 12345               │ │
│  │  Status:       🟢 Active                                           │ │
│  │  Owner:        You (staff@landscape.test)                          │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │  👥 Collaborators (3)                      [+ Invite Member]       │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │  ┌──────────────────────────────────────────────────────────────┐ │ │
│  │  │  👤 Staff User (You)                              🔑 Owner    │ │ │
│  │  │  └─ staff@landscape.test                                      │ │ │
│  │  └──────────────────────────────────────────────────────────────┘ │ │
│  │                                                                    │ │
│  │  ┌──────────────────────────────────────────────────────────────┐ │ │
│  │  │  👤 John Smith                                    ⚙️ Manager  │ │ │
│  │  │  └─ john@example.com          [Change Role ▼] [Remove]       │ │ │
│  │  └──────────────────────────────────────────────────────────────┘ │ │
│  │                                                                    │ │
│  │  ┌──────────────────────────────────────────────────────────────┐ │ │
│  │  │  👤 Jane Doe                                  👥 Participant  │ │ │
│  │  │  └─ jane@example.com           [Change Role ▼] [Remove]      │ │ │
│  │  └──────────────────────────────────────────────────────────────┘ │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │  🔴 Live Collaboration Session                                     │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │  Status: 🟢 Active Session                                        │ │
│  │  Started: 15 minutes ago by You                                   │ │
│  │  Session ID: abc123-def456-ghi789                                 │ │
│  │                                                                    │ │
│  │  Active Participants (2):                                         │ │
│  │  • You (staff@landscape.test) - editing...                        │ │
│  │  • John Smith (john@example.com) - viewing                        │ │
│  │                                                                    │ │
│  │  [Join Session] [End Session]                                     │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │  📜 Recent Activity & Revisions                          [View All]│ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │  • 15 min ago - You updated vendor description                    │ │
│  │  • 2 hours ago - John Smith changed phone number                  │ │
│  │  • 1 day ago - You added Jane Doe as collaborator                 │ │
│  │  • 3 days ago - Jane Doe updated address                          │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 🔴 Live Collaboration Session View

```
┌─────────────────────────────────────────────────────────────────────────┐
│  🔴 LIVE: Equipment Rentals Ltd.                          [End Session] │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │  👥 Active Participants (2)                                        │ │
│  │  • 🟢 You - editing description                                    │ │
│  │  • 🟢 John Smith - viewing                                         │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │  ✏️ Edit Vendor Information                                        │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │  Name:        [Equipment Rentals Ltd.               ]              │ │
│  │               👁️ John is viewing this field                        │ │
│  │                                                                    │ │
│  │  Description: [We provide high-quality equipment... ]  ← You      │ │
│  │               ▓▓▓▓▓▓▓▓▓ (cursor blinking)                          │ │
│  │                                                                    │ │
│  │  Email:       [contact@equipmentrentals.com         ]              │ │
│  │                                                                    │ │
│  │  Phone:       [+1 (555) 123-4567                    ]              │ │
│  │                                                                    │ │
│  │                                    [Save Changes] [Cancel]         │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │  💬 Live Activity Feed                                             │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │  Just now - You are typing in description...                      │ │
│  │  2 sec ago - John Smith joined the session                        │ │
│  │  5 sec ago - You started editing description                      │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 📨 Invite Member Modal

```
┌─────────────────────────────────────────────────────────────────────────┐
│                                                                          │
│    ┌────────────────────────────────────────────────────────────────┐  │
│    │  + Invite New Collaborator                                 [X] │  │
│    ├────────────────────────────────────────────────────────────────┤  │
│    │                                                                │  │
│    │  Vendor: Equipment Rentals Ltd.                               │  │
│    │                                                                │  │
│    │  ─────────────────────────────────────────────────────────────│  │
│    │                                                                │  │
│    │  Email Address:                                               │  │
│    │  [                                                          ] │  │
│    │                                                                │  │
│    │  Role:                                                         │  │
│    │  [ Participant  ▼ ]                                           │  │
│    │                                                                │  │
│    │  Options:                                                      │  │
│    │  ☐ Owner        - Full control, can delete vendor            │  │
│    │  ☐ Manager      - Can invite/remove members, edit vendor     │  │
│    │  ☑ Participant  - Can view and edit vendor in sessions       │  │
│    │                                                                │  │
│    │  Personal Message (optional):                                 │  │
│    │  ┌──────────────────────────────────────────────────────────┐ │  │
│    │  │ Hi! I'd like you to collaborate on our vendor profile.  │ │  │
│    │  │                                                          │ │  │
│    │  └──────────────────────────────────────────────────────────┘ │  │
│    │                                                                │  │
│    │                      [Cancel]  [Send Invitation]              │  │
│    │                                                                │  │
│    └────────────────────────────────────────────────────────────────┘  │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 🔔 Real-Time Notifications

```
┌────────────────────────────────────────────────────────────┐
│  🔔 Notifications                                      [X] │
├────────────────────────────────────────────────────────────┤
│                                                            │
│  🔴 New  John Smith joined Equipment Rentals session      │
│         2 minutes ago                            [View →] │
│                                                            │
│  ✏️ New  Jane Doe updated Green Supply Co. description    │
│         1 hour ago                               [View →] │
│                                                            │
│  👥 New  You've been added to Organic Fertilizers Inc.    │
│         3 hours ago                              [View →] │
│                                                            │
│  ─────────────────────────────────────────────────────────│
│                                        [Mark All as Read] │
│                                                            │
└────────────────────────────────────────────────────────────┘
```

---

## 📊 Vendor Analytics Dashboard (Optional)

```
┌─────────────────────────────────────────────────────────────────────────┐
│  📊 Vendor Collaboration Analytics                                      │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐  │
│  │   Total      │ │   Active     │ │   Total      │ │   This Month │  │
│  │   Vendors    │ │   Sessions   │ │   Edits      │ │   Activity   │  │
│  │      12      │ │      3       │ │     247      │ │      89      │  │
│  └──────────────┘ └──────────────┘ └──────────────┘ └──────────────┘  │
│                                                                          │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │  📈 Collaboration Activity (Last 30 Days)                         │  │
│  │                                                                   │  │
│  │      *                                                            │  │
│  │     * *        *                                                  │  │
│  │    *   *      * *       *                                         │  │
│  │   *     *    *   *     * *                                        │  │
│  │  *       *  *     *   *   *                                       │  │
│  │ ──────────────────────────────────────────────────────────────   │  │
│  │  Week 1  Week 2  Week 3  Week 4                                   │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│                                                                          │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │  🏆 Most Active Vendors                                           │  │
│  ├──────────────────────────────────────────────────────────────────┤  │
│  │  1. Equipment Rentals Ltd.        - 89 edits this month          │  │
│  │  2. Green Supply Co.              - 67 edits this month          │  │
│  │  3. Organic Fertilizers Inc.      - 45 edits this month          │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 🎯 Integration Points Summary

### 1. **Navigation Bar Updates**
- Add "Vendors" link to staff navigation
- Add notification bell icon for real-time updates
- Highlight active collaboration sessions

### 2. **Dashboard Enhancements**
- New "Vendor Collaborations" card showing active vendors
- Quick access buttons to join active sessions
- Real-time status indicators

### 3. **New Pages Required**
- `/staff/vendors` - List all vendor collaborations
- `/staff/vendors/{id}` - View vendor details & manage
- `/staff/vendors/{id}/session` - Live collaboration interface
- `/staff/vendors/invitations` - Manage pending invitations

### 4. **Real-Time Features**
- WebSocket connection for live updates
- Presence indicators (who's viewing/editing)
- Live activity feed
- Push notifications for important events

### 5. **API Endpoints Used**
- `GET /api/v1/vendors` - List vendors
- `GET /api/v1/vendors/{id}/collaboration/bootstrap` - Get collab data
- `POST /api/v1/vendors/{id}/session/start` - Start session
- `POST /api/v1/vendors/{id}/session/heartbeat` - Keep alive
- `POST /api/v1/vendors/{id}/invite` - Invite collaborator

---

## 🎨 Color Scheme & Icons

### Status Colors
- 🟢 **Active** - `#28a745` (Green)
- 🟡 **Idle** - `#ffc107` (Yellow)
- 🔴 **Live Session** - `#dc3545` (Red)
- ⚪ **Inactive** - `#6c757d` (Gray)

### Role Icons
- 🔑 **Owner** - Key symbol
- ⚙️ **Manager** - Gear symbol
- 👥 **Participant** - People symbol

### Action Icons
- ✏️ **Edit**
- 👁️ **View**
- 🔔 **Notifications**
- 💬 **Chat/Activity**
- 📊 **Analytics**

---

## 📝 Notes for Implementation

1. **Livewire Components Needed:**
   - VendorList - Display all vendors with filters
   - VendorDetail - Show vendor information
   - CollaboratorManager - Manage team members
   - LiveSession - Real-time editing interface
   - InvitationModal - Send invitations

2. **JavaScript Libraries:**
   - Laravel Echo - WebSocket client
   - Pusher/Soketi - Broadcasting server
   - Alpine.js - Reactive UI components

3. **Database Migrations:**
   - All vendor collaboration tables (already created)
   - Notification preferences table
   - Session history table

4. **Testing Considerations:**
   - Multi-user concurrent editing
   - Conflict resolution
   - Permission enforcement
   - Real-time sync accuracy

---

## 🚀 Next Steps

1. Implement Livewire components
2. Set up Laravel Broadcasting
3. Create API endpoints for session management
4. Build real-time UI with Alpine.js
5. Add notification system
6. Implement permission checks
7. Create comprehensive tests

