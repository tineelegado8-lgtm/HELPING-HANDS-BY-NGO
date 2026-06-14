# How to Use Your New Design System

## 🎨 For Everyone: Changing the Theme

### The Fastest Way (30 seconds)

1. **Log in to Admin Panel**
2. Click **"🎨 Design Presets"** (new menu item on the left)
3. See 4 beautiful theme options with colors preview
4. Click **"Apply"** on your favorite theme
5. ✅ Done! Your entire website now uses that theme

### That's It!
No code, no files, no complicated steps. Your chosen design is now live on every page.

---

## 🔧 For Admins: Full Customization

### Going Deeper (If You Want)

If the 4 presets aren't enough, customize individual settings:

**Path:** Admin Panel → **Website Builder CMS** → Scroll to **Design & Branding Settings**

#### Customize These Areas:

**Site Identity**
- Site name
- Tagline
- Hero section text

**Images**
- Upload new header logo
- Upload hero image
- Upload QR codes

**Colors** (Use the color picker)
- Primary color (affects buttons, links)
- Secondary color (accents)
- Accent color (call-to-action buttons)
- Background colors
- Text colors

**Typography**
- Choose font from dropdown (Arial, Segoe UI, Roboto, Poppins, Montserrat, Inter)

**Admin Panel Theme**
- Customize only the admin dashboard colors

---

## 👨‍💼 For Volunteers: What You Can Do

If you're a volunteer with admin access, you can:

✅ **Change themes instantly**
- Go to 🎨 Design Presets
- Pick a theme
- Click Apply

✅ **Edit website text**
- Go to Website Builder CMS
- Edit mission, vision, footer text, announcements

✅ **Manage navigation**
- Add/remove menu items
- Reorder navigation
- Show/hide items

✅ **Upload images**
- Logo, hero image, QR codes

❌ **What you (typically) cannot do:**
- Access user management
- View activity logs
- Change user roles
- (Depends on admin permissions)

---

## 🖥️ For Developers: Customization

### Add a New Design Setting

**Step 1:** Add input in `admin/cms.php`
```php
<div class="col-md-4">
    <label class="form-label">My Custom Color</label>
    <input class="form-control form-control-color" type="color" 
           name="setting[my_custom_color]" 
           value="<?= setting_value('my_custom_color', '#000000') ?>">
</div>
```

**Step 2:** Use in CSS (auto-loads in `theme-colors.php`)
```php
:root {
    --my-custom-color: <?= setting_value('my_custom_color', '#000000') ?>;
}
```

**Step 3:** Reference in your styles
```css
.my-element {
    background-color: var(--my-custom-color);
}
```

### Add a New Preset Theme

**Edit:** `includes/layout.php`, function `get_design_preset()`

```php
function get_design_preset(string $preset = 'default'): array
{
    $presets = [
        'default' => [ /* existing */ ],
        // Add new preset:
        'my-theme' => [
            'name' => 'My Awesome Theme',
            'primary_color' => '#12345',
            'secondary_color' => '#67890',
            'accent_color' => '#abcde',
            'background_color' => '#fff',
            'form_background_color' => '#f5f5f5',
            'header_color' => '#fff',
            'header_text_color' => '#333',
            'button_color' => '#12345',
            'button_text_color' => '#fff',
        ],
    ];
    return $presets[$preset] ?? $presets['default'];
}
```

Then add to `get_all_design_presets()`:
```php
'my-theme' => get_design_preset('my-theme'),
```

Now **apply it in admin panel**: Admin → Design Presets → Apply "My Awesome Theme"

---

## 🔌 How It All Connects

### The System Flow

