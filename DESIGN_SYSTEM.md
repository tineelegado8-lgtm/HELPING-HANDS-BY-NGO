# Design System & Default UI Documentation

## Overview

Your NGO website now has a **unified, database-driven design system** that is:
- ✅ **Default**: Built into every page automatically
- ✅ **Portable**: Copy any file and the design persists  
- ✅ **Editable**: Admin & volunteers can change the design without touching code
- ✅ **Theme-Based**: 4 ready-made color presets + full customization

---

## How It Works

### The Design Is Database-Driven
All design settings (colors, fonts, images, logos) are stored in the database, not hardcoded in files. This means:
- The design travels with your data
- No design files need to be copied separately
- Changes apply instantly site-wide

### Every Page Uses the Same Design
All pages (index.php, about.php, donation.php, etc.) include a common header and footer from the `includes/` folder:

```php
<?php
require_once __DIR__ . '/includes/functions.php';
page_title('Page Title');
require __DIR__ . '/includes/header.php';
?>
<!-- Your page content -->
<?php require __DIR__ . '/includes/footer.php'; ?>
```

The header and footer automatically pull all design settings from the database, so **the design is truly portable**.

---

## Admin Controls

### 1. Design Presets (Quick Theme Switching)
**Location:** Admin Panel → 🎨 Design Presets

Choose from 4 ready-made themes:
- **Blue & Green (Default)** - Professional, community-focused
- **Modern Dark** - Sophisticated, contemporary
- **Warm Orange** - Friendly, energetic  
- **Ocean Blue** - Calm, trusted

Click "Apply" to instantly switch themes across the entire site.

### 2. Full Design Customization
**Location:** Admin Panel → Website Builder CMS → Design & Branding Settings

Customize:
- **Site Identity**: Name, tagline, hero section text
- **Logo & Images**: Header logo, hero image, QR codes
- **Colors**: Primary, secondary, accent, background, header, buttons
- **Typography**: Font family for entire site
- **Admin Dashboard**: Theme colors for the admin panel
- **Page-Specific**: Events page background override

### 3. Content Editing
**Location:** Admin Panel → Website Builder CMS → Content Management

Edit text blocks:
- Mission statement
- Vision statement
- Footer text
- Announcements

---

## What Makes This System Special

### 1. True Portability
If you export your database and copy the website files to another server:
```
Database (with all design settings) + Website Files = Works instantly
```

You don't need separate design files or assets to travel with your code.

### 2. Editable Without Code
Volunteers and staff can change:
- Colors (with color picker)
- Logo and images (with file upload)
- Text content (with text editor)
- Theme (with one-click presets)

All without touching any PHP or CSS code.

### 3. Instant Global Changes
Change the primary color in the admin panel → all buttons, links, and accents update instantly across the entire website.

---

## File Structure for Portable Design

```
includes/
├── header.php       ← Pulls design settings and renders navbar
├── footer.php       ← Renders footer with settings
├── layout.php       ← Helper functions for design system
└── functions.php    ← Includes layout.php at the end

assets/css/
├── style.css           ← Base styling
└── theme-colors.php    ← Dynamic colors from database

admin/
├── cms.php          ← Full design editor
└── design.php       ← Quick preset switcher

All public pages (*.php)
├── index.php
├── about.php
├── donation.php
└── ... (all use includes/header.php and footer.php)
```

---

## For Volunteers

### What Volunteers Can Edit
Navigate to **Admin Panel** and:
1. **Design Presets** - Change the theme instantly
2. **Website Builder CMS** - Update content and colors

### What Volunteers Cannot Edit
(If permissions are configured):
- Database structure
- Source code
- User accounts and roles
- Activity logs

---

## Default Settings

If no settings are in the database, these defaults apply:

### Colors
```
Primary: #1769aa (Blue)
Secondary: #16865a (Green)
Accent: #f28c28 (Orange)
Background: #ffffff (White)
```

### Typography
```
Font: Arial, Helvetica, sans-serif
```

### Site Identity
```
Site Name: Helping Hands NGO
Tagline: (APP_SLOGAN)
```

---

## Database Tables for Design

### `settings` Table
Stores all design configuration:
```sql
SELECT * FROM settings WHERE setting_key LIKE '%color%';
```

Keys include:
- `primary_color`
- `secondary_color`
- `accent_color`
- `background_color`
- `form_background_color`
- `header_color`
- `header_text_color`
- `button_color`
- `button_text_color`
- `font_family`
- `site_logo`
- `hero_image`
- `admin_background_color`
- `admin_sidebar_color`
- ... and more

### `content_blocks` Table
Stores editable text content:
```sql
SELECT * FROM content_blocks;
```

---

## Resetting to Default

If you want to reset the design to the original:

**Via Admin Panel:**
1. Go to **Website Builder CMS**
2. Scroll to **"Restore Default Theme"**
3. Click **"Reset Default Colors"**

**Via Database:**
```sql
UPDATE settings SET setting_value = '#1769aa' WHERE setting_key = 'primary_color';
UPDATE settings SET setting_value = '#16865a' WHERE setting_key = 'secondary_color';
-- etc.
```

---

## Troubleshooting

### Design Not Showing Up?
1. Check that `includes/header.php` is included in your page
2. Verify the database has the `settings` table
3. Check file permissions on `assets/css/theme-colors.php`

### Changes Not Applying?
1. Clear browser cache (Ctrl+Shift+Delete)
2. Verify the database connection
3. Check that `setting_value()` function is working

### Need to Revert to a Previous Design?
- All design changes are stored in the database
- Use a database backup to restore previous settings
- Or manually reset colors from the admin panel

---

## Advanced: Adding Custom Design Settings

To add a new design setting:

1. **In Admin CMS**, add an input field:
   ```php
   <input type="color" name="setting[custom_color]" value="<?= setting_value('custom_color', '#000000') ?>">
   ```

2. **In CSS (theme-colors.php or style.css)**:
   ```css
   :root {
       --custom-color: <?= setting_value('custom_color', '#000000') ?>;
   }
   ```

3. **Use in your styles**:
   ```css
   .element {
       color: var(--custom-color);
   }
   ```

---

## Key Principles

| Principle | Benefit |
|-----------|---------|
| **Database-First** | Design travels with data |
| **Single Source of Truth** | No conflicting design files |
| **Instant Global Changes** | Update once, changes everywhere |
| **No Code Required** | Non-technical staff can edit |
| **Preset Themes** | Quick customization for new installations |
| **Fully Customizable** | Or keep the beautiful defaults |

---

## Support

If you need to:
- **Add a new design setting** → Edit `includes/layout.php` and `admin/cms.php`
- **Change default colors** → Modify `includes/layout.php` in the `get_design_preset()` function
- **Add a new preset** → Add to `get_all_design_presets()` in `includes/layout.php`
- **Create a custom page** → Follow the template pattern with header/footer includes

---

**Enjoy your portable, editable, default design system!** 🎨
