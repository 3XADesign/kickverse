# KICKVERSE CHECKOUT - COLOR PALETTE & DESIGN TOKENS

## Color System

### Primary Brand Colors
```
Primary Purple:    #b054e9
Primary Hover:     #c151d4  (lighter for hover states)
Primary Dark:      #9243d0  (for pressed states)
Accent Pink:       #f479d9  (for icons, highlights)
Gradient Primary:  linear-gradient(135deg, #b054e9 0%, #ec4899 100%)
```

### Grayscale
```
White/BG:    #ffffff
Gray-50:     #f9fafb  (lightest background)
Gray-100:    #f3f4f6  (light background, card backgrounds)
Gray-200:    #e5e7eb  (borders, dividers, secondary lines)
Gray-300:    #d1d5db  (lighter borders)
Gray-400:    #9ca3af  (disabled text, icons)
Gray-500:    #6b7280  (medium gray)
Gray-600:    #4b5563  (secondary text)
Gray-700:    #374151  (primary text, labels)
Gray-800:    #1f2937  (dark text)
Gray-900:    #111827  (darkest text)
```

### Status & Semantic Colors
```
Success Green:     #10b981  (FREE shipping badge)
Success Dark:      #059669  (hover state)
Error Red:         #ef4444  (error messages)
Error Dark:        #dc2626  (error hover)
Warning Amber:     #f59e0b  (warnings)
```

---

## Component-Specific Colors

### Summary/Card Components
```
Background:        White (#ffffff)
Border Color:      var(--gray-100) #f3f4f6
Border (light):    var(--gray-200) #e5e7eb
Icon Color:        #f479d9 (Pink)
Text Primary:      var(--gray-900) #111827
Text Secondary:    var(--gray-700) #374151
```

### Buttons
```
Primary Button:
  Background:      linear-gradient(135deg, #b054e9, #ec4899)
  Text:            White (#ffffff)
  Hover BG:        linear-gradient(135deg, #c151d4, #f47bb3) or darker shade
  Hover Shadow:    0 4px 12px rgba(176, 84, 233, 0.3)
  Disabled:        opacity: 0.6

Secondary Button:
  Background:      White
  Border:          2px solid var(--primary) #b054e9
  Text:            var(--primary) #b054e9
  Hover BG:        rgba(176, 84, 233, 0.1) or var(--gray-100)
```

### Form Elements
```
Input Background:      White (#ffffff)
Input Border:          var(--gray-300) #d1d5db
Input Border (focus):  var(--primary) #b054e9
Input Focus Shadow:    0 0 0 3px rgba(176, 84, 233, 0.1)
Label Text:            var(--gray-700) #374151
Placeholder:           var(--gray-400) #9ca3af
```

### Badges & Labels
```
Free Shipping Badge:   
  Background:          var(--success) #10b981
  Text:                White (#ffffff)
  Border:              none

Applied Coupon:
  Background:          rgba(16, 185, 129, 0.1) or #d4edda
  Border:              1px solid var(--success) #10b981
  Text:                var(--success) #10b981
```

---

## Accessibility Contrast Ratios

### Recommended Combinations (WCAG AA)
```
Text (Normal) on Gray-900:      Gray-100 (18:1) ✓✓✓
Text (Normal) on Gray-700:      Gray-50  (8:1)  ✓✓
Text (Small) on Gray-700:       Gray-100 (9:1) ✓✓
Primary on White:               #b054e9  (3.1:1) ✓ (Normal), ✗ (Small)
Primary on Gray-100:            #b054e9  (3.5:1) ✓ (Normal), ✓ (Small)
```

### DO NOT USE (Insufficient Contrast)
```
Gray-400 on White (4.5:1) - Fails for normal text
Gray-500 on White (4.8:1) - Fails for normal text, passes for large
Primary on Gray-50 (3:1) - Borderline, use with caution
```

---

## Color Usage by Component

### Header/Title
```
H1 Gradient:        linear-gradient(135deg, #b054e9, #ec4899)
H2/H3 Text:         var(--gray-900) #111827
Subtitle:           var(--gray-600) #4b5563
```

### Summary Card Header
```
Background:         White (#ffffff)
Title:              var(--gray-900) #111827
Icon:               #f479d9 (Pink) - IMPORTANT!
Border-Bottom:      2px solid var(--gray-100) #f3f4f6
```

### Cost Rows
```
Label Text:         var(--gray-700) #374151
Value Text:         var(--gray-900) #111827
Divider Line:       1px solid var(--gray-200) #e5e7eb
```

### Total Amount
```
Label Text:         var(--gray-900) #111827
Total Value:        linear-gradient(135deg, #b054e9, #ec4899)
Background Clip:    text (for gradient text effect)
Border-Top:         2px solid var(--gray-200) #e5e7eb
```

### Coupon Section
```
Applied Box BG:     rgba(16, 185, 129, 0.1)
Applied Box Border: 1px solid var(--success) #10b981
Input Border:       var(--gray-300) #d1d5db
Input Focus:        var(--primary) #b054e9
Button BG:          var(--primary) #b054e9
Button Text:        White (#ffffff)
```