```
┌─────────────────────────────────────────────────┐
│  Admin Panel                                    │
│  → 🎨 Design Presets (quick themes)            │
│  → Website Builder CMS (full customization)    │
└────────────────┬────────────────────────────────┘
                 │ Updates
                 ▼
        ┌────────────────┐
        │  Database      │
        │  settings      │
        │  table         │
        └────────────┬───┘
                     │ Reads
                     ▼
        ┌────────────────────────────────┐
        │  theme-colors.php              │
        │  (Generates dynamic CSS)       │
        └────────────────┬───────────────┘
                         │ Loads
                         ▼
        ┌────────────────────────────────┐
        │  Every Page                    │
        │  - index.php                   │
        │  - about.php                   │
        │  - donation.php                │
        │  - ... all pages use it        │
        └────────────────────────────────┘
```

**Key Point:** Everything flows from the database, so changes are instant and global.

---

## 🆘 Troubleshooting

### Colors Not Changing?
1. Clear browser cache: `Ctrl+Shift+Delete`
2. Verify database connection in `config/database.php`
3. Check that `settings` table exists: `SELECT * FROM settings;`

### Design Not Showing?
1. Verify pages include `includes/header.php`
2. Check `theme-colors.php` is loadable
3. Verify `includes/layout.php` exists
4. Check PHP error logs

### Need to Revert?
1. Go to **Admin Panel → Website Builder CMS**
2. Scroll to **"Restore Default Theme"**
3. Click the button
4. ✅ All colors reset to beautiful defaults

---

## 📋 Checklist: Your Design System Is Ready

- ✅ 4 professional theme presets available
- ✅ Quick theme switcher in admin panel
- ✅ Full customization available in CMS
- ✅ All pages automatically use the design
- ✅ Design is database-driven (portable)
- ✅ Non-technical staff can customize
- ✅ Changes apply instantly
- ✅ Documentation complete

---

## 🎯 Real-World Examples

### Example 1: You Want a Fresh New Look
1. Go to **Admin Panel**
2. Click **🎨 Design Presets**
3. Click **Apply** on "Modern Dark"
4. Your entire site is now dark and modern
5. Time spent: 30 seconds

### Example 2: You Want to Match Your Organization's Brand
1. Go to **Admin Panel → Website Builder CMS**
2. Find **"Primary Color"** input
3. Click the color box
4. Pick your brand color
5. Click **Save**
6. All buttons and links now use your brand color
7. Time spent: 1 minute

### Example 3: You Want to Change the Logo
1. Go to **Admin Panel → Website Builder CMS**
2. Find **"Header Logo"** input
3. Click "Choose File"
4. Upload your new logo
5. Click **Save**
6. New logo appears on every page
7. Time spent: 2 minutes

### Example 4: You're Moving to a New Server
1. Export database (includes all design settings)
2. Copy website files to new server
3. Import database
4. ✅ Your design is instantly there
5. No need to copy design files separately

---

## 🎓 Learning Path

**0-5 minutes:** Read this document
**5-10 minutes:** Try changing a theme (if you have admin access)
**10-15 minutes:** Try customizing a color
**15-30 minutes:** Read `DESIGN_QUICK_START.md` for more tips

**Developers only:**
Read `DESIGN_SYSTEM.md` for technical deep dive

---

## 🎉 You're All Set!

Your design system is ready to use. Start with the quick theme switcher, then explore deeper customization as needed.

**Remember:** It's all point-and-click. No code required.

---

## 📞 Quick Reference

| What You Want | Where to Go |
|---|---|
| Change theme in 30 seconds | Admin Panel → 🎨 Design Presets |
| Change colors | Admin Panel → Website Builder CMS |
| Change logo/images | Admin Panel → Website Builder CMS |
| Change site name | Admin Panel → Website Builder CMS |
| Edit page text | Admin Panel → Website Builder CMS → Content Management |
| Add menu item | Admin Panel → Website Builder CMS → Navigation Management |
| Reset to defaults | Admin Panel → Website Builder CMS → Restore Default Theme |
| Technical help | Read `DESIGN_SYSTEM.md` |

---

**Happy customizing!** 🎨✨
