# SIMON Suggestions & Roadmap Feature

## Overview

The SIMON Joomla component now includes a comprehensive suggestions and roadmap system that allows users to submit improvement ideas and administrators to manage them through a structured roadmap.

## Features

### For Users

1. **Submit Suggestions**
   - Access via: `index.php?option=com_simon&view=suggestion&layout=submit`
   - Users must be logged in
   - Form fields:
     - Title (required)
     - Description (required)
     - Category (optional)
     - Priority (optional)

2. **View My Suggestions**
   - Access via: `index.php?option=com_simon&view=suggestion&layout=mysuggestions`
   - Shows all suggestions submitted by the logged-in user
   - Displays status, category, and creation date
   - Shows if suggestion is on roadmap

### For Administrators

1. **Manage Suggestions**
   - Access via: Components → SIMON → Suggestions
   - List all suggestions with filtering options
   - Edit suggestions to update status, assign to roadmap, add admin notes
   - Status options:
     - Submitted
     - Under Review
     - Approved
     - In Progress
     - Testing
     - Completed
     - Rejected
     - On Hold

2. **Manage Roadmap**
   - Access via: Components → SIMON → Roadmap
   - Create roadmap items with:
     - Title
     - Description
     - Year and Quarter
     - Start/End dates
     - Status (Planned, In Progress, Completed, Delayed, Cancelled)
   - Associate suggestions with roadmap items
   - View roadmap with included suggestions

## Database Schema

### Tables Created

1. **`#__simon_suggestions`**
   - Stores user suggestions
   - Fields: id, title, description, category, priority, status, user_id, user_name, user_email, votes, roadmap_id, planned_date, completed_date, admin_notes, created, modified, published

2. **`#__simon_roadmap`**
   - Stores roadmap items
   - Fields: id, title, description, quarter, year, start_date, end_date, status, created, modified, published

3. **`#__simon_roadmap_suggestions`**
   - Junction table linking roadmap items to suggestions
   - Fields: id, roadmap_id, suggestion_id, order

## Status System

### Suggestion Statuses

- **submitted** - Newly submitted by user
- **under_review** - Being reviewed by admin
- **approved** - Approved for implementation
- **in_progress** - Currently being worked on
- **testing** - In testing phase
- **completed** - Fully implemented
- **rejected** - Not approved for implementation
- **on_hold** - Temporarily paused

### Roadmap Statuses

- **planned** - Planned for future
- **in_progress** - Currently in development
- **completed** - Finished
- **delayed** - Behind schedule
- **cancelled** - Cancelled

## Priority Levels

- **low** - Nice to have
- **medium** - Standard priority
- **high** - Important
- **critical** - Urgent

## Categories

- **feature** - New Feature
- **improvement** - Improvement
- **bugfix** - Bug Fix
- **ui** - User Interface
- **api** - API
- **documentation** - Documentation
- **other** - Other

## Usage

### User Workflow

1. User logs in to Joomla site
2. Navigates to suggestion submission page
3. Fills out form with suggestion details
4. Submits suggestion
5. Can view their suggestions and track status

### Admin Workflow

1. Admin reviews submitted suggestions
2. Updates status as suggestion progresses
3. Creates roadmap items for planned work
4. Associates suggestions with roadmap items
5. Updates roadmap status as work progresses
6. Marks suggestions as completed when done

## Installation

The database tables are automatically created when the component is installed. The SQL install script includes:

- Table creation with proper indexes
- Foreign key constraints
- Default values

## Future Enhancements

Potential improvements:
- Voting system for suggestions
- Comments/discussion on suggestions
- Email notifications for status changes
- Public roadmap view
- Suggestion search and filtering for users
- Export roadmap to PDF/CSV

