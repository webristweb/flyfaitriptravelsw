# Admin Panel - Contact Inquiries Management

## Features ✨

- **Secure Login System** - Simple authentication to protect admin area
- **Bootstrap 4 DataTables** - Professional data table with advanced features
- **Search Functionality** - Search across all columns instantly
- **Pagination** - Customizable entries per page (10, 25, 50, 100, All)
- **Responsive Design** - Works perfectly on mobile, tablet, and desktop
- **Sorting** - Click on any column header to sort
- **CSV Data Integration** - Reads all contact inquiries from CSV files
- **Beautiful UI** - Modern gradient design with smooth animations

## Access Details 🔐

**URL:** `http://yourwebsite.com/admin/`

**Default Login Credentials:**
- Username: `admin`
- Password: `admin123`

⚠️ **IMPORTANT:** Change the default password in `admin/index.php` (line 7-8)

## How It Works 🔧

1. **Login Page** - Secure authentication before accessing dashboard
2. **Dashboard** - Shows total inquiries count and data table
3. **DataTable Features:**
   - Search box to filter inquiries
   - Sort by clicking column headers
   - Pagination controls
   - Responsive view for mobile devices
   - Show 10/25/50/100 or all entries
   - Email and phone are clickable links

## Data Source 📊

The admin panel reads data from CSV files located in:
```
/inquiries/contacts_YYYY-MM.csv
```

All CSV files are automatically loaded and displayed in the table, sorted by date (newest first).

## Customization 🎨

### Change Login Credentials
Edit `admin/index.php` lines 7-8:
```php
$admin_username = "your_username";
$admin_password = "your_secure_password";
```

### Change Colors
Edit the CSS in `admin/index.php` to customize:
- Gradient colors: `#667eea` and `#764ba2`
- Button colors
- Table styling

## Security Notes 🔒

1. **Change default password immediately**
2. Consider using password hashing (password_hash/password_verify)
3. Add HTTPS to your website
4. Limit admin access by IP if possible
5. Add session timeout for auto-logout

## Browser Support 🌐

- Chrome (Latest)
- Firefox (Latest)
- Safari (Latest)
- Edge (Latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Dependencies 📦

All dependencies are loaded via CDN:
- Bootstrap 4.6.2
- jQuery 3.6.0
- DataTables 1.13.7
- Font Awesome 6.5.1

No installation required!

## Troubleshooting 🔍

**Problem:** Can't login
- Check username and password in `admin/index.php`
- Clear browser cookies/cache

**Problem:** No data showing
- Check if CSV files exist in `/inquiries/` folder
- Verify CSV file format matches expected structure

**Problem:** Table not loading
- Check browser console for JavaScript errors
- Ensure internet connection (CDN dependencies)

## Support 💬

For any issues or customization requests, contact the development team.

---

**Created for FlyFaiTrip Travel Agency**