---

## Shadow & Elevation System

### Card Shadows
```
Default:            0 2px 8px rgba(0, 0, 0, 0.08)
On Hover:           0 4px 12px rgba(176, 84, 233, 0.3) or rgba(0, 0, 0, 0.1)
Sticky/Elevated:    0 4px 16px rgba(0, 0, 0, 0.12)
Minimal:            0 1px 2px rgba(0, 0, 0, 0.05)
```

### Interactive Elements
```
Button Hover:       0 4px 12px rgba(176, 84, 233, 0.3)
Input Focus:        0 0 0 3px rgba(176, 84, 233, 0.1)
Sticky Header:      0 2px 8px rgba(0, 0, 0, 0.08)
```

---

## Gradient Definitions

### Primary Gradient (MOST USED)
```css
linear-gradient(135deg, #b054e9 0%, #ec4899 100%)
```
Usage: Page titles, buttons, total amounts, accents

### Button Gradient
```css
linear-gradient(135deg, #b054e9 0%, #ec4899 100%)
```
Same as primary, applied to button backgrounds

### Page Background Gradient
```css
linear-gradient(135deg, #f9fafb 0%, #ffffff 100%)
```
Light gradient for checkout page background

### Text Gradient (with -webkit-background-clip)
```css
background: linear-gradient(135deg, #b054e9, #ec4899);
-webkit-background-clip: text;
-webkit-text-fill-color: transparent;
background-clip: text;
```
For gradient text on headings and amounts

---

## CSS Variable Usage Examples

### Best Practice - Always Use Variables
```css
/* GOOD */
color: var(--gray-900);
border: 1px solid var(--gray-200);
background: var(--primary);

/* BAD - Hardcoded */
color: #111827;
border: 1px solid #e5e7eb;
background: #b054e9;
```

### Complete Card Example
```css
.checkout-card {
  background: white;
  border: 2px solid var(--gray-100);
  border-radius: var(--radius-lg);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  padding: var(--space-6);
}

.checkout-card-title {
  color: var(--gray-900);
  font-size: 1.125rem;
  font-weight: 700;
  margin-bottom: var(--space-5);
  padding-bottom: var(--space-4);
  border-bottom: 2px solid var(--gray-100);
}

.checkout-card-title i {
  color: #f479d9;
  font-size: 1.25rem;
  margin-right: var(--space-2);
}

.cost-row {
  display: flex;
  justify-content: space-between;
  padding: var(--space-3) 0;
  border-bottom: 1px solid var(--gray-200);
  color: var(--gray-700);
}

.cost-value {
  color: var(--gray-900);
  font-weight: 600;
}

.cost-total-value {
  font-size: 1.5rem;
  font-weight: 700;
  background: linear-gradient(135deg, #b054e9, #ec4899);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
```

---

## Icon Colors

### Primary Icons (Interactive)
```
Color:     #b054e9
Hover:     #c151d4
Active:    #9243d0
```

### Accent Icons (Highlights)
```
Color:     #f479d9 (Pink)
Used in:   Summary titles, section icons
Should be: Vibrant and distinct from primary
```

### Success Icons
```
Color:     #10b981 (Green)
Used in:   Checkmarks, free shipping badge
```

### Error Icons
```
Color:     #ef4444 (Red)
Used in:   Error messages, validation
```

### Neutral Icons
```
Color:     var(--gray-700) #374151
Used in:   Generic icons, secondary actions
```

---

## Dark Mode (Future - Not Implemented)

If dark mode is added, consider:
```
Dark Background:    #1a1a1a or #121212
Card Background:    #2a2a2a
Text Primary:       #ffffff
Text Secondary:     #b3b3b3
Border Color:       #404040
Primary Purple:     #a855f7 (lighter for dark)
Accent Pink:        #f472b6 (lighter for dark)
```

---

## Color Combinations to Avoid

```
✗ Gray-400 on White (insufficient contrast)
✗ Gray-500 on Light Gray (too subtle)
✗ Primary on Gray-50 (borderline contrast)
✗ Dark Gray on Dark Background (no contrast)
✗ Pink on Primary Purple (too similar)
```

---

## Hex Color Quick Reference

Copy-paste values:
```
#b054e9  - Primary Purple
#f479d9  - Accent Pink
#c151d4  - Purple Hover
#9243d0  - Purple Dark
#10b981  - Success Green
#ef4444  - Error Red
#f3f4f6  - Gray-100
#e5e7eb  - Gray-200
#111827  - Gray-900
#374151  - Gray-700
```

---

## Testing Accessibility

Use tools to verify:
- WebAIM Contrast Checker
- Stark (Figma plugin)
- Axe DevTools (Browser extension)
- Windows High Contrast Mode
- Color blindness simulators

Minimum Requirements:
- Normal text: 4.5:1 ratio
- Large text (18pt+): 3:1 ratio
- Graphics/UI: 3:1 ratio

---

Generated: November 7, 2025
