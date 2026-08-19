---
name: Clinical Precision
colors:
  surface: '#f7f9fb'
  surface-dim: '#d8dadc'
  surface-bright: '#f7f9fb'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f4f6'
  surface-container: '#eceef0'
  surface-container-high: '#e6e8ea'
  surface-container-highest: '#e0e3e5'
  on-surface: '#191c1e'
  on-surface-variant: '#464554'
  inverse-surface: '#2d3133'
  inverse-on-surface: '#eff1f3'
  outline: '#777586'
  outline-variant: '#c7c4d7'
  surface-tint: '#5148d7'
  primary: '#2a14b4'
  on-primary: '#ffffff'
  primary-container: '#4338ca'
  on-primary-container: '#c1beff'
  inverse-primary: '#c3c0ff'
  secondary: '#615a78'
  on-secondary: '#ffffff'
  secondary-container: '#e4dbfe'
  on-secondary-container: '#655f7c'
  tertiary: '#00414d'
  on-tertiary: '#ffffff'
  tertiary-container: '#005a69'
  on-tertiary-container: '#69d3ed'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#e3dfff'
  primary-fixed-dim: '#c3c0ff'
  on-primary-fixed: '#100069'
  on-primary-fixed-variant: '#372abf'
  secondary-fixed: '#e7deff'
  secondary-fixed-dim: '#cac2e4'
  on-secondary-fixed: '#1d1831'
  on-secondary-fixed-variant: '#49435f'
  tertiary-fixed: '#abedff'
  tertiary-fixed-dim: '#6bd5ee'
  on-tertiary-fixed: '#001f26'
  on-tertiary-fixed-variant: '#004e5c'
  background: '#f7f9fb'
  on-background: '#191c1e'
  surface-variant: '#e0e3e5'
typography:
  display-lg:
    fontFamily: Manrope
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
  headline-md:
    fontFamily: Manrope
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  headline-sm:
    fontFamily: Manrope
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  body-lg:
    fontFamily: Manrope
    fontSize: 16px
    fontWeight: '500'
    lineHeight: 24px
  body-md:
    fontFamily: Manrope
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: Manrope
    fontSize: 13px
    fontWeight: '600'
    lineHeight: 18px
    letterSpacing: 0.02em
  label-sm:
    fontFamily: Manrope
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
  display-lg-mobile:
    fontFamily: Manrope
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  unit: 4px
  margin-page: 2rem
  gutter-grid: 1.5rem
  sidebar-width: 260px
  card-padding: 1.5rem
---

## Brand & Style

This design system is engineered for the high-stakes environment of healthcare administration. The personality is **authoritative, dependable, and efficient**. It prioritizes clarity and speed of data entry over decorative elements.

The visual style is **Corporate / Modern**, utilizing a structured layout with high-contrast functional elements. It employs a "Deep Navy" foundation for navigation to evoke a sense of security and institutional stability, while the "Vibrant Blue" primary actions provide clear, energetic call-to-actions. Whitespace is used strategically to prevent cognitive overload, ensuring that hospital billing staff can navigate complex financial workflows with minimal friction.

## Colors

The palette is anchored by high-contrast tones to distinguish between navigation, content, and action.

*   **Primary (Vibrant Blue):** Used for critical actions and active states. It should stand out clearly against both white and navy backgrounds.
*   **Secondary (Deep Navy):** Reserved for the global sidebar and primary navigation containers. This provides a strong structural frame for the application.
*   **Tertiary (Teal/Cyan):** Used for informational callouts, data summaries, and status indicators that require visibility without the urgency of a primary action.
*   **Neutral (Cool Gray/White):** The workspace uses a layered approach of pure white (#FFFFFF) for cards and a very light gray (#F8FAFC) for the background to define surface depth.

## Typography

The design system uses **Manrope** for its balance of geometric modernism and high legibility. The typeface is optimized for both numerical data and dense interface labels.

*   **Headlines:** Use SemiBold (600) or Bold (700) weights. Primary page titles should be high-contrast against the background.
*   **Body Text:** Primarily uses Medium (500) for better readability on light gray backgrounds.
*   **Labels:** Specifically for table headers and sidebar items, use a slightly smaller font size with increased letter spacing to enhance scanability.

## Layout & Spacing

The layout follows a **Fixed-Fluid Hybrid** model. 
*   **Sidebar:** Fixed at 260px, providing a persistent anchor for navigation.
*   **Main Content:** Fluid container with a max-width of 1440px to ensure line lengths remain readable on ultra-wide monitors.
*   **Grid:** A 12-column system is used for dashboard widgets. Cards should span 4, 6, or 12 columns.
*   **Rhythm:** A 4px baseline grid governs all internal spacing. Margins between major sections are consistently 32px (8 units), while internal card elements use 16px or 24px increments.

## Elevation & Depth

This design system uses **Tonal Layering** supplemented by extremely soft ambient shadows to define hierarchy.

1.  **Level 0 (Floor):** The background (#F8FAFC) serves as the foundation.
2.  **Level 1 (Cards/Surface):** White (#FFFFFF) surfaces with a subtle 1px border (#E2E8F0) and a very soft, diffused shadow (0px 4px 12px rgba(0,0,0,0.03)).
3.  **Level 2 (Active Elements):** Primary buttons and active navigation states use high-saturation color to "pop" from the surface.
4.  **Navigation (Sidebar):** Deep Navy creates a "recessed" or "anchor" feel, visually separated from the workspace by color contrast rather than shadow.

## Shapes

The shape language is **Rounded**, striking a balance between clinical precision and modern approachability.

*   **Buttons & Inputs:** Use a 0.5rem (8px) radius to feel professional yet ergonomic.
*   **Dashboard Cards:** Use 1rem (16px) for large containers to soften the overall layout.
*   **Sidebar Selection:** Active states in the sidebar should use rounded pills or soft-cornered rectangles to highlight the current location.

## Components

### Buttons
*   **Primary:** Solid Vibrant Blue with white text and a leading icon. Use for the main action (e.g., "Buat Kwitansi Baru").
*   **Secondary/Ghost:** Transparent background with Primary-colored border and text.
*   **Danger:** Red-tinted text/icon for Logout or destructive actions.

### Navigation Sidebar
*   **Items:** White or light-gray text on Navy background.
*   **Active State:** A white background with Primary-colored text/icon, utilizing a soft-rounded corner.
*   **Icons:** Simple, 24px stroke-based icons for high legibility.

### Input Fields
*   **Style:** White background with a light 1px border.
*   **Focus State:** 2px border in Vibrant Blue with a soft glow.
*   **Labels:** Always positioned above the field in Label-MD typography.

### Cards & Stats
*   **Stats Cards:** Use high-contrast background colors (like Tertiary Teal) for high-level summaries.
*   **Data Tables:** Clean, borderless rows with subtle dividers and "Label" style headers.

### Navigation Header
*   A clean, white top bar containing the page title, notification bell, and a user profile dropdown (Pill-shaped with light gray background).