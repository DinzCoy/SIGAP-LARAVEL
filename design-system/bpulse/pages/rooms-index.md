# Rooms Index Page Overrides

> **PROJECT:** BPulSe
> **Generated:** 2026-05-06 00:19:10
> **Page Type:** Dashboard / Data View

> ⚠️ **IMPORTANT:** Rules in this file **override** the Master file (`design-system/MASTER.md`).
> Only deviations from the Master are documented here. For all other rules, refer to the Master.

---

## Page-Specific Rules

### Layout Overrides

- **Max Width:** 1400px or full-width
- **Grid:** 12-column grid for data flexibility

### Spacing Overrides

- **Content Density:** High — optimize for information display

### Typography Overrides

- No overrides — use Master typography

### Color Overrides

- No overrides — use Master colors

### Component Overrides

- Avoid: Use arbitrary large z-index values
- Avoid: Load 50MB textures
- Avoid: Expect z-index to work across contexts

---

## Page-Specific Components

- No unique components for this page

---

## Recommendations

- Effects: Hover tooltips, chart zoom on click, row highlighting on hover, smooth filter animations, data loading spinners
- Layout: Define z-index scale system (10 20 30 50)
- Sustainability: Compress and lazy load 3D models
- Layout: Understand what creates new stacking context
