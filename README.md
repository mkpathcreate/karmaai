# PHP Vedic Horoscope Matcher for Japan

A complete server-side horoscope matching system built in PHP, optimized for Japanese locations.

## Features

✅ **47 Static Prefectures** - No external API needed
✅ **Dynamic City Loading** - Cities load from CSV when prefecture selected  
✅ **Accurate Coordinates** - Averaged from multiple GPS points
✅ **Real Astronomical Calculations** - Moon longitude, Nakshatra, Rashi
✅ **Ashtakoot Compatibility** - Traditional 8-factor matching system
✅ **Dosha Detection** - Manglik and Kala Sarpa dosha analysis
✅ **Mobile Responsive** - Works on all devices
✅ **Apache Ready** - No reverse proxy needed

## Files Structure

```
/
├── index.php              # Main application
├── LocationService.php    # Prefecture/city management & CSV parsing
├── AstrologyService.php   # Horoscope calculations & compatibility
├── api.php               # AJAX endpoints for city loading
├── latest.csv            # Japanese location database
└── README_PHP.md         # This file
```

## Requirements

- **PHP 7.4+** (with standard extensions)
- **Apache/Nginx** web server
- **latest.csv** file in same directory

## Installation

1. **Upload files** to your web directory
2. **Ensure CSV file** `latest.csv` is in the same folder
3. **Set permissions**:
   ```bash
   chmod 644 *.php
   chmod 644 latest.csv
   ```
4. **Access via browser**: `http://yourdomain.com/index.php`

## Usage

1. **Select Prefecture** → Cities auto-load
2. **Fill birth details** for both people
3. **Click Calculate** → Get instant compatibility results

## API Endpoints

### Get Cities for Prefecture
```
GET api.php?action=cities&prefecture=東京都
```
Response:
```json
{
  "success": true,
  "prefecture": "東京都", 
  "cities": ["新宿区", "渋谷区", ...],
  "count": 61
}
```

### Get City Coordinates
```
GET api.php?action=coordinates&prefecture=東京都&city=新宿区
```
Response:
```json
{
  "success": true,
  "coordinates": {"lat": 35.7004, "lon": 139.7183}
}
```

## Testing

The system has been tested with:
- ✅ All 47 Japanese prefectures
- ✅ Thousands of cities/towns
- ✅ Astronomical calculations
- ✅ Compatibility algorithms
- ✅ Dosha detection

## Example Results

**Input:**
- Groom: 1990-03-15, 08:30, 東京都 あきる野市
- Bride: 1992-07-22, 14:15, 東京都 三宅村

**Output:**
- Groom: Anuradha (Vrishchika)
- Bride: Bharani (Mesha) 
- Compatibility: 24/36 (Very Good Match! 💫)
- Doshas: No Manglik, No Kala Sarpa

## Advantages Over Next.js

✅ **No build process** - Just upload and run
✅ **Direct CSV access** - No CORS issues
✅ **Apache native** - No reverse proxy needed
✅ **Server-side processing** - Better for large datasets
✅ **Immediate deployment** - Works instantly

## Browser Support

- Chrome, Firefox, Safari, Edge (modern versions)
- Mobile browsers (iOS Safari, Android Chrome)
- Responsive design for all screen sizes

## Security

- Input validation and sanitization
- SQL injection protection (no database used)
- XSS prevention with htmlspecialchars()
- CSRF protection ready (can be added if needed)

---

**Ready for production!** 🚀