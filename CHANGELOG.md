# Version 4.0.2 (95)
- Fixed missing icon on notifications for tickets made from mobile version
- FAQ feature (user-end) is now available on the Oxwall Mobile version
- FAQ menu item is added to the bottom of the Oxwall user men
- Minor bug fixes

# Version 4.0.1 (90)
- Fixed missing icon on all notifications
- Hotfix for Support manager page returning 500 error
- Fixed missing language keys for Manual Purge and Date
- Added 'Updated' column to Support Manager ticket list
- Added checks to Department User functions;
    - Users can only belong to 1 department at a time. If you try to add them to multiple, you will receive an error
    - If a user with the Manager role has not been assigned to a Department yet, they will be denied access to Support Manager

# Version 4.0.0 (85)
- New feature F.A.Q:
    - Admin can set questions/answers in the new Admin-end page FAQ
    - Admin can edit existing questions/answers or delete them
    - In Settings, you must have F.A.Q on for users to access the page
    - F.A.Qs show up at /support/faq
- Added option for Ticket Info Position:
    - Static Above displays the ticket info as a static block before the ticket (for sidebar themes)
    - Or Side (Default) for the standard floating sidebar
- Added option for Purging:
    - Admin can now setup Auto-Purge in the settings
    - This can delete tickets without any activity starting from 1 week, 3 months, or 6 months
    - A manual purge is available as well, in case cron does not work
    - Dates are starting points. E.g; setting to one week will delete all tickets that have no been updated for a week or more.
- Admin Settings page has been re-designed for a more streamlined use
- Added most user-end styles to /css/supportcenter.css for easier style editing
- Bug fixes:
    - Navigation menus are now correctly highlighted
    - Erronous/missing text strings fixed
    - Fixed a bug where users viewing the Support Manager would see all of the tickets
        - This now only shows tickets in that user's department
    - Minor bug fixes
    - Minor style fixes/adjustments
    